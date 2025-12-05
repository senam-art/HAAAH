<?php
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../settings/db_class.php';

if (!isset($_GET['payment_id'])) {
    header("Location: homepage.php");
    exit();
}

$payment_id = intval($_GET['payment_id']);
$user_id = $_SESSION['user_id'] ?? 0;

// Fetch Payment & Event Details
$conn = new mysqli(SERVER, USERNAME, PASSWD, DATABASE);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure we fetch everything (p.* includes payment_type)
$sql = "SELECT p.*, e.title, e.event_date, e.event_time, v.name as venue_name, u.first_name, u.last_name, u.email 
        FROM payments p
        JOIN events e ON p.event_id = e.event_id
        JOIN venues v ON e.venue_id = v.venue_id
        JOIN users u ON p.user_id = u.id
        WHERE p.payment_id = ? AND p.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $payment_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$invoice = $result->fetch_assoc();

if (!$invoice) {
    die("Invoice not found or access denied.");
}

// Handle Timestamp (use payment_date if available, else fallback to paid_at)
$ts = isset($invoice['payment_date']) ? strtotime($invoice['payment_date']) : (isset($invoice['paid_at']) ? strtotime($invoice['paid_at']) : time());
$date_formatted = date('F j, Y', $ts); 
$time_formatted = date('h:i A', $ts);

// --- LOGIC: Determine Label based on payment_type ---
$payment_label = "Player Match Fee"; // Default fallback
$label_color = "text-brand-accent"; // Greenish for players

if (isset($invoice['payment_type'])) {
    if ($invoice['payment_type'] === 'organizer_fee') {
        $payment_label = "Organizer Publishing Fee";
        $label_color = "text-brand-purple"; // Purple for organizers
    } elseif ($invoice['payment_type'] === 'booking') {
        $payment_label = "Player Match Fee";
        $label_color = "text-brand-accent";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?php echo $invoice['payment_id']; ?> - Haaah Sports</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { dark: '#0f0f13', card: '#1a1a23', accent: '#3dff92', purple: '#7000ff' }
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #0f0f13; color: white; }
        @media print {
            body { background-color: white; color: black; }
            .no-print { display: none; }
            .print-border { border: 1px solid #ccc; }
            .text-white { color: black !important; }
            .text-gray-400 { color: #666 !important; }
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-lg bg-brand-card rounded-2xl border border-white/10 shadow-2xl p-8 print-border">
        
        <!-- Header -->
        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-transparent bg-clip-text bg-gradient-to-r from-brand-accent to-brand-purple print:text-black">
                    HAAAH<span class="text-white text-sm font-normal tracking-widest ml-1 print:text-black">SPORTS</span>
                </h1>
                <p class="text-xs text-gray-500 mt-1">Official Receipt</p>
            </div>
            <div class="text-right">
                <div class="text-xs text-gray-500">INVOICE #</div>
                <div class="font-mono font-bold text-white print:text-black"><?php echo str_pad($invoice['payment_id'], 6, '0', STR_PAD_LEFT); ?></div>
            </div>
        </div>

        <!-- Success Badge -->
        <div class="text-center mb-8 no-print">
            <div class="w-16 h-16 bg-green-500/20 text-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="check" class="w-8 h-8"></i>
            </div>
            <h2 class="text-xl font-bold text-white">Payment Successful</h2>
            
            <!-- Specific Warning for Organizers Pending Approval -->
            <?php if(isset($_GET['status']) && $_GET['status'] === 'pending_approval'): ?>
                <div class="mt-4 p-3 bg-yellow-500/10 border border-yellow-500/20 rounded-xl text-center">
                    <p class="text-sm font-bold text-yellow-500">Event Pending Admin Approval</p>
                    <p class="text-xs text-gray-400 mt-1">Your event will be live once approved.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Details -->
        <div class="space-y-4 border-t border-white/10 pt-6 mb-8">
            <!-- NEW ROW: Payment Type -->
            <div class="flex justify-between">
                <span class="text-sm text-gray-400">Transaction Type</span>
                <span class="text-sm font-black uppercase tracking-wide <?php echo $label_color; ?>">
                    <?php echo $payment_label; ?>
                </span>
            </div>
            
            <div class="flex justify-between">
                <span class="text-sm text-gray-400">Transaction Ref</span>
                <span class="text-sm font-mono text-white print:text-black"><?php echo $invoice['reference']; ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-400">Date Paid</span>
                <span class="text-sm text-white print:text-black"><?php echo $date_formatted . ' ' . $time_formatted; ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-400">Billed To</span>
                <span class="text-sm text-white print:text-black"><?php echo $invoice['first_name'] . ' ' . $invoice['last_name']; ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-400">Event</span>
                <span class="text-sm font-bold text-white print:text-black text-right"><?php echo $invoice['title']; ?></span>
            </div>
        </div>

        <!-- Amount -->
        <div class="bg-black/30 p-4 rounded-xl flex justify-between items-center mb-8 print:bg-gray-100">
            <span class="text-gray-400 text-sm">Total Amount Paid</span>
            <span class="text-2xl font-black text-brand-accent print:text-black">
                <?php echo $invoice['currency'] . ' ' . number_format($invoice['amount'], 2); ?>
            </span>
        </div>

        <!-- Actions -->
        <div class="grid grid-cols-2 gap-4 no-print">
            <button onclick="window.print()" class="flex items-center justify-center gap-2 py-3 bg-white/5 hover:bg-white/10 text-white font-bold rounded-xl transition-colors">
                <i data-lucide="printer" size="18"></i> Print / Save
            </button>
            <a href="event-profile.php?id=<?php echo $invoice['event_id']; ?>" class="flex items-center justify-center gap-2 py-3 bg-brand-accent text-black font-bold rounded-xl hover:bg-[#2fe080] transition-colors">
                Go to Lobby <i data-lucide="arrow-right" size="18"></i>
            </a>
        </div>

    </div>

    <script>lucide.createIcons();</script>
</body>
</html>