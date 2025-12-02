<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Haaah Sports - Just Play.</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Updated Lucide Script to a stable version -->
    <script src="https://unpkg.com/lucide@0.263.1/dist/umd/lucide.min.js"></script>
    
    <!-- Google Maps API for Location Coordinates -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDgP6xqZcN4y50x2kq8cbytyD-k4OY1Sis&libraries=places"></script>

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
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; background-color: #0f0f13; color: white; }
        
        /* 1. Text Shimmer Animation */
        .text-gradient {
            background: linear-gradient(to right, #3dff92, #00c6ff, #3dff92);
            background-size: 200% auto;
            color: transparent;
            -webkit-background-clip: text;
            background-clip: text;
            animation: textShine 5s linear infinite;
        }
        @keyframes textShine {
            to { background-position: 200% center; }
        }

        /* 2. Scroll Reveal Animation Classes */
        /* Default state is visible for accessibility/fallback, JS toggles 'reveal-hidden' */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s cubic-bezier(0.5, 0, 0, 1);
        }
        
        /* Force visibility if JS fails or on initial load before script runs */
        .no-js .reveal {
            opacity: 1;
            transform: translateY(0);
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* Stagger delays for grid items */
        .reveal-delay-100 { transition-delay: 0.1s; }
        .reveal-delay-200 { transition-delay: 0.2s; }
        .reveal-delay-300 { transition-delay: 0.3s; }
    </style>
</head>
<body class="selection:bg-brand-accent selection:text-black overflow-x-hidden">

    <!-- Navigation -->
    <nav class="fixed top-0 z-50 w-full bg-brand-dark/90 backdrop-blur-md border-b border-white/5 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-black tracking-tighter text-white">
                HAAAH<span class="text-brand-accent text-base font-normal tracking-widest ml-1">SPORTS</span>
            </h1>
            <div class="hidden md:flex gap-8 text-sm font-medium text-gray-400">
                <a href="#how-it-works" class="hover:text-brand-accent transition-colors">How it Works</a>
                <a href="#benefits" class="hover:text-brand-accent transition-colors">The Haaah Advantage</a>
                <a href="#mission" class="hover:text-brand-accent transition-colors">Mission</a>
            </div>
            <div class="flex gap-4">
                <a href="view/login.php" class="hidden sm:block text-sm font-bold text-white hover:text-brand-accent py-2">Log In</a>
                <a href="view/homepage.php" class="px-5 py-2 bg-brand-accent hover:bg-[#2fe080] text-black font-bold rounded-full text-sm transition-transform hover:scale-105 shadow-lg shadow-brand-accent/20">
                    Find a Game
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 px-6 overflow-hidden">
        <div class="absolute inset-0 z-0">
            <!-- 
                ADJUSTMENTS FOR LOCAL IMAGE:
                1. Increased opacity from 'opacity-20' to 'opacity-60' so your image is brighter.
                2. Changed the gradient overlay below to be less dark (via-brand-dark/50).
            -->
            <img src="assets/images/your-image.jpg" alt="Football Pitch" class="w-full h-full object-cover opacity-60 scale-105 animate-[pulse_10s_ease-in-out_infinite]" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1551958219-acbc608c6377?auto=format&fit=crop&q=80&w=1920'">
            <!-- Gradient Overlay: Made lighter (via-brand-dark/50) so you can see the image -->
            <div class="absolute inset-0 bg-gradient-to-b from-brand-dark/80 via-brand-dark/50 to-brand-dark"></div>
        </div>

        <div class="relative z-10 max-w-4xl mx-auto text-center reveal active">
            <span class="inline-block px-4 py-1.5 rounded-full border border-brand-accent/30 bg-brand-accent/10 text-brand-accent text-xs font-bold uppercase tracking-wider mb-6 animate-bounce">
                No Team? No Problem.
            </span>
            <h1 class="text-5xl lg:text-7xl font-black tracking-tight mb-8 leading-[1.1]">
                Miss the Game? <br />
                <span class="text-gradient">We'll Find You a Squad.</span>
            </h1>
            <p class="text-xl text-gray-400 mb-10 max-w-2xl mx-auto leading-relaxed reveal reveal-delay-100">
                Left Uni and lost your teammates? New in town? 
                Join open games or propose your own. Once enough players sign up, we handle the venue, the bibs, and the logistics. You just play.
            </p>
            
            <!-- SEARCH BAR: Captures Coordinates + Current Location Button -->
            <!-- Using Inline SVGs to ensure icons show up instantly -->
            <div class="reveal reveal-delay-200 w-full max-w-lg mx-auto mt-8 relative z-20">
                <form onsubmit="handleLocationSearch(event)" class="relative flex items-center w-full">
                    
                    <!-- Map Pin Icon -->
                    <div class="absolute left-4 z-10 text-brand-accent pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    
                    <!-- Input -->
                    <input id="landing-location-input" type="text" placeholder="Enter your location..." class="w-full bg-white text-black font-bold pl-12 pr-28 py-4 rounded-xl shadow-2xl focus:outline-none focus:ring-4 focus:ring-brand-accent/50 transition-all placeholder:text-gray-400">
                    
                    <!-- Right Side Buttons -->
                    <div class="absolute right-2 top-1/2 -translate-y-1/2 flex items-center gap-1 z-20">
                        <!-- Use Location Button -->
                        <button type="button" onclick="useCurrentLocation()" class="p-2 text-gray-400 hover:text-brand-accent hover:bg-gray-100 rounded-lg transition-colors" title="Use my current location">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="22" y1="12" x2="18" y2="12"/><line x1="6" y1="12" x2="2" y2="12"/><line x1="12" y1="6" x2="12" y2="2"/><line x1="12" y1="22" x2="12" y2="18"/></svg>
                        </button>

                        <!-- Submit Button -->
                        <button type="submit" class="bg-brand-accent text-black p-2.5 rounded-lg hover:bg-[#2fe080] transition-colors shadow-lg flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </button>
                    </div>
                </form>
                <div class="flex items-center justify-center gap-4 mt-6">
                    <a href="view/homepage.php" class="text-sm font-bold text-white/70 hover:text-white border-b border-white/20 hover:border-white transition-colors">Browse all games</a>
                    <span class="text-white/20">•</span>
                    <a href="#how-it-works" class="text-sm font-bold text-white/70 hover:text-white border-b border-white/20 hover:border-white transition-colors">How it works</a>
                </div>
            </div>

        </div>
    </header>

    <!-- How It Works (The Threshold Concept) -->
    <section id="how-it-works" class="py-20 px-6 bg-brand-dark relative">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16 reveal">
                <h2 class="text-3xl lg:text-4xl font-black mb-4">The "Green Light" System</h2>
                <p class="text-gray-400">We don't book until you're ready. Here is how we facilitate the game.</p>
            </div>

            <!-- Process Timeline -->
            <div class="relative">
                <!-- Connecting Line (Desktop) -->
                <div class="hidden lg:block absolute top-1/2 left-0 w-full h-1 bg-white/5 -translate-y-1/2 z-0"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 relative z-10">
                    
                    <!-- Step 1 -->
                    <div class="bg-brand-card p-6 rounded-2xl border border-white/5 text-center reveal hover:-translate-y-2 transition-transform duration-300">
                        <div class="w-16 h-16 mx-auto bg-brand-accent/10 rounded-full flex items-center justify-center text-brand-accent mb-4 text-2xl font-black shadow-[0_0_20px_rgba(61,255,146,0.1)]">1</div>
                        <h3 class="text-xl font-bold mb-2">Propose or Join</h3>
                        <p class="text-gray-400 text-sm">
                            "I want to play 5-a-side at East Legon on Tuesday." You create the request, or join an existing one.
                        </p>
                    </div>

                    <!-- Step 2 -->
                    <div class="bg-brand-card p-6 rounded-2xl border border-white/5 text-center reveal reveal-delay-100 hover:-translate-y-2 transition-transform duration-300">
                        <div class="w-16 h-16 mx-auto bg-blue-500/10 rounded-full flex items-center justify-center text-blue-400 mb-4 text-2xl font-black shadow-[0_0_20px_rgba(59,130,246,0.1)]">2</div>
                        <h3 class="text-xl font-bold mb-2">Fill the Slots</h3>
                        <p class="text-gray-400 text-sm">
                            The event stays in "Pending" mode. Share the link or let our community find it. We need 10 players minimum.
                        </p>
                    </div>

                    <!-- Step 3 (The Core Value) -->
                    <div class="bg-brand-card p-6 rounded-2xl border border-brand-accent/50 shadow-[0_0_30px_rgba(61,255,146,0.15)] text-center transform lg:-translate-y-4 reveal reveal-delay-200">
                        <div class="w-16 h-16 mx-auto bg-brand-accent rounded-full flex items-center justify-center text-black mb-4 text-2xl font-black animate-pulse">
                            <i data-lucide="check" size="32"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2 text-brand-accent">The Green Light</h3>
                        <p class="text-gray-300 text-sm">
                            <strong>Threshold Met!</strong> Our system automatically books the venue, assigns a referee, and processes payments.
                        </p>
                    </div>

                    <!-- Step 4 -->
                    <div class="bg-brand-card p-6 rounded-2xl border border-white/5 text-center reveal reveal-delay-300 hover:-translate-y-2 transition-transform duration-300">
                        <div class="w-16 h-16 mx-auto bg-purple-500/10 rounded-full flex items-center justify-center text-purple-400 mb-4 text-2xl font-black shadow-[0_0_20px_rgba(168,85,247,0.1)]">4</div>
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
            <div class="lg:w-1/2 reveal">
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
                    <div class="p-4 bg-black/20 rounded-xl border border-white/5 hover:bg-white/5 transition-colors">
                        <i data-lucide="map-pin" class="text-brand-accent mb-2"></i>
                        <h4 class="font-bold text-white">Premium Venues</h4>
                        <p class="text-sm text-gray-500">Access to pitches that usually require corporate bookings.</p>
                    </div>
                    <div class="p-4 bg-black/20 rounded-xl border border-white/5 hover:bg-white/5 transition-colors">
                        <i data-lucide="shirt" class="text-brand-accent mb-2"></i>
                        <h4 class="font-bold text-white">Logistics Sorted</h4>
                        <p class="text-sm text-gray-500">We provide the bibs, balls, and even water.</p>
                    </div>
                    <div class="p-4 bg-black/20 rounded-xl border border-white/5 hover:bg-white/5 transition-colors">
                        <i data-lucide="shield-check" class="text-brand-accent mb-2"></i>
                        <h4 class="font-bold text-white">Safe & Secure</h4>
                        <p class="text-sm text-gray-500">Vetted players and secure digital payments. No cash on the pitch.</p>
                    </div>
                    <div class="p-4 bg-black/20 rounded-xl border border-white/5 hover:bg-white/5 transition-colors">
                        <i data-lucide="users" class="text-brand-accent mb-2"></i>
                        <h4 class="font-bold text-white">Always a Game</h4>
                        <p class="text-sm text-gray-500">If your game doesn't hit the threshold, we merge you with another nearby.</p>
                    </div>
                </div>
            </div>
            
            <div class="lg:w-1/2 reveal reveal-delay-200">
                <!-- Visualizing the connection -->
                <div class="relative bg-brand-card border border-white/10 rounded-2xl p-8 overflow-hidden hover:border-brand-accent/30 transition-colors duration-500">
                    <div class="absolute top-0 right-0 p-32 bg-brand-accent/5 blur-[80px] rounded-full"></div>
                    
                    <h3 class="text-2xl font-black mb-6">The "Solo" Experience</h3>
                    
                    <div class="space-y-6 relative z-10">
                        <!-- User Story 1 -->
                        <div class="flex items-start gap-4">
                            <div>
                                <p class="text-sm italic text-gray-300">"I moved to Accra for work and didn't know anyone. I joined a 'Pending' game on Haaah, 12 others joined, and by 7PM we were playing. Now we play every week."</p>
                                <p class="text-xs font-bold text-brand-accent mt-1">Kwame, 26</p>
                            </div>
                        </div>

                        <hr class="border-white/5">

                        <!-- User Story 2 -->
                        <div class="flex items-start gap-4">
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
        <div class="max-w-4xl mx-auto reveal">
            <h2 class="text-3xl lg:text-5xl font-black mb-6">Decentralizing Sports</h2>
            <p class="text-xl text-gray-400 mb-10 leading-relaxed">
                We believe football shouldn't be gated by clubs or exclusive leagues. 
                By allowing <span class="text-white font-bold">anyone</span> to trigger an event, we are creating a grassroots network that runs itself.
                <br><br>
                Today Football. Tomorrow Basketball. Then the world.
            </p>
            <div class="inline-flex flex-wrap justify-center gap-3">
                <span class="px-4 py-2 rounded-full bg-white/5 border border-white/10 text-sm text-gray-300 hover:bg-white/10 transition-colors cursor-default">⚽ Football</span>
                <span class="px-4 py-2 rounded-full bg-white/5 border border-white/10 text-sm text-gray-500 line-through decoration-brand-accent opacity-50">🏀 Basketball</span>
                <span class="px-4 py-2 rounded-full bg-white/5 border border-white/10 text-sm text-gray-500 line-through decoration-brand-accent opacity-50">🎾 Tennis</span>
                <span class="px-4 py-2 rounded-full bg-white/5 border border-white/10 text-sm text-gray-500 line-through decoration-brand-accent opacity-50">🏐 Volleyball</span>
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
                <a href="view/homepage.php" class="hover:text-white transition-colors">Find a Game</a>
                <a href="view/venue_portal.php" class="hover:text-white transition-colors">Venue Partners</a>
                <a href="#" class="hover:text-white transition-colors">Contact</a>
            </div>
        </div>
    </footer>

    <script>
        // GLOBAL: Hold the location coordinates
        let selectedLat = null;
        let selectedLng = null;

        function initAutocomplete() {
            const input = document.getElementById('landing-location-input');
            if(!input) return;

            const autocomplete = new google.maps.places.Autocomplete(input, {
                types: ['geocode'], // or 'establishment'
                componentRestrictions: { country: 'gh' } // Limit to Ghana
            });

            autocomplete.addListener('place_changed', function() {
                const place = autocomplete.getPlace();
                if (place.geometry) {
                    selectedLat = place.geometry.location.lat();
                    selectedLng = place.geometry.location.lng();
                    console.log("Selected Location:", selectedLat, selectedLng);
                }
            });
        }

        function handleLocationSearch(e) {
            e.preventDefault();
            const input = document.getElementById('landing-location-input');
            const term = input.value;

            // Alert user if empty (since 'required' was removed)
            if (!term && !selectedLat) {
                input.focus();
                input.classList.add('ring-2', 'ring-red-500');
                setTimeout(() => input.classList.remove('ring-2', 'ring-red-500'), 2000);
                return;
            }

            // Build redirection URL
            let url = 'view/homepage.php?search=' + encodeURIComponent(term);
            
            // If we have coordinates, append them for DB precision
            if (selectedLat && selectedLng) {
                url += `&lat=${selectedLat}&lng=${selectedLng}`;
            }

            window.location.href = url;
        }

        // NEW FUNCTION: Use Browser Geolocation
        function useCurrentLocation() {
            if (!navigator.geolocation) {
                alert("Geolocation is not supported by your browser.");
                return;
            }

            const btn = document.querySelector('button[title="Use my current location"]');
            const input = document.getElementById('landing-location-input');
            const originalIcon = btn.innerHTML;
            
            // Visual feedback
            btn.innerHTML = `<svg class="animate-spin h-5 w-5 text-brand-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;
            input.value = "Locating...";
            
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    // Redirect to homepage with coordinates
                    window.location.href = `view/homepage.php?lat=${lat}&lng=${lng}&search=Current Location`;
                },
                (error) => {
                    console.error("Error getting location:", error);
                    alert("Unable to retrieve your location. Please check your browser settings.");
                    btn.innerHTML = originalIcon; // Reset button
                    input.value = ""; // Reset input
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                }
            );
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Init Lucide icons inside DOMContentLoaded to ensure elements exist
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            // SCROLL REVEAL ANIMATION SCRIPT
            const observerOptions = {
                threshold: 0.1,
                rootMargin: "0px 0px -50px 0px" // Trigger slightly before element is fully in view
            };

            // Safety check if IntersectionObserver is supported
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('active');
                            observer.unobserve(entry.target); // Once shown, keep shown
                        }
                    });
                }, observerOptions);

                document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
            } else {
                // Fallback for very old browsers: just show everything
                document.querySelectorAll('.reveal').forEach(el => el.classList.add('active'));
            }

            // Init Maps Autocomplete if API is loaded
            if (typeof google !== 'undefined' && google.maps && google.maps.places) {
                initAutocomplete();
            } else {
                // Retry if script is loading async
                setTimeout(() => {
                    if (typeof google !== 'undefined') initAutocomplete();
                }, 1000);
            }
        });
    </script>
</body>
</html>