<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Haaah Sports</title>
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
    <style>@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap'); body { font-family: 'Inter', sans-serif; background-color: #0f0f13; color: white; }</style>
</head>
<body class="selection:bg-brand-accent selection:text-black pb-20">

    <!-- Navbar -->
    <nav class="border-b border-white/5 bg-brand-dark px-6 py-4 flex justify-between items-center sticky top-0 z-50">
        <a href="index.html" class="font-black tracking-tighter text-xl">
            HAAAH<span class="text-gray-500 text-xs font-normal ml-1">SPORTS</span>
        </a>
        <div class="flex items-center gap-4">
            <!-- The "Switch" Button the user asked about -->
            <a href="dashboard.html" class="hidden sm:flex items-center gap-2 px-4 py-2 bg-white/5 border border-white/10 rounded-full text-xs font-bold hover:bg-white/10 transition-colors">
                <i data-lucide="layout-dashboard" size="14"></i> Switch to Organizer
            </a>
            <div class="w-8 h-8 rounded-full bg-purple-600 flex items-center justify-center font-bold text-sm">JM</div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-8">

        <!-- Profile Header -->
        <div class="relative bg-brand-card rounded-3xl p-8 mb-8 border border-white/5 overflow-hidden">
            <!-- Decorative background element -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-brand-accent/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row items-center md:items-start gap-8">
                <!-- Avatar -->
                <div class="relative group">
                    <div class="w-32 h-32 rounded-full bg-gradient-to-br from-purple-500 to-blue-600 p-1">
                        <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=300" class="w-full h-full rounded-full object-cover border-4 border-brand-card">
                    </div>
                    <button class="absolute bottom-0 right-2 p-2 bg-brand-card border border-white/10 rounded-full text-brand-accent hover:text-white transition-colors">
                        <i data-lucide="camera" size="16"></i>
                    </button>
                </div>

                <!-- Info -->
                <div class="flex-1 text-center md:text-left">
                    <div class="flex flex-col md:flex-row items-center md:justify-between mb-4">
                        <div>
                            <h1 class="text-3xl font-black mb-1">John Mensah</h1>
                            <p class="text-gray-400 flex items-center justify-center md:justify-start gap-2 text-sm">
                                <i data-lucide="map-pin" size="14"></i> East Legon, Accra
                                <span class="w-1 h-1 rounded-full bg-gray-600"></span>
                                <span class="text-brand-accent">Midfielder</span>
                            </p>
                        </div>
                        <button class="mt-4 md:mt-0 px-6 py-2 border border-white/20 rounded-xl font-bold text-sm hover:bg-white/5 transition-colors">
                            Edit Profile
                        </button>
                    </div>

                    <!-- Tags -->
                    <div class="flex flex-wrap justify-center md:justify-start gap-2 mb-6">
                        <span class="px-3 py-1 rounded-lg bg-white/5 text-xs font-bold text-gray-300">⚽ Football</span>
                        <span class="px-3 py-1 rounded-lg bg-white/5 text-xs font-bold text-gray-300">👟 5-a-side</span>
                        <span class="px-3 py-1 rounded-lg bg-brand-accent/10 text-xs font-bold text-brand-accent">🔥 Competitive</span>
                    </div>

                    <!-- Reliability Score (Crucial for a community app) -->
                    <div class="inline-flex items-center gap-4 bg-black/20 px-4 py-2 rounded-xl border border-white/5">
                        <div class="flex items-center gap-1">
                            <i data-lucide="shield-check" class="text-green-500" size="18"></i>
                            <span class="text-sm font-bold text-gray-300">Reliability Score</span>
                        </div>
                        <div class="h-4 w-px bg-white/10"></div>
                        <span class="font-black text-brand-accent">98%</span>
                        <span class="text-xs text-gray-500">(Never missed a game)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-brand-card p-5 rounded-2xl border border-white/5 text-center">
                <div class="text-3xl font-black mb-1">24</div>
                <div class="text-xs text-gray-400 font-bold uppercase tracking-wider">Matches</div>
            </div>
            <div class="bg-brand-card p-5 rounded-2xl border border-white/5 text-center">
                <div class="text-3xl font-black mb-1 text-brand-accent">6</div>
                <div class="text-xs text-gray-400 font-bold uppercase tracking-wider">Goals</div>
            </div>
            <div class="bg-brand-card p-5 rounded-2xl border border-white/5 text-center">
                <div class="text-3xl font-black mb-1 text-purple-400">3</div>
                <div class="text-xs text-gray-400 font-bold uppercase tracking-wider">MVPs</div>
            </div>
            <div class="bg-brand-card p-5 rounded-2xl border border-white/5 text-center">
                <div class="text-3xl font-black mb-1">4.8</div>
                <div class="text-xs text-gray-400 font-bold uppercase tracking-wider">Rating</div>
            </div>
        </div>

        <!-- Content Split -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left: Upcoming Schedule -->
            <div class="lg:col-span-2 space-y-6">
                <h3 class="font-bold text-xl flex items-center gap-2">
                    <i data-lucide="calendar-clock" class="text-brand-accent"></i> My Schedule
                </h3>

                <!-- Upcoming Game Card -->
                <div class="bg-brand-card rounded-xl p-5 border-l-4 border-brand-accent border-y border-r border-white/5 hover:border-r-white/20 transition-all cursor-pointer">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <span class="px-2 py-1 bg-brand-accent/10 text-brand-accent text-[10px] font-bold uppercase tracking-wider rounded">Tomorrow</span>
                            <h4 class="font-bold text-lg mt-2">Tuesday Night 5-a-side</h4>
                            <p class="text-gray-400 text-sm">McDan Astro Turf • 19:00</p>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-gray-500 mb-1">Status</div>
                            <div class="text-green-400 font-bold text-sm flex items-center gap-1 justify-end">
                                <i data-lucide="check-circle" size="14"></i> Confirmed
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 pt-4 border-t border-white/5">
                        <div class="flex -space-x-2">
                            <div class="w-8 h-8 rounded-full bg-gray-700 border-2 border-brand-card"></div>
                            <div class="w-8 h-8 rounded-full bg-gray-600 border-2 border-brand-card"></div>
                            <div class="w-8 h-8 rounded-full bg-gray-500 border-2 border-brand-card flex items-center justify-center text-[10px] text-white">+8</div>
                        </div>
                        <span class="text-xs text-gray-500">You are playing with 10 others</span>
                    </div>
                </div>

                <!-- Pending Game Card -->
                <div class="bg-brand-card rounded-xl p-5 border-l-4 border-yellow-500 border-y border-r border-white/5 hover:border-r-white/20 transition-all cursor-pointer opacity-75 hover:opacity-100">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <span class="px-2 py-1 bg-yellow-500/10 text-yellow-500 text-[10px] font-bold uppercase tracking-wider rounded">Saturday</span>
                            <h4 class="font-bold text-lg mt-2">Weekend League Match</h4>
                            <p class="text-gray-400 text-sm">Ajax Park • 16:00</p>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-gray-500 mb-1">Status</div>
                            <div class="text-yellow-500 font-bold text-sm flex items-center gap-1 justify-end">
                                <i data-lucide="clock" size="14"></i> Needs 2 more
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                         <div class="w-full bg-white/5 rounded-full h-1.5 mb-2">
                            <div class="bg-yellow-500 h-1.5 rounded-full" style="width: 80%"></div>
                         </div>
                         <div class="flex justify-between text-xs text-gray-500">
                             <span>8 players joined</span>
                             <span>Target: 10</span>
                         </div>
                    </div>
                </div>

            </div>

            <!-- Right: Settings / Account -->
            <div class="space-y-6">
                <h3 class="font-bold text-xl">Settings</h3>
                
                <div class="bg-brand-card rounded-xl border border-white/5 overflow-hidden">
                    <a href="#" class="flex items-center justify-between p-4 hover:bg-white/5 transition-colors border-b border-white/5">
                        <div class="flex items-center gap-3">
                            <i data-lucide="credit-card" size="18" class="text-gray-400"></i>
                            <span class="text-sm font-medium">Payment Methods</span>
                        </div>
                        <i data-lucide="chevron-right" size="16" class="text-gray-500"></i>
                    </a>
                    <a href="#" class="flex items-center justify-between p-4 hover:bg-white/5 transition-colors border-b border-white/5">
                        <div class="flex items-center gap-3">
                            <i data-lucide="bell" size="18" class="text-gray-400"></i>
                            <span class="text-sm font-medium">Notifications</span>
                        </div>
                        <i data-lucide="chevron-right" size="16" class="text-gray-500"></i>
                    </a>
                    <a href="#" class="flex items-center justify-between p-4 hover:bg-white/5 transition-colors">
                        <div class="flex items-center gap-3">
                            <i data-lucide="lock" size="18" class="text-gray-400"></i>
                            <span class="text-sm font-medium">Privacy & Security</span>
                        </div>
                        <i data-lucide="chevron-right" size="16" class="text-gray-500"></i>
                    </a>
                </div>

                <div class="bg-red-500/10 rounded-xl p-4 border border-red-500/20">
                    <button class="w-full text-red-500 font-bold text-sm flex items-center justify-center gap-2">
                        <i data-lucide="log-out" size="16"></i> Log Out
                    </button>
                </div>

            </div>

        </div>

    </div>

    <script>lucide.createIcons();</script>
</body>
</html>
