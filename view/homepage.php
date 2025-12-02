<?php
require_once '../settings/core.php';

// 1. CAPTURE DATA FROM LANDING PAGE
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$lat = isset($_GET['lat']) ? floatval($_GET['lat']) : '';
$lng = isset($_GET['lng']) ? floatval($_GET['lng']) : '';

// 2. DYNAMIC HEADER LOGIC
$games_header = "Open Games";
if (!empty($search_term)) {
    if ($search_term === 'Current Location') {
        $games_header = "Open Games Near You";
    } else {
        // e.g. "Open Games in East Legon"
        $games_header = "Open Games in " . htmlspecialchars($search_term);
    }
}
?>
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
                    colors: {
                        brand: {
                            dark: '#0f0f13',
                            card: '#1a1a23',
                            accent: '#3dff92',
                            purple: '#7000ff'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #0f0f13; color: white; }
        .hidden-force { display: none !important; }
        .animate-fade-in { animation: fadeIn 0.5s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="selection:bg-brand-accent selection:text-black">

    <div id="mobile-overlay" class="fixed inset-0 z-40 bg-black/80 backdrop-blur-sm hidden lg:hidden" onclick="toggleSidebar()"></div>

    <!-- Sidebar Navigation -->
    <aside id="sidebar" class="fixed top-0 left-0 z-50 h-full w-72 bg-brand-card border-r border-white/5 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col">
        <div class="p-6 flex items-center justify-between">
            <h1 class="text-3xl font-black tracking-tighter text-transparent bg-clip-text bg-gradient-to-r from-brand-accent to-[#00c6ff]">
                HAAAH<span class="text-white text-base font-normal tracking-widest ml-1">SPORTS</span>
            </h1>
            <button onclick="toggleSidebar()" class="lg:hidden text-gray-400 hover:text-white">
                <i data-lucide="x"></i>
            </button>
        </div>

        <!-- GUEST NAVIGATION -->
        <nav id="nav-guest" class="flex-1 px-4 py-4 space-y-2 overflow-y-auto">
            <div class="px-4 text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Discover</div>
            <a href="index.php" class="flex items-center gap-3 w-full px-4 py-3 rounded-xl bg-brand-accent text-black font-bold shadow-lg shadow-brand-accent/20">
                <i data-lucide="home" size="20"></i> <span class="text-sm">Browse Games</span>
            </a>
            <a href="login.php" class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white transition-all group">
                <i data-lucide="trophy" class="group-hover:text-brand-accent"></i> <span class="text-sm">Tournaments</span>
            </a>
            <a href="venue-portal.html" class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white transition-all group">
                <i data-lucide="map-pin" class="group-hover:text-brand-accent"></i> <span class="text-sm">Find Venues</span>
            </a>
        </nav>

        <!-- USER NAVIGATION -->
        <nav id="nav-user" class="hidden-force flex-1 px-4 py-4 space-y-2 overflow-y-auto">
            <div class="px-4 text-xs font-bold text-brand-purple uppercase tracking-wider mb-2">My Account</div>
            <a href="dashboard.html" class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white transition-all group">
                <i data-lucide="layout-dashboard" class="text-brand-purple"></i> <span class="text-sm font-bold text-white">Dashboard</span>
            </a>
            <a href="#" class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white transition-all group">
                <i data-lucide="wallet" class="group-hover:text-brand-accent"></i> <span class="text-sm">Wallet</span>
            </a>
        </nav>

        <div id="footer-user" class="hidden-force p-4 border-t border-white/5 space-y-2">
            <button onclick="logout()" class="w-full flex items-center gap-3 px-4 py-2 text-red-400 hover:bg-red-500/10 rounded-lg transition-colors text-sm font-bold">
                <i data-lucide="log-out" size="18"></i> <span>Log Out</span>
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="lg:ml-72 min-h-screen relative">
        
        <header id="main-header" class="sticky top-0 z-40 px-4 py-4 lg:px-8 flex items-center justify-between transition-all duration-300 bg-transparent">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="lg:hidden text-white p-2 rounded-lg hover:bg-white/10"><i data-lucide="menu"></i></button>
                <div class="hidden md:flex items-center gap-2 text-sm text-gray-400">
                    <span class="w-2 h-2 rounded-full bg-brand-accent animate-pulse"></span> Live: 1,240 Players active
                </div>
            </div>

            <!-- GUEST HEADER ACTIONS -->
            <div id="header-guest" class="flex items-center gap-3">
                <a href="login.php" class="hidden sm:flex items-center gap-2 px-6 py-2 bg-transparent text-white font-bold hover:text-brand-accent transition-colors text-sm">Log In</a>
                <a href="signup.php" class="px-5 py-2 bg-white text-black font-bold rounded-full text-sm hover:bg-gray-200 transition-colors">Sign Up</a>
            </div>

            <!-- USER HEADER ACTIONS -->
            <div id="header-user" class="hidden-force flex items-center gap-4">
                <a href="create-event.php" class="hidden sm:flex items-center gap-2 px-4 py-2 bg-brand-accent text-black font-bold rounded-full text-sm hover:bg-[#2fe080]">
                    <i data-lucide="plus" size="16"></i> Create
                </a>
                <a href="profile.html" class="w-9 h-9 rounded-full bg-purple-600 flex items-center justify-center font-bold text-sm border-2 border-brand-card hover:border-brand-accent transition-colors">JM</a>
            </div>
        </header>

        <div class="p-4 lg:p-8 space-y-10">
            
            <!-- Hero Section -->
            <section class="relative rounded-3xl overflow-hidden bg-brand-card border border-white/5 min-h-[250px] flex items-center">
                <div class="absolute inset-0">
                    <img src="https://images.unsplash.com/photo-1517466787929-bc90951d64b8?auto=format&fit=crop&q=80&w=1600" class="w-full h-full object-cover opacity-30 mix-blend-overlay">
                    <div class="absolute inset-0 bg-gradient-to-r from-brand-dark via-brand-dark/80 to-transparent"></div>
                </div>

                <div class="relative z-10 px-6 lg:px-12 py-8 w-full">
                    <h2 class="text-3xl lg:text-4xl font-black mb-4 leading-tight">
                        Find Your <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-accent to-[#00c6ff]">Squad.</span>
                    </h2>
                    
                    <!-- Search Bar (Pre-filled via PHP) -->
                    <div class="flex flex-col sm:flex-row gap-3 max-w-lg mt-6">
                        <div class="relative flex-1">
                            <i data-lucide="search" class="absolute left-3 top-3.5 text-gray-500" size="18"></i>
                            <input type="text" id="search-input" 
                                   value="<?php echo htmlspecialchars($search_term); ?>"
                                   placeholder="Search by location (e.g. Osu)..." 
                                   class="w-full bg-white/10 border border-white/10 rounded-xl py-3 pl-10 text-sm text-white placeholder-gray-400 focus:outline-none focus:border-brand-accent focus:bg-brand-dark transition-all">
                        </div>
                        <button id="search-btn" class="px-6 py-3 bg-brand-accent text-black font-bold rounded-xl hover:bg-[#2fe080] transition-colors">
                            Find Games
                        </button>
                    </div>
                </div>
            </section>

            <!-- MAIN GRID: Left (Games) + Right (Widgets) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Left Column: Open Games List -->
                <div class="lg:col-span-2">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold flex items-center gap-2">
                            <i data-lucide="flame" class="text-orange-500"></i> <?php echo $games_header; ?>
                        </h3>
                        <div class="flex gap-2">
                            <button class="px-3 py-1 bg-white/5 rounded-lg text-xs font-bold hover:bg-white/10">All</button>
                            <button class="px-3 py-1 bg-white/5 rounded-lg text-xs font-bold hover:bg-white/10 text-gray-400">5-a-side</button>
                        </div>
                    </div>

                    <!-- Games Container (JS loads content here) -->
                    <div id="games-container" class="space-y-4">
                        <!-- Loading State (JS will replace this) -->
                        <div class="p-12 text-center text-gray-500">Loading games...</div>
                    </div>
                </div>

                <!-- Right Column: Sidebar Widgets -->
                <div class="space-y-6">
                    
                    <!-- Login Prompt (Shown only to Guests) -->
                    <div id="widget-guest-login" class="bg-gradient-to-br from-brand-card to-[#20202a] border border-white/10 rounded-2xl p-6 text-center">
                        <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-4 text-white">
                            <i data-lucide="user" size="24"></i>
                        </div>
                        <h3 class="font-bold text-lg mb-2">Track Your Stats</h3>
                        <p class="text-sm text-gray-400 mb-4">Sign in to build your player card and get rated by the community.</p>
                        <a href="login.php" class="block w-full py-2 bg-white/10 border border-white/10 rounded-lg text-sm font-bold hover:bg-white/20 transition-colors">Log In / Sign Up</a>
                    </div>

                    <!-- Top Venues -->
                    <div class="bg-[#16161c] border border-white/5 rounded-xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold">Popular Pitches</h3>
                            <a href="venue-portal.html" class="text-xs text-brand-accent hover:underline">List yours</a>
                        </div>
                        <div class="space-y-4">
                            <!-- Example Static Venues (Ideally fetch from DB) -->
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-lg bg-white/5 flex items-center justify-center text-gray-500 font-bold text-xs">01</div>
                                <div class="flex-1">
                                    <div class="font-bold text-sm">McDan Park</div>
                                    <div class="text-xs text-gray-500">La, Accra</div>
                                </div>
                                <div class="flex items-center gap-1 bg-yellow-500/10 px-2 py-1 rounded text-yellow-500 text-xs font-bold">4.8 <i data-lucide="star" size="10" fill="currentColor"></i></div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-lg bg-white/5 flex items-center justify-center text-gray-500 font-bold text-xs">02</div>
                                <div class="flex-1">
                                    <div class="font-bold text-sm">Ajax Park</div>
                                    <div class="text-xs text-gray-500">Legon</div>
                                </div>
                                <div class="flex items-center gap-1 bg-yellow-500/10 px-2 py-1 rounded text-yellow-500 text-xs font-bold">4.5 <i data-lucide="star" size="10" fill="currentColor"></i></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- NEWS SECTION -->
            <div class="pt-8 border-t border-white/5">
                <h3 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <i data-lucide="newspaper" class="text-brand-accent"></i> Trending in Community
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- News Item 1 -->
                    <div class="group relative aspect-video rounded-xl overflow-hidden cursor-pointer border border-white/5">
                        <img src="https://images.unsplash.com/photo-1517927033932-b3d18e61fb3a?auto=format&fit=crop&q=80&w=800" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-[10px] font-bold uppercase tracking-wider bg-brand-accent text-black px-2 py-0.5 rounded-sm">Community</span>
                                <span class="text-xs text-gray-300">3 min read</span>
                            </div>
                            <h4 class="text-lg font-bold leading-tight group-hover:underline decoration-brand-accent decoration-2 underline-offset-4 text-white">
                                Haaah Sports Pilots New Venue Booking System in Accra
                            </h4>
                        </div>
                    </div>
                    <!-- News Item 2 -->
                    <div class="group relative aspect-video rounded-xl overflow-hidden cursor-pointer border border-white/5">
                        <img src="https://images.unsplash.com/photo-1529900748604-07564a03e7a6?auto=format&fit=crop&q=80&w=800" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-[10px] font-bold uppercase tracking-wider bg-brand-accent text-black px-2 py-0.5 rounded-sm">Development</span>
                                <span class="text-xs text-gray-300">5 min read</span>
                            </div>
                            <h4 class="text-lg font-bold leading-tight group-hover:underline decoration-brand-accent decoration-2 underline-offset-4 text-white">
                                Rising Stars: How Local Leagues are Feeding the National Teams
                            </h4>
                        </div>
                    </div>
                    <!-- News Item 3 -->
                    <div class="group relative aspect-video rounded-xl overflow-hidden cursor-pointer border border-white/5">
                        <img src="https://images.unsplash.com/photo-1431324155629-1a6deb1dec8d?auto=format&fit=crop&q=80&w=800" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-[10px] font-bold uppercase tracking-wider bg-brand-accent text-black px-2 py-0.5 rounded-sm">Investment</span>
                                <span class="text-xs text-gray-300">4 min read</span>
                            </div>
                            <h4 class="text-lg font-bold leading-tight group-hover:underline decoration-brand-accent decoration-2 underline-offset-4 text-white">
                                Vision 2026: Scaling Grassroots Football Across 10 Regions
                            </h4>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </main>

    <script>
        lucide.createIcons();

        // 2. PASS PHP DATA TO JS
        const INITIAL_SEARCH = "<?php echo htmlspecialchars($search_term); ?>";
        const INITIAL_LAT = "<?php echo htmlspecialchars($lat); ?>";
        const INITIAL_LNG = "<?php echo htmlspecialchars($lng); ?>";

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('-translate-x-full');
            document.getElementById('mobile-overlay').classList.toggle('hidden');
        }

        function updateUI() {
            const isLoggedIn = <?php echo json_encode(isset($_SESSION['user_id'])); ?>; // Simple session check
            const guestElements = ['nav-guest', 'header-guest', 'widget-guest-login'];
            const userElements = ['nav-user', 'footer-user', 'header-user'];

            if (isLoggedIn) {
                guestElements.forEach(id => document.getElementById(id)?.classList.add('hidden-force'));
                userElements.forEach(id => document.getElementById(id)?.classList.remove('hidden-force'));
            } else {
                userElements.forEach(id => document.getElementById(id)?.classList.add('hidden-force'));
                guestElements.forEach(id => document.getElementById(id)?.classList.remove('hidden-force'));
            }
        }
        updateUI();

        // Logout function
        window.logout = function() {
            window.location.href = '../actions/logout.php';
        }

        // Header Scroll
        const header = document.getElementById('main-header');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 20) {
                header.classList.add('bg-brand-dark/90', 'backdrop-blur-md', 'border-b', 'border-white/5');
                header.classList.remove('bg-transparent');
            } else {
                header.classList.remove('bg-brand-dark/90', 'backdrop-blur-md', 'border-b', 'border-white/5');
                header.classList.add('bg-transparent');
            }
        });
    </script>

    <!-- 3. LOAD GAMES SCRIPT -->
    <script src="../js/find_game.js"></script>
</body>
</html>