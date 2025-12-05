<?php
require_once __DIR__ . '/../settings/core.php';

$error_msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : 'Transaction failed.';
$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed - Haaah Sports</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap'); body { font-family: 'Inter', sans-serif; background-color: #0f0f13; color: white; }</style>
</head>
<body class="flex items-center justify-center min-h-screen selection:bg-red-500 selection:text-white">

    <div class="text-center max-w-md p-8 bg-[#1a1a23] rounded-2xl border border-red-500/30 shadow-2xl">
        <div class="w-20 h-20 bg-red-500/10 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6">
            <i data-lucide="x-circle" class="w-10 h-10"></i>
        </div>
        
        <h1 class="text-3xl font-black mb-2 text-white">Payment Failed</h1>
        <p class="text-gray-400 mb-8">
            We couldn't complete your transaction.<br>
            <span class="text-red-400 text-sm font-mono mt-2 block"><?php echo $error_msg; ?></span>
        </p>
        
        <div class="space-y-3">
            <?php if($event_id > 0): ?>
                <a href="checkout.php?event_id=<?php echo $event_id; ?>" class="block w-full py-3 bg-white text-black font-bold rounded-xl hover:bg-gray-200 transition-transform hover:scale-105">
                    Try Again
                </a>
            <?php endif; ?>
            
            <a href="index.php" class="block w-full py-3 bg-white/5 text-gray-400 font-bold rounded-xl hover:bg-white/10 transition-colors">
                Back to Home
            </a>
        </div>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>