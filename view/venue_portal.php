<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Your Venue - Haaah Sports</title>
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
<body class="selection:bg-brand-accent selection:text-black">

    <nav class="absolute top-0 w-full p-6 flex justify-between items-center z-50">
        <h1 class="text-2xl font-black tracking-tighter text-white">
            HAAAH<span class="text-brand-purple text-base font-normal tracking-widest ml-1">VENUES</span>
        </h1>
        <a href="index.html" class="text-sm font-bold hover:text-brand-accent">Back to Player App</a>
    </nav>

    <div class="min-h-screen flex flex-col lg:flex-row">
        
        <!-- Left Side: Sales Pitch -->
        <div class="lg:w-1/2 bg-brand-card relative flex items-center justify-center p-12 overflow-hidden">
            <div class="absolute inset-0 z-0">
                <img src="https://images.unsplash.com/photo-1522778119026-d647f0565c6a?auto=format&fit=crop&q=80&w=1600" class="w-full h-full object-cover opacity-20 grayscale">
                <div class="absolute inset-0 bg-gradient-to-t from-brand-card to-transparent"></div>
            </div>
            
            <div class="relative z-10 max-w-lg">
                <h1 class="text-5xl font-black mb-6 leading-tight">
                    Empty Pitch? <br>
                    <span class="text-brand-purple">Lost Revenue.</span>
                </h1>
                <p class="text-gray-400 text-lg mb-8">
                    Join Ghana's largest network of sports facilities. We bring the players, handle the payments, and ensure your slots are filled.
                </p>
                <div class="space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-brand-purple/20 flex items-center justify-center text-brand-purple"><i data-lucide="check" size="20"></i></div>
                        <span>Guaranteed automated payments. No cash handling.</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-brand-purple/20 flex items-center justify-center text-brand-purple"><i data-lucide="check" size="20"></i></div>
                        <span>Dashboard to manage bookings and maintenance.</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-brand-purple/20 flex items-center justify-center text-brand-purple"><i data-lucide="check" size="20"></i></div>
                        <span>Marketing to 10,000+ local players.</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Registration Form -->
        <div class="lg:w-1/2 bg-brand-dark flex items-center justify-center p-12">
            <div class="max-w-md w-full">
                <h2 class="text-2xl font-bold mb-2">List your venue</h2>
                <p class="text-gray-500 mb-8 text-sm">Start filling your slots today.</p>

                <form class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">First Name</label>
                            <input type="text" class="w-full bg-[#1a1a23] border border-white/10 rounded-lg p-3 text-sm focus:border-brand-purple focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Last Name</label>
                            <input type="text" class="w-full bg-[#1a1a23] border border-white/10 rounded-lg p-3 text-sm focus:border-brand-purple focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Venue Name</label>
                        <input type="text" placeholder="e.g. Osu Community Park" class="w-full bg-[#1a1a23] border border-white/10 rounded-lg p-3 text-sm focus:border-brand-purple focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Location</label>
                        <!-- Google Maps Autocomplete would go here -->
                        <div class="relative">
                            <i data-lucide="map-pin" class="absolute left-3 top-3 text-gray-500" size="16"></i>
                            <input type="text" placeholder="Search for location..." class="w-full bg-[#1a1a23] border border-white/10 rounded-lg p-3 pl-10 text-sm focus:border-brand-purple focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pitch Type</label>
                        <select class="w-full bg-[#1a1a23] border border-white/10 rounded-lg p-3 text-sm focus:border-brand-purple focus:outline-none text-gray-400">
                            <option>Astroturf (5-a-side)</option>
                            <option>Astroturf (7-a-side)</option>
                            <option>Grass Pitch</option>
                            <option>Indoor Court</option>
                        </select>
                    </div>

                    <div class="pt-4">
                        <button class="w-full bg-brand-purple hover:bg-purple-600 text-white font-bold py-4 rounded-xl transition-colors">
                            Create Venue Account
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
    <script>lucide.createIcons();</script>
</body>
</html>