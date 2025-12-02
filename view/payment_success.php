<?php
session_start();
if (!isset($_GET['event_id'])) {
    header("Location: homepage.php");
    exit();
}
$event_id = intval($_GET['event_id']);
$msg = isset($_GET['msg']) ? $_GET['msg'] : 'success';

$title = ($msg === 'published') ? "Event Published!" : "You're In!";
$message = ($msg === 'published') 
    ? "Your event has been created and is waiting for admin approval." 
    : "Your spot is secured. Get ready to play!";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - Haaah Sports</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap'); body { font-family: 'Inter', sans-serif; background-color: #0f0f13; color: white; }</style>
    <script>
        // Auto-redirect after 5 seconds
        setTimeout(function() {
            window.location.href = "event-profile.php?id=<?php echo $event_id; ?>";
        }, 5000);
    </script>
</head>
<body class="flex items-center justify-center min-h-screen selection:bg-[#3dff92] selection:text-black">

    <div class="text-center max-w-md p-8 bg-[#1a1a23] rounded-2xl border border-white/10 shadow-2xl animate-fade-in">
        <div class="w-20 h-20 bg-green-500/20 text-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
            <i data-lucide="check-circle" class="w-10 h-10"></i>
        </div>
        
        <h1 class="text-3xl font-black mb-2 text-white"><?php echo $title; ?></h1>
        <p class="text-gray-400 mb-8"><?php echo $message; ?></p>
        
        <a href="event-profile.php?id=<?php echo $event_id; ?>" class="block w-full py-3 bg-[#3dff92] text-black font-bold rounded-xl hover:bg-[#2fe080] transition-transform hover:scale-105">
            Go to Match Lobby
        </a>
        
        <p class="text-xs text-gray-500 mt-4">Redirecting automatically in 5 seconds...</p>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>