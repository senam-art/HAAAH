<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Haaah Sports - Find Games Near You</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
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
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap'); 
        body { font-family: 'Inter', sans-serif; background-color: #0f0f13; color: white; }
        .animate-fade-in { animation: fadeIn 0.5s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="selection:bg-brand-accent selection:text-black">

    <!-- Navbar -->
    <nav class="sticky top-0 z-40 px-6 py-4 flex items-center justify-between bg-brand-dark/90 backdrop-blur-md border-b border-white/5">
        <a href="index.php" class="text-2xl font-black tracking-tighter text-white">
            HAAAH<span class="text-brand-accent text-base font-normal tracking-widest ml-1">SPORTS</span>
        </a>

        <div class="flex items-center gap-3">
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="create_event.php" class="px-5 py-2 bg-brand-accent hover:bg-[#2fe080] text-black font-bold rounded-full text-sm transition-transform hover:scale-105 flex items-center gap-2">
                    <i data-lucide="plus" size="16"></i> Host Match
                </a>
                <a href="../actions/logout.php" class="p-2 text-gray-400 hover:text-white"><i data-lucide="log-out" size="20"></i></a>
            <?php else: ?>
                <a href="login.php" class="text-sm font-bold text-white hover:text-brand-accent mr-2">Log In</a>
                <a href="signup.php" class="px-5 py-2 bg-white text-black font-bold rounded-full text-sm hover:bg-gray-200 transition-colors">Sign Up</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="p-4 lg:p-8 max-w-7xl mx-auto space-y-10">
        
        <!-- Hero Section -->
        <section class="relative rounded-3xl overflow-hidden bg-brand-card border border-white/5 min-h-[300px] flex items-center">
            <div class="absolute inset-0">
                <!-- Using the same stadium image logic as landing page -->
                <img src="https://images.unsplash.com/photo-1517466787929-bc90951d64b8?auto=format&fit=crop&q=80&w=1600" class="w-full h-full object-cover opacity-30 mix-blend-overlay">
                <div class="absolute inset-0 bg-gradient-to-r from-brand-dark via-brand-dark/80 to-transparent"></div>
            </div>
            <div class="relative z-10 px-8 py-12 w-full">
                <h2 class="text-4xl lg:text-5xl font-black mb-4 leading-tight">
                    Find Your <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-accent to-[#00c6ff]">Squad.</span>
                </h2>
                
                <!-- Search Bar -->
                <div class="flex flex-col sm:flex-row gap-3 max-w-lg mt-8">
                    <div class="relative flex-1">
                        <i data-lucide="search" class="absolute left-3 top-3.5 text-gray-500" size="18"></i>
                        <input type="text" id="search-input" placeholder="Search by location (e.g. Osu)..." class="w-full bg-white/10 border border-white/10 rounded-xl py-3 pl-10 text-sm text-white placeholder-gray-400 focus:outline-none focus:border-brand-accent focus:bg-brand-dark transition-all">
                    </div>
                    <button id="search-btn" class="px-6 py-3 bg-brand-accent text-black font-bold rounded-xl hover:bg-[#2fe080] transition-colors">
                        Find Games
                    </button>
                </div>
            </div>
        </section>

        <!-- Events Grid -->
        <div>
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold flex items-center gap-2">
                    <i data-lucide="flame" class="text-orange-500"></i> Open Games
                </h3>
            </div>

            <!-- Dynamic Container for JS -->
            <div id="games-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Javascript will load content here -->
            </div>
        </div>
    </div>

    <script src="../js/find_game.js"></script>
</body>
</html>