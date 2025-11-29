<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Haaah Sports - Just Play.</title>
    
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
        
        .text-gradient {
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-image: linear-gradient(to right, #3dff92, #00c6ff);
        }
    </style>
</head>
<body class="selection:bg-brand-accent selection:text-black">

    <!-- Navigation -->
    <nav class="fixed top-0 z-50 w-full bg-brand-dark/90 backdrop-blur-md border-b border-white/5">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-black tracking-tighter text-gradient">
                HAAAH<span class="text-white text-base font-normal tracking-widest ml-1">SPORTS</span>
            </h1>
            <div class="hidden md:flex gap-8 text-sm font-medium text-gray-400">
                <a href="#how-it-works" class="hover:text-brand-accent transition-colors">How it Works</a>
                <a href="#benefits" class="hover:text-brand-accent transition-colors">The Haaah Advantage</a>
                <a href="#mission" class="hover:text-brand-accent transition-colors">Mission</a>
            </div>
            <div class="flex gap-4">
                <a href="index.html" class="hidden sm:block text-sm font-bold text-white hover:text-brand-accent py-2">Log In</a>
                <a href="index.html" class="px-5 py-2 bg-brand-accent hover:bg-[#2fe080] text-black font-bold rounded-full text-sm transition-transform hover:scale-105">
                    Find a Game
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 px-6 overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1517466787929-bc90951d64b8?auto=format&fit=crop&q=80&w=1920" alt="Football Pitch" class="w-full h-full object-cover opacity-20">
            <div class="absolute inset-0 bg-gradient-to-b from-brand-dark via-brand-dark/90 to-brand-dark"></div>
        </div>

        <div class="relative z-10 max-w-4xl mx-auto text-center">
            <span class="inline-block px-4 py-1.5 rounded-full border border-brand-accent/30 bg-brand-accent/10 text-brand-accent text-xs font-bold uppercase tracking-wider mb-6 animate-pulse">
                No Team? No Problem.
            </span>
            <h1 class="text-5xl lg:text-7xl font-black tracking-tight mb-8 leading-[1.1]">
                Miss the Game? <br />
                <span class="text-gradient">We'll Find You a Squad.</span>
            </h1>
            <p class="text-xl text-gray-400 mb-10 max-w-2xl mx-auto leading-relaxed">
                Left Uni and lost your teammates? New in town? 
                Join open games or propose your own. Once enough players sign up, we handle the venue, the bibs, and the logistics. You just play.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="index.html" class="w-full sm:w-auto px-8 py-4 bg-white text-black font-bold rounded-xl hover:bg-gray-200 transition-colors flex items-center justify-center gap-2">
                    Join a Game Near You <i data-lucide="arrow-right" size="20"></i>
                </a>
                <a href="#how-it-works" class="w-full sm:w-auto px-8 py-4 bg-white/5 border border-white/10 text-white font-bold rounded-xl hover:bg-white/10 transition-colors">
                    See How It Works
                </a>
            </div>
        </div>
    </header>

    <!-- How It Works (The Threshold Concept) -->
    <section id="how-it-works" class="py-20 px-6 bg-brand-dark relative">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-black mb-4">The "Green Light" System</h2>
                <p class="text-gray-400">We don't book until you're ready. Here is how we facilitate the game.</p>
            </div>

            <!-- Process Timeline -->
            <div class="relative">
                <!-- Connecting Line (Desktop) -->
                <div class="hidden lg:block absolute top-1/2 left-0 w-full h-1 bg-white/5 -translate-y-1/2 z-0"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 relative z-10">
                    
                    <!-- Step 1 -->
                    <div class="bg-brand-card p-6 rounded-2xl border border-white/5 text-center">
                        <div class="w-16 h-16 mx-auto bg-brand-accent/10 rounded-full flex items-center justify-center text-brand-accent mb-4 text-2xl font-black">1</div>
                        <h3 class="text-xl font-bold mb-2">Propose or Join</h3>
                        <p class="text-gray-400 text-sm">
                            "I want to play 5-a-side at East Legon on Tuesday." You create the request, or join an existing one.
                        </p>
                    </div>

                    <!-- Step 2 -->
                    <div class="bg-brand-card p-6 rounded-2xl border border-white/5 text-center">
                        <div class="w-16 h-16 mx-auto bg-blue-500/10 rounded-full flex items-center justify-center text-blue-400 mb-4 text-2xl font-black">2</div>
                        <h3 class="text-xl font-bold mb-2">Fill the Slots</h3>
                        <p class="text-gray-400 text-sm">
                            The event stays in "Pending" mode. Share the link or let our community find it. We need 10 players minimum.
                        </p>
                    </div>

                    <!-- Step 3 (The Core Value) -->
                    <div class="bg-brand-card p-6 rounded-2xl border border-brand-accent/50 shadow-[0_0_30px_rgba(61,255,146,0.1)] text-center transform lg:-translate-y-4">
                        <div class="w-16 h-16 mx-auto bg-brand-accent rounded-full flex items-center justify-center text-black mb-4 text-2xl font-black">
                            <i data-lucide="check" size="32"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2 text-brand-accent">The Green Light</h3>
                        <p class="text-gray-300 text-sm">
                            <strong>Threshold Met!</strong> Our system automatically books the venue, assigns a referee, and processes payments.
                        </p>
                    </div>

                    <!-- Step 4 -->
                    <div class="bg-brand-card p-6 rounded-2xl border border-white/5 text-center">
                        <div class="w-16 h-16 mx-auto bg-purple-500/10 rounded-full flex items-center justify-center text-purple-400 mb-4 text-2xl font-black">4</div>
                        <h3 class="text-xl font-bold mb-2">Game On</h3>
                        <p class="text-gray-400 text-sm">
                            You just show up. The pitch is ready, the teams are sorted, and the stats are tracked.
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- The Logistics & Economies of Scale -->
    <section id="benefits" class="py-20 px-6 bg-[#16161c]">
        <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-center gap-16">
            <div class="lg:w-1/2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-purple/20 text-brand-purple text-xs font-bold mb-6">
                    <i data-lucide="box" size="14"></i> We Handle The Hassle
                </div>
                <h2 class="text-3xl lg:text-5xl font-black mb-6 leading-tight">
                    Why organize it yourself <br /> when we can do it better?
                </h2>
                <p class="text-gray-400 text-lg mb-8">
                    Organizing a game is hard. Collecting money is awkward. Booking pitches is expensive. 
                    <br><br>
                    Because Haaah Sports books hundreds of hours across Ghana, we get <strong>economies of scale</strong> that you can't get alone.
                </p>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 bg-black/20 rounded-xl border border-white/5">
                        <i data-lucide="map-pin" class="text-brand-accent mb-2"></i>
                        <h4 class="font-bold text-white">Premium Venues</h4>
                        <p class="text-sm text-gray-500">Access to pitches that usually require corporate bookings.</p>
                    </div>
                    <div class="p-4 bg-black/20 rounded-xl border border-white/5">
                        <i data-lucide="shirt" class="text-brand-accent mb-2"></i>
                        <h4 class="font-bold text-white">Logistics Sorted</h4>
                        <p class="text-sm text-gray-500">We provide the bibs, balls, and even water.</p>
                    </div>
                    <div class="p-4 bg-black/20 rounded-xl border border-white/5">
                        <i data-lucide="shield-check" class="text-brand-accent mb-2"></i>
                        <h4 class="font-bold text-white">Safe & Secure</h4>
                        <p class="text-sm text-gray-500">Vetted players and secure digital payments. No cash on the pitch.</p>
                    </div>
                    <div class="p-4 bg-black/20 rounded-xl border border-white/5">
                        <i data-lucide="users" class="text-brand-accent mb-2"></i>
                        <h4 class="font-bold text-white">Always a Game</h4>
                        <p class="text-sm text-gray-500">If your game doesn't hit the threshold, we merge you with another nearby.</p>
                    </div>
                </div>
            </div>
            
            <div class="lg:w-1/2">
                <!-- Visualizing the connection -->
                <div class="relative bg-brand-card border border-white/10 rounded-2xl p-8 overflow-hidden">
                    <div class="absolute top-0 right-0 p-32 bg-brand-accent/5 blur-[80px] rounded-full"></div>
                    
                    <h3 class="text-2xl font-black mb-6">The "Solo" Experience</h3>
                    
                    <div class="space-y-6 relative z-10">
                        <!-- User Story 1 -->
                        <div class="flex items-start gap-4">
                            <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=100" class="w-12 h-12 rounded-full object-cover border-2 border-brand-accent">
                            <div>
                                <p class="text-sm italic text-gray-300">"I moved to Accra for work and didn't know anyone. I joined a 'Pending' game on Haaah, 12 others joined, and by 7PM we were playing. Now we play every week."</p>
                                <p class="text-xs font-bold text-brand-accent mt-1">Kwame, 26</p>
                            </div>
                        </div>

                        <hr class="border-white/5">

                        <!-- User Story 2 -->
                        <div class="flex items-start gap-4">
                            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=100" class="w-12 h-12 rounded-full object-cover border-2 border-purple-500">
                            <div>
                                <p class="text-sm italic text-gray-300">"I wanted to organize a match for my alumni group but didn't want to chase people for money. I just sent the Haaah link. Once 15 people paid, the pitch was booked automatically."</p>
                                <p class="text-xs font-bold text-purple-400 mt-1">Sarah, 29</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Decentralizing Football Mission -->
    <section id="mission" class="py-20 px-6 text-center bg-brand-dark">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-3xl lg:text-5xl font-black mb-6">Decentralizing Sports</h2>
            <p class="text-xl text-gray-400 mb-10 leading-relaxed">
                We believe football shouldn't be gated by clubs or exclusive leagues. 
                By allowing <span class="text-white font-bold">anyone</span> to trigger an event, we are creating a grassroots network that runs itself.
                <br><br>
                Today Football. Tomorrow Basketball. Then the world.
            </p>
            <div class="inline-flex flex-wrap justify-center gap-3">
                <span class="px-4 py-2 rounded-full bg-white/5 border border-white/10 text-sm text-gray-300">⚽ Football</span>
                <span class="px-4 py-2 rounded-full bg-white/5 border border-white/10 text-sm text-gray-500 line-through decoration-brand-accent">🏀 Basketball</span>
                <span class="px-4 py-2 rounded-full bg-white/5 border border-white/10 text-sm text-gray-500 line-through decoration-brand-accent">🎾 Tennis</span>
                <span class="px-4 py-2 rounded-full bg-white/5 border border-white/10 text-sm text-gray-500 line-through decoration-brand-accent">🏐 Volleyball</span>
            </div>
            <p class="text-xs text-brand-accent mt-2 font-mono">Coming Soon</p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-white/5 bg-[#0a0a0d] py-12 px-6">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-8">
            <div class="text-center md:text-left">
                <h4 class="font-black text-xl tracking-tighter text-white">HAAAH<span class="text-gray-500 font-normal ml-1">SPORTS</span></h4>
                <p class="text-xs text-gray-500 mt-2 max-w-xs">
                    Facilitating play. Connecting communities.
                </p>
            </div>
            <div class="flex gap-8 text-sm text-gray-400">
                <a href="#" class="hover:text-white">Find a Game</a>
                <a href="#" class="hover:text-white">Venue Partners</a>
                <a href="#" class="hover:text-white">Contact</a>
            </div>
        </div>
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>