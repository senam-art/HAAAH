<?php
// Add these lines to see the real error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// view/checkout.php
session_start();

// 1. Bootstrap Core (Standardizes paths & DB connection)
require_once __DIR__ . '/../settings/core.php'; 

// 2. Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?msg=login_to_pay");
    exit();
}

// 3. Load Payment Controller
require_once PROJECT_ROOT . '/controllers/payment_controller.php';
// Include Event Controller for player checks
require_once PROJECT_ROOT . '/controllers/guest_controller.php';

// 4. Get Input
$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;
$type = isset($_GET['type']) ? $_GET['type'] : 'join_game';

// 5. Get Processed Data from Controller
$details = get_checkout_details_ctr($event_id, $type);

if (!$details) {
    header("Location: index.php?error=invalid_event");
    exit();
}

$event = $details['event'];
$user_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : 'guest@haaah.com';
$user_id = $_SESSION['user_id'];

// 6. Prevent Double Booking / Payments
if ($type === 'organizer_fee') {
    // If event is not 'awaiting_payment', it's already processed
    if ($event['status'] !== 'awaiting_payment') {
        header("Location: event-profile.php?id=$event_id&msg=already_published");
        exit();
    }
} else {
    // Check if player is already in squad
    $current_players = get_event_players_ctr($event_id);
    if (is_array($current_players)) {
        foreach ($current_players as $player) {
            if (isset($player['id']) && $player['id'] == $user_id) {
                header("Location: event-profile.php?id=$event_id&msg=already_joined");
                exit();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($details['page_title']); ?> - Haaah Sports</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Paystack Inline -->
    <script src="https://js.paystack.co/v1/inline.js"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { brand: { dark: '#0f0f13', card: '#1a1a23', accent: '#3dff92', purple: '#7000ff' } },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
        
        // Pass Public Key to JS (Replace with your actual Public Key)
        window.PAYSTACK_PUBLIC_KEY = 'pk_test_62bcb1bc82f3445af1255aa8a8f0f1e7446f7936'; 
    </script>
    <style>@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap'); body { font-family: 'Inter', sans-serif; background-color: #0f0f13; color: white; }</style>
</head>
<body class="selection:bg-brand-accent selection:text-black pb-20">

    <nav class="border-b border-white/5 bg-brand-card px-6 py-4 flex justify-between items-center">
        <a href="event-profile.php?id=<?php echo $event_id; ?>" class="flex items-center gap-2 text-gray-400 hover:text-white">
            <i data-lucide="arrow-left" size="20"></i> Cancel
        </a>
        <div class="flex items-center gap-2 text-sm text-gray-400">
            <i data-lucide="lock" size="14"></i> Secure Transaction
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            
            <!-- LEFT: Order Summary -->
            <div class="space-y-6">
                <div>
                    <h2 class="text-2xl font-bold mb-2"><?php echo htmlspecialchars($details['page_title']); ?></h2>
                    <p class="text-gray-400 text-sm">Review your details before paying.</p>
                </div>

                <div class="bg-brand-card rounded-2xl p-6 border border-white/5 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-20 bg-brand-accent/5 blur-[60px] rounded-full"></div>
                    <div class="relative z-10">
                        <!-- Event Info -->
                        <div class="flex items-start justify-between mb-6">
                            <div>
                                <h3 class="font-bold text-lg text-white"><?php echo htmlspecialchars($event['title']); ?></h3>
                                <div class="flex items-center gap-2 text-sm text-gray-400 mt-1">
                                    <i data-lucide="map-pin" size="14"></i> <?php echo htmlspecialchars($event['venue_name'] ?? 'Unknown Venue'); ?>
                                </div>
                            </div>
                            <div class="bg-white/5 p-3 rounded-xl">
                                <i data-lucide="<?php echo ($type === 'organizer_fee') ? 'shield' : 'ticket'; ?>" class="text-brand-accent"></i>
                            </div>
                        </div>

                        <div class="border-t border-white/10 my-4"></div>

                        <!-- Item Details -->
                        <div class="flex justify-between items-center mb-2">
                            <div>
                                <div class="font-bold text-sm text-white"><?php echo htmlspecialchars($details['item_name']); ?></div>
                                <div class="text-xs text-gray-500"><?php echo htmlspecialchars($details['item_desc']); ?></div>
                            </div>
                            <div class="font-mono font-bold">GHS <?php echo number_format($details['amount_to_pay'], 2); ?></div>
                        </div>

                        <div class="border-t border-white/10 my-4"></div>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Total Due</span>
                            <span class="text-2xl font-black text-brand-accent">GHS <?php echo number_format($details['amount_to_pay'], 2); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-center gap-4 opacity-50">
                    <span class="text-xs font-bold text-gray-500">SECURED BY</span>
                    <span class="font-black text-lg tracking-tighter">Paystack</span>
                </div>
            </div>

            <!-- RIGHT: Payment Method -->
            <div class="bg-brand-card rounded-2xl p-8 border border-white/5 h-fit">
                <form id="paymentForm">
                    
                    <!-- DATA CARRIERS: Hidden inputs for JS to read -->
                    <input type="hidden" id="p_amount" value="<?php echo $details['amount_to_pay']; ?>">
                    <input type="hidden" id="p_email" value="<?php echo htmlspecialchars($user_email); ?>">
                    <input type="hidden" id="p_event_id" value="<?php echo $event_id; ?>">
                    <input type="hidden" id="p_type" value="<?php echo htmlspecialchars($type); ?>">
                    <input type="hidden" id="p_ref" value="<?php echo htmlspecialchars($details['tx_ref']); ?>">
                    <input type="hidden" id="p_title" value="<?php echo htmlspecialchars($event['title']); ?>">

                    <h3 class="font-bold mb-6">Select Payment Method</h3>
                    
                    <div class="space-y-3 mb-8">
                        <label class="flex items-center justify-between p-4 bg-brand-accent/10 border border-brand-accent rounded-xl cursor-pointer transition-all">
                            <div class="flex items-center gap-3">
                                <div class="w-5 h-5 rounded-full border-2 border-brand-accent flex items-center justify-center">
                                    <div class="w-2.5 h-2.5 bg-brand-accent rounded-full"></div>
                                </div>
                                <span class="font-bold text-sm text-brand-accent">Mobile Money / Card</span>
                            </div>
                            <i data-lucide="credit-card" size="20" class="text-brand-accent"></i>
                            <input type="radio" name="payment_method" value="paystack" checked class="hidden">
                        </label>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email Address</label>
                            <input type="email" value="<?php echo htmlspecialchars($user_email); ?>" readonly class="w-full bg-brand-dark border border-white/10 rounded-lg p-3 text-sm text-gray-400 focus:outline-none cursor-not-allowed">
                        </div>
                    </div>

                    <button type="submit" id="payBtn" class="w-full mt-8 py-4 bg-brand-accent hover:bg-[#2fe080] text-black font-bold rounded-xl transition-transform hover:scale-[1.02] shadow-lg shadow-brand-accent/20 flex items-center justify-center gap-2">
                        <?php echo $details['button_text']; ?>
                    </button>
                    
                    <p class="text-center text-[10px] text-gray-500 mt-4 flex items-center justify-center gap-1">
                        <i data-lucide="lock" size="10"></i> Transactions are encrypted and secure.
                    </p>
                </form>
            </div>

        </div>
    </div>

    <!-- External JS Logic -->
    <script src="../js/checkout.js"></script>
    <script>lucide.createIcons();</script>
</body>
</html>