<?php
require_once '../settings/core.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Haaah Sports - Find Games Near You</title>
    
    <!-- Tailwind CSS (Styling) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Lucide Icons (For the icons) -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Custom Config for Brand Colors -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            dark: '#0f0f13',
                            card: '#1a1a23',
                            accent: '#3dff92', // Neon Green
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
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f0f13;
            color: white;
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #0f0f13; 
        }
        ::-webkit-scrollbar-thumb {
            background: #333; 
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #555; 
        }

        /* Utility for hiding/showing based on state */
        .hidden-force {
            display: none !important;
        }
    </style>
</head>
<body class="selection:bg-brand-accent selection:text-black">

    <!-- Mobile Overlay -->
    <div id="mobile-overlay" class="fixed inset-0 z-40 bg-black/80 backdrop-blur-sm hidden lg:hidden" onclick="toggleSidebar()"></div>

    <!-- Sidebar Navigation -->
    <aside id="sidebar" class="fixed top-0 left-0 z-50 h-full w-72 bg-brand-card border-r border-white/5 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col">
        <!-- Logo -->
        <div class="p-6 flex items-center justify-between">
            <h1 class="text-3xl font-black tracking-tighter text-transparent bg-clip-text bg-gradient-to-r from-brand-accent to-[#00c6ff]">
                HAAAH<span class="text-white text-base font-normal tracking-widest ml-1">SPORTS</span>
            </h1>
            <button onclick="toggleSidebar()" class="lg:hidden text-gray-400 hover:text-white">
                <i data-lucide="x"></i>
            </button>
        </div>

        <!-- GUEST NAVIGATION (Default) -->
        <nav id="nav-guest" class="flex-1 px-4 py-4 space-y-2 overflow-y-auto">
            <div class="px-4 text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Discover</div>
            
            <a href="#" class="flex items-center gap-3 w-full px-4 py-3 rounded-xl bg-brand-accent text-black font-bold shadow-lg shadow-brand-accent/20">
                <i data-lucide="home" size="20"></i>
                <span class="text-sm">Browse Games</span>
            </a>
            
            <a href="game-details.html" class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white transition-all group">
                <i data-lucide="trophy" class="group-hover:text-brand-accent"></i>
                <span class="text-sm">Tournaments</span>
            </a>

            <a href="venue-portal.html" class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white transition-all group">
                <i data-lucide="map-pin" class="group-hover:text-brand-accent"></i>
                <span class="text-sm">Find Venues</span>
            </a>

            <!-- Teaser for Guests -->
            <div class="pt-6 pb-2 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">
                Member Features
            </div>

            <a href="login.php" class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-gray-500 border border-dashed border-white/10 hover:border-brand-accent hover:text-brand-accent transition-all group opacity-70">
                <i data-lucide="layout-dashboard" size="18"></i>
                <span class="text-sm">My Dashboard</span>
            </a>
            
            <a href="login.php" class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-gray-500 border border-dashed border-white/10 hover:border-brand-accent hover:text-brand-accent transition-all group opacity-70">
                <i data-lucide="wallet" size="18"></i>
                <span class="text-sm">Wallet & Earnings</span>
            </a>
        </nav>

        <!-- USER NAVIGATION (Hidden by default) -->
        <nav id="nav-user" class="hidden-force flex-1 px-4 py-4 space-y-2 overflow-y-auto">
            <div class="px-4 text-xs font-bold text-brand-purple uppercase tracking-wider mb-2">My Account</div>
            
            <a href="dashboard.html" class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white transition-all group">
                <i data-lucide="layout-dashboard" class="text-brand-purple"></i>
                <span class="text-sm font-bold text-white">Dashboard</span>
            </a>

            <a href="#" class="flex items-center justify-between w-full px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white transition-all group">
                <div class="flex items-center gap-3">
                    <i data-lucide="calendar-check" class="group-hover:text-brand-accent"></i>
                    <span class="text-sm">My Games</span>
                </div>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-brand-accent/20 text-brand-accent">2</span>
            </a>

            <a href="#" class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white transition-all group">
                <i data-lucide="users" class="group-hover:text-brand-accent"></i>
                <span class="text-sm">My Teams</span>
            </a>

            <a href="#" class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white transition-all group">
                <i data-lucide="wallet" class="group-hover:text-brand-accent"></i>
                <span class="text-sm">Wallet (GHS 45)</span>
            </a>

            <div class="pt-6 pb-2 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">
                Explore
            </div>

            <a href="#" class="flex items-center gap-3 w-full px-4 py-3 rounded-xl bg-white/5 text-white font-bold">
                <i data-lucide="home" size="20"></i>
                <span class="text-sm">Browse Games</span>
            </a>
        </nav>

        <!-- Sidebar Footer (Guest) -->
        <div id="footer-guest" class="p-4 border-t border-white/5">
            <div class="bg-gradient-to-br from-[#380036] to-[#690060] p-4 rounded-xl relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-2 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i data-lucide="users" size="64"></i>
                </div>
                <h3 class="font-bold text-white mb-1">Join the Squad</h3>
                <p class="text-xs text-purple-200 mb-3">Track stats and find local games.</p>
                <a href="sign_up.php" class="block text-center text-xs font-bold bg-white text-purple-900 px-3 py-2 rounded-lg w-full hover:bg-purple-50 transition-colors">
                    Sign Up Free
                </a>
            </div>
        </div>

        <!-- Sidebar Footer (User) -->
        <div id="footer-user" class="hidden-force p-4 border-t border-white/5 space-y-2">
            <a href="profile.html" class="flex items-center gap-3 p-2 hover:bg-white/5 rounded-xl transition-colors">
                <div class="w-10 h-10 rounded-full bg-purple-600 flex items-center justify-center font-bold">JM</div>
                <div>
                    <div class="text-sm font-bold">John Mensah</div>
                    <div class="text-xs text-brand-accent">View Profile</div>
                </div>
            </a>
            <button onclick="logout()" class="w-full flex items-center gap-3 px-4 py-2 text-red-400 hover:bg-red-500/10 rounded-lg transition-colors text-sm font-bold">
                <i data-lucide="log-out" size="18"></i>
                <span>Log Out</span>
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="lg:ml-72 min-h-screen relative">
        
        <!-- Header -->
        <header id="main-header" class="sticky top-0 z-40 px-4 py-4 lg:px-8 flex items-center justify-between transition-all duration-300 bg-transparent">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="lg:hidden text-white p-2 rounded-lg hover:bg-white/10">
                    <i data-lucide="menu"></i>
                </button>
                <div class="hidden md:flex items-center gap-2 text-sm text-gray-400">
                    <span class="w-2 h-2 rounded-full bg-brand-accent animate-pulse"></span>
                    Live: 1,240 Players active in Ghana right now
                </div>
            </div>

            <!-- GUEST HEADER ACTIONS -->
            <div id="header-guest" class="flex items-center gap-3">
                <a href="login.php" class="hidden sm:flex items-center gap-2 px-6 py-2 bg-transparent text-white font-bold hover:text-brand-accent transition-colors text-sm">
                    Log In
                </a>
                <a href="sign_up.php" class="px-5 py-2 bg-white text-black font-bold rounded-full text-sm hover:bg-gray-200 transition-colors">
                    Sign Up
                </a>
                <a href="create-event.php" class="px-5 py-2 bg-brand-accent hover:bg-[#2fe080] text-black font-bold rounded-full text-sm transition-transform hover:scale-105 flex items-center gap-2">
                    <i data-lucide="plus" size="16"></i> Host Match
                </a>
            </div>

            <!-- USER HEADER ACTIONS (Hidden by default) -->
            <div id="header-user" class="hidden-force flex items-center gap-4">
                <button class="p-2 text-gray-400 hover:text-white relative">
                    <i data-lucide="bell" size="20"></i>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>
                <a href="create-event.html" class="hidden sm:flex items-center gap-2 px-4 py-2 bg-brand-accent text-black font-bold rounded-full text-sm hover:bg-[#2fe080]">
                    <i data-lucide="plus" size="16"></i> Create
                </a>
                <a href="profile.html" class="w-9 h-9 rounded-full bg-purple-600 flex items-center justify-center font-bold text-sm border-2 border-brand-card hover:border-brand-accent transition-colors">
                    JM
                </a>
            </div>
        </header>

        <div class="p-4 lg:p-8 space-y-10">
            
            <!-- Hero Section (Welcome Guest) -->
            <section id="hero-guest" class="relative rounded-3xl overflow-hidden bg-brand-card border border-white/5 min-h-[350px] flex items-center">
                <div class="absolute inset-0">
                    <img src="https://images.unsplash.com/photo-1517466787929-bc90951d64b8?auto=format&fit=crop&q=80&w=1600" alt="Ghana Football" class="w-full h-full object-cover opacity-30 mix-blend-overlay">
                    <div class="absolute inset-0 bg-gradient-to-r from-brand-dark via-brand-dark/80 to-transparent"></div>
                </div>

                <div class="relative z-10 max-w-2xl px-6 lg:px-12 py-12">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-brand-purple/20 text-brand-purple text-xs font-bold uppercase tracking-wider rounded-full mb-4 border border-brand-purple/20">
                        <i data-lucide="map-pin" size="12"></i> Available in Accra & Kumasi
                    </div>
                    <h2 class="text-4xl lg:text-5xl font-black mb-4 leading-tight">
                        No Team? <br />
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-accent to-[#00c6ff]">No Problem.</span>
                    </h2>
                    <p class="text-gray-300 text-lg mb-8 max-w-lg leading-relaxed">
                        Join open games in your neighborhood. We handle the venue, the bibs, and the logistics. You just show up and play.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-3 max-w-md">
                        <div class="relative flex-1">
                            <i data-lucide="search" class="absolute left-3 top-3.5 text-gray-500" size="18"></i>
                            <input type="text" placeholder="Search location..." class="w-full bg-white/10 border border-white/10 rounded-xl py-3 pl-10 text-sm text-white placeholder-gray-400 focus:outline-none focus:border-brand-accent">
                        </div>
                        <button class="px-6 py-3 bg-brand-accent text-black font-bold rounded-xl hover:bg-[#2fe080]">
                            Find Games
                        </button>
                    </div>
                </div>
            </section>

             <!-- Hero Section (Logged In User) -->
             <section id="hero-user" class="hidden-force relative rounded-3xl overflow-hidden bg-gradient-to-r from-[#1a1a23] to-[#252530] border border-white/5 p-8 flex flex-col md:flex-row items-center justify-between gap-8">
                <div>
                    <h2 class="text-2xl font-bold mb-2">Welcome back, John! 👋</h2>
                    <p class="text-gray-400 mb-6">You have 1 upcoming game this week.</p>
                    
                    <div class="flex items-center gap-4 bg-black/20 p-4 rounded-xl border border-white/5 max-w-md cursor-pointer hover:border-brand-accent/50 transition-colors" onclick="window.location.href='game-details.html'">
                        <div class="bg-brand-accent/10 p-3 rounded-lg text-brand-accent">
                            <i data-lucide="calendar" size="24"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-white">Tuesday Night Ballers</h4>
                            <p class="text-sm text-gray-400">Tue, 19:00 • McDan Park</p>
                        </div>
                        <div class="ml-auto">
                            <i data-lucide="chevron-right" class="text-gray-500"></i>
                        </div>
                    </div>
                </div>
                <!-- Mini Stats -->
                <div class="grid grid-cols-2 gap-4 w-full md:w-auto">
                    <div class="bg-black/20 p-4 rounded-xl text-center border border-white/5">
                        <div class="text-2xl font-black text-brand-accent">24</div>
                        <div class="text-[10px] text-gray-500 uppercase font-bold">Matches</div>
                    </div>
                    <div class="bg-black/20 p-4 rounded-xl text-center border border-white/5">
                        <div class="text-2xl font-black text-purple-400">4.8</div>
                        <div class="text-[10px] text-gray-500 uppercase font-bold">Rating</div>
                    </div>
                </div>
            </section>

            <!-- Main Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Left Column: Open Games List -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold flex items-center gap-2">
                            <i data-lucide="flame" class="text-orange-500"></i>
                            Open Games Near You
                        </h3>
                        <div class="flex gap-2">
                            <button class="px-3 py-1 bg-white/5 rounded-lg text-xs font-bold hover:bg-white/10">All</button>
                            <button class="px-3 py-1 bg-white/5 rounded-lg text-xs font-bold hover:bg-white/10 text-gray-400">5-a-side</button>
                        </div>
                    </div>

                    <!-- Match List -->
                    <div class="space-y-4">
                        
                        <!-- Game 1 -->
                        <div class="group relative p-5 bg-[#16161c] border border-white/5 rounded-2xl hover:border-brand-accent/30 transition-all">
                            <div class="absolute top-4 right-4 text-center">
                                <div class="text-xs text-gray-400 font-bold mb-1 uppercase">Fee</div>
                                <div class="text-brand-accent font-black text-lg">GHS 30</div>
                            </div>

                            <div class="flex items-start gap-4 mb-4">
                                <div class="w-12 h-12 bg-white/5 rounded-xl flex items-center justify-center text-gray-400">
                                    <i data-lucide="trophy" size="24"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-lg text-white group-hover:text-brand-accent transition-colors">Tuesday Night Ballers</h4>
                                    <div class="flex items-center gap-3 text-sm text-gray-400 mt-1">
                                        <span class="flex items-center gap-1"><i data-lucide="map-pin" size="12"></i> McDan Park, La</span>
                                        <span class="flex items-center gap-1"><i data-lucide="clock" size="12"></i> Tue, 19:00</span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-black/20 rounded-lg p-3 border border-white/5 mb-4">
                                <div class="flex justify-between text-xs mb-2">
                                    <span class="text-yellow-500 font-bold flex items-center gap-1"><i data-lucide="clock" size="12"></i> Pending (Needs 2 more)</span>
                                    <span class="text-gray-400">8 / 10 Players</span>
                                </div>
                                <div class="w-full bg-white/10 rounded-full h-2">
                                    <div class="bg-yellow-500 h-2 rounded-full" style="width: 80%"></div>
                                </div>
                            </div>

                            <div class="flex gap-3">
                                <a href="game-details.html" class="flex-1 py-2 bg-white/5 border border-white/10 rounded-lg text-center text-sm font-bold hover:bg-white/10 transition-colors">View Details</a>
                                <a href="game-details.html" class="flex-1 py-2 bg-brand-accent text-black rounded-lg text-center text-sm font-bold hover:bg-[#2fe080] transition-colors">Join Squad</a>
                            </div>
                        </div>

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
                        <p class="text-sm text-gray-400 mb-4">Sign in to build your player card and get rated.</p>
                        <a href="login.php" class="block w-full py-2 bg-white/10 border border-white/10 rounded-lg text-sm font-bold hover:bg-white/20 transition-colors">Log In / Sign Up</a>
                    </div>

                    <!-- Top Venues -->
                    <div class="bg-[#16161c] border border-white/5 rounded-xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold">Popular Pitches</h3>
                            <a href="venue-portal.html" class="text-xs text-brand-accent hover:underline">List yours</a>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-lg bg-white/5 flex items-center justify-center text-gray-500 font-bold text-xs">01</div>
                                <div class="flex-1">
                                    <div class="font-bold text-sm">McDan Park</div>
                                    <div class="text-xs text-gray-500">La, Accra</div>
                                </div>
                                <div class="flex items-center gap-1 bg-yellow-500/10 px-2 py-1 rounded text-yellow-500 text-xs font-bold">4.8 <i data-lucide="star" size="10" fill="currentColor"></i></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <!-- Footer -->
        <footer class="border-t border-white/5 mt-12 bg-[#0a0a0d] py-12 px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-8">
                <div>
                    <h5 class="font-bold text-white mb-4">Platform</h5>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-brand-accent">Find Matches</a></li>
                    </ul>
                </div>
            </div>
            <div class="flex flex-col md:flex-row justify-between items-center pt-8 border-t border-white/5 text-xs text-gray-600">
                <p>&copy; 2025 Haaah Sports Ghana.</p>
            </div>
        </footer>

    </main>

    <!-- Scripts -->
    <script>
        // Initialize Icons
        lucide.createIcons();

        // Mobile Sidebar Toggle
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobile-overlay');

        function toggleSidebar() {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        // Update UI based on server-side auth state
        function updateUI() {
            const isLoggedIn = <?php echo json_encode(isLoggedIn()); ?>;
            const guestElements = ['nav-guest', 'footer-guest', 'header-guest', 'hero-guest', 'widget-guest-login'];
            const userElements = ['nav-user', 'footer-user', 'header-user', 'hero-user'];

            if (isLoggedIn) {
                // Show User Elements, Hide Guest Elements
                guestElements.forEach(id => document.getElementById(id)?.classList.add('hidden-force'));
                userElements.forEach(id => document.getElementById(id)?.classList.remove('hidden-force'));
            } else {
                // Show Guest Elements, Hide User Elements
                userElements.forEach(id => document.getElementById(id)?.classList.add('hidden-force'));
                guestElements.forEach(id => document.getElementById(id)?.classList.remove('hidden-force'));
            }
        }

        // Logout function
        function logout() {
            window.location.href = '../actions/logout.php';
        }

        // Initialize UI on page load
        updateUI();

        // Header Scroll Effect
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
</body>
</html>