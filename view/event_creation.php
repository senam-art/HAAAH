<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event - Haaah Sports</title>
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

    <!-- Simple Header -->
    <nav class="border-b border-white/5 bg-brand-card px-6 py-4 flex justify-between items-center sticky top-0 z-50">
        <a href="index.html" class="flex items-center gap-2 text-gray-400 hover:text-white">
            <i data-lucide="arrow-left" size="20"></i> Back to Dashboard
        </a>
        <h1 class="font-bold">Create New Event</h1>
        <div class="w-8"></div> <!-- Spacer -->
    </nav>

    <div class="max-w-3xl mx-auto px-6 py-8">
        
        <!-- Progress Bar -->
        <div class="flex items-center justify-between mb-8 text-sm font-medium text-gray-500">
            <span class="text-brand-accent">1. Details</span>
            <span>2. Venue</span>
            <span>3. Rules</span>
            <span>4. Review</span>
        </div>
        <div class="h-1 bg-white/10 rounded-full mb-12 overflow-hidden">
            <div class="h-full w-1/4 bg-brand-accent"></div>
        </div>

        <form class="space-y-8">
            
            <!-- Section 1: Basic Info -->
            <div class="bg-brand-card p-6 rounded-2xl border border-white/5">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <i data-lucide="trophy" class="text-brand-accent"></i> Match Basics
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">Event Title</label>
                        <input type="text" placeholder="e.g. Tuesday Night 5-a-side" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 focus:border-brand-accent focus:outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Sport</label>
                            <select class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 focus:border-brand-accent focus:outline-none">
                                <option>Football (Soccer)</option>
                                <option>Basketball</option>
                                <option>Volleyball</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Format</label>
                            <select class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 focus:border-brand-accent focus:outline-none">
                                <option>5 vs 5</option>
                                <option>7 vs 7</option>
                                <option>11 vs 11</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Venue Selection (Map Integration Point) -->
            <div class="bg-brand-card p-6 rounded-2xl border border-white/5">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <i data-lucide="map-pin" class="text-brand-accent"></i> Select Venue
                </h2>
                
                <!-- Google Maps Placeholder -->
                <div class="h-64 bg-[#2a2a35] rounded-xl mb-4 relative flex items-center justify-center border border-white/10 group cursor-pointer overflow-hidden">
                    <div class="absolute inset-0 bg-[url('https://api.mapbox.com/styles/v1/mapbox/dark-v10/static/-0.1870,5.6037,12,0/800x400?access_token=YOUR_TOKEN')] bg-cover opacity-50 grayscale group-hover:grayscale-0 transition-all"></div>
                    <div class="relative z-10 text-center">
                        <i data-lucide="map" size="32" class="mx-auto mb-2 text-brand-accent"></i>
                        <span class="text-sm font-bold">Click to Open Map View</span>
                        <p class="text-xs text-gray-400 mt-1">(Google Maps API Integration)</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Venue Card Selected -->
                    <div class="p-4 border border-brand-accent bg-brand-accent/5 rounded-xl cursor-pointer">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold">McDan Astro Turf</h3>
                            <i data-lucide="check-circle" class="text-brand-accent fill-brand-accent/20"></i>
                        </div>
                        <p class="text-xs text-gray-400 mb-2">La Town Park, Accra</p>
                        <div class="text-sm font-bold">GHS 250 <span class="text-gray-500 font-normal">/ hr</span></div>
                    </div>
                    
                    <!-- Venue Card Option -->
                    <div class="p-4 border border-white/10 bg-brand-dark rounded-xl cursor-pointer hover:border-white/30">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold">Ajax Park</h3>
                        </div>
                        <p class="text-xs text-gray-400 mb-2">Legon University Campus</p>
                        <div class="text-sm font-bold">GHS 150 <span class="text-gray-500 font-normal">/ hr</span></div>
                    </div>
                </div>
            </div>

            <!-- Section 3: The "Economics" (Threshold Logic) -->
            <div class="bg-brand-card p-6 rounded-2xl border border-white/5">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <i data-lucide="wallet" class="text-brand-accent"></i> Cost & Thresholds
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Cost per Player (GHS)</label>
                            <input type="number" value="30" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 focus:border-brand-accent font-mono text-xl">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Min. Players to "Green Light"</label>
                            <div class="flex items-center gap-4">
                                <input type="range" min="8" max="22" value="10" class="flex-1 accent-brand-accent h-2 bg-brand-dark rounded-lg appearance-none cursor-pointer">
                                <span class="font-mono text-xl font-bold">10</span>
                            </div>
                        </div>
                    </div>

                    <!-- Live Calculator -->
                    <div class="bg-black/40 rounded-xl p-4 border border-white/5 flex flex-col justify-center">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-400">Venue Cost</span>
                            <span class="font-mono">GHS 250.00</span>
                        </div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-400">Haaah Fee (10%)</span>
                            <span class="font-mono">GHS 25.00</span>
                        </div>
                        <div class="h-px bg-white/10 my-2"></div>
                        <div class="flex justify-between items-center text-brand-accent">
                            <span class="font-bold">Total needed</span>
                            <span class="font-mono font-bold text-xl">GHS 275.00</span>
                        </div>
                        <div class="mt-4 text-xs text-center text-gray-500 bg-white/5 py-2 rounded">
                            <i data-lucide="info" size="12" class="inline mb-0.5"></i> 
                            With 10 players paying GHS 30, you earn <span class="text-white font-bold">GHS 25.00</span> commission.
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="button" class="flex-1 py-4 rounded-xl font-bold bg-white/5 hover:bg-white/10 transition-colors">Save Draft</button>
                <button type="button" class="flex-[2] py-4 rounded-xl font-bold bg-brand-accent text-black hover:bg-[#2fe080] transition-colors shadow-lg shadow-brand-accent/20">
                    Publish Event
                </button>
            </div>

        </form>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>