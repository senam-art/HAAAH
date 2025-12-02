<?php
session_start();
require_once '../settings/db_class.php';

if (!isset($_GET['payment_id'])) {
    header("Location: homepage.php");
    exit();
}

$payment_id = intval($_GET['payment_id']);
$user_id = $_SESSION['user_id'] ?? 0;

// Fetch Payment & Event Details
// Security: Ensure the logged-in user owns this payment
$conn = new mysqli(SERVER, USERNAME, PASSWD, DATABASE);
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

$date_formatted = date('F j, Y', strtotime($invoice['paid_at']));
$time_formatted = date('h:i A', strtotime($invoice['paid_at']));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?php echo $invoice['payment_id']; ?> - Haaah Sports</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #0f0f13; color: white; }
        @media print {
            body { background-color: white; color: black; }
            .no-print { display: none; }
            .print-border { border: 1px solid #ccc; }
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-lg bg-[#1a1a23] rounded-2xl border border-white/10 shadow-2xl p-8 print-border">
        
        <!-- Header -->
        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-transparent bg-clip-text bg-gradient-to-r from-[#3dff92] to-[#00c6ff] print:text-black">
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
        </div>

        <!-- Details -->
        <div class="space-y-4 border-t border-white/10 pt-6 mb-8">
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
                <span class="text-sm font-bold text-white print:text-black"><?php echo $invoice['title']; ?></span>
            </div>
        </div>

        <!-- Amount -->
        <div class="bg-black/30 p-4 rounded-xl flex justify-between items-center mb-8 print:bg-gray-100">
            <span class="text-gray-400 text-sm">Total Amount Paid</span>
            <span class="text-2xl font-black text-[#3dff92] print:text-black">
                <?php echo $invoice['currency'] . ' ' . number_format($invoice['amount'], 2); ?>
            </span>
        </div>

        <!-- Actions -->
        <div class="grid grid-cols-2 gap-4 no-print">
            <button onclick="window.print()" class="flex items-center justify-center gap-2 py-3 bg-white/5 hover:bg-white/10 text-white font-bold rounded-xl transition-colors">
                <i data-lucide="printer" size="18"></i> Print / Save
            </button>
            <a href="event-profile.php?id=<?php echo $invoice['event_id']; ?>" class="flex items-center justify-center gap-2 py-3 bg-[#3dff92] text-black font-bold rounded-xl hover:bg-[#2fe080] transition-colors">
                Go to Lobby <i data-lucide="arrow-right" size="18"></i>
            </a>
        </div>

    </div>

    <script>lucide.createIcons();</script>
</body>
</html>