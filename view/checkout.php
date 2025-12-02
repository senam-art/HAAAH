<?php
session_start();

// 1. SECURITY: Enforce Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?msg=login_to_pay");
    exit();
}

// 2. Include Logic
require_once __DIR__ . '/../controllers/payment_controller.php';

// ... rest of your checkout code ...
$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;
$type = isset($_GET['type']) ? $_GET['type'] : 'join_game';

$details = get_checkout_details_ctr($event_id, $type);

if (!$details) {
    header("Location: index.php?error=invalid_event");
    exit();
}

$event = $details['event'];
$user_email = $_SESSION['user_email'] ?? 'user@haaah.com';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $details['page_title']; ?> - Haaah Sports</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
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
            <div class="space-y-6">
                <div>
                    <h2 class="text-2xl font-bold mb-2"><?php echo $details['page_title']; ?></h2>
                    <p class="text-gray-400 text-sm">Review your details before paying.</p>
                </div>
                <div class="bg-brand-card rounded-2xl p-6 border border-white/5 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-20 bg-brand-accent/5 blur-[60px] rounded-full"></div>
                    <div class="relative z-10">
                        <div class="flex items-start justify-between mb-6">
                            <div>
                                <h3 class="font-bold text-lg text-white"><?php echo htmlspecialchars($event['title']); ?></h3>
                                <div class="flex items-center gap-2 text-sm text-gray-400 mt-1">
                                    <i data-lucide="map-pin" size="14"></i> <?php echo htmlspecialchars($event['venue_name']); ?>
                                </div>
                            </div>
                            <div class="bg-white/5 p-3 rounded-xl">
                                <i data-lucide="<?php echo ($type === 'organizer_fee') ? 'shield' : 'ticket'; ?>" class="text-brand-accent"></i>
                            </div>
                        </div>
                        <div class="border-t border-white/10 my-4"></div>
                        <div class="flex justify-between items-center mb-2">
                            <div>
                                <div class="font-bold text-sm text-white"><?php echo $details['item_name']; ?></div>
                                <div class="text-xs text-gray-500"><?php echo $details['item_desc']; ?></div>
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
            </div>

            <div class="bg-brand-card rounded-2xl p-8 border border-white/5 h-fit">
                <form id="paymentForm" onsubmit="handlePayment(event)">
                    <input type="hidden" name="amount" value="<?php echo $details['amount_to_pay']; ?>">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($user_email); ?>">
                    <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
                    <input type="hidden" name="payment_type" value="<?php echo $type; ?>">
                    <input type="hidden" name="tx_ref" value="<?php echo $details['tx_ref']; ?>">

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

                    <button type="submit" id="payBtn" class="w-full mt-8 py-4 bg-brand-accent hover:bg-[#2fe080] text-black font-bold rounded-xl transition-transform hover:scale-[1.02] shadow-lg shadow-brand-accent/20 flex items-center justify-center gap-2">
                        <?php echo $details['button_text']; ?>
                    </button>
                    <p class="text-center text-[10px] text-gray-500 mt-4 flex items-center justify-center gap-1">
                        <i data-lucide="lock" size="10"></i> Secured by Paystack
                    </p>
                </form>
            </div>
        </div>
    </div>
    <script src="../js/checkout.js"></script>
</body>
</html>