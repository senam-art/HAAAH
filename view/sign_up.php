<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Haaah Sports</title>
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
<body class="selection:bg-brand-accent selection:text-black min-h-screen flex flex-col relative overflow-hidden">

    <!-- Background Elements -->
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1543351611-58f69d7c1781?auto=format&fit=crop&q=80&w=1600" class="w-full h-full object-cover opacity-20 grayscale">
        <div class="absolute inset-0 bg-gradient-to-t from-brand-dark via-brand-dark/90 to-brand-dark/50"></div>
    </div>

    <!-- Nav -->
    <nav class="relative z-10 px-6 py-6 flex justify-between items-center">
        <a href="landing.html" class="font-black tracking-tighter text-2xl text-white">
            HAAAH<span class="text-brand-accent text-base font-normal tracking-widest ml-1">SPORTS</span>
        </a>
        <a href="venue-portal.html" class="hidden sm:block text-xs font-bold text-gray-400 hover:text-white border border-white/10 px-3 py-1.5 rounded-full">
            Own a pitch? Register here
        </a>
    </nav>

    <!-- Main Content -->
    <main class="flex-1 flex items-center justify-center px-4 relative z-10 py-12">
        <div class="w-full max-w-lg bg-brand-card/90 backdrop-blur-xl border border-white/10 rounded-3xl p-8 shadow-2xl">
            
            <div class="text-center mb-8">
                <h1 class="text-3xl font-black mb-2">Join the Squad</h1>
                <p class="text-gray-400 text-sm">Create your player profile to find games near you.</p>
            </div>

            <form class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">First Name</label>
                        <input type="text" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 text-sm focus:border-brand-accent focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Last Name</label>
                        <input type="text" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 text-sm focus:border-brand-accent focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Location / Neighborhood</label>
                    <div class="relative group">
                        <i data-lucide="map-pin" class="absolute left-3 top-3 text-brand-accent" size="18"></i>
                        <input type="text" placeholder="Search your area (e.g. East Legon)" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 pl-10 text-sm focus:border-brand-accent focus:outline-none">
                        <!-- Simulated Google Maps Autocomplete Dropdown -->
                        <div class="absolute top-full left-0 w-full bg-[#252530] border border-white/10 rounded-xl mt-1 hidden group-focus-within:block shadow-xl z-20">
                            <div class="p-2 text-xs text-gray-500 border-b border-white/5">Google Maps Suggestions</div>
                            <div class="p-3 hover:bg-white/5 cursor-pointer text-sm flex items-center gap-2">
                                <i data-lucide="map-pin" size="12" class="text-gray-400"></i> East Legon, Accra
                            </div>
                            <div class="p-3 hover:bg-white/5 cursor-pointer text-sm flex items-center gap-2">
                                <i data-lucide="map-pin" size="12" class="text-gray-400"></i> East Legon Hills, Accra
                            </div>
                        </div>
                    </div>
                    <p class="text-[10px] text-gray-500 mt-1">We use this to show you relevant games nearby.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email Address</label>
                    <div class="relative">
                        <i data-lucide="mail" class="absolute left-3 top-3 text-gray-500" size="18"></i>
                        <input type="email" placeholder="you@example.com" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 pl-10 text-sm focus:border-brand-accent focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Password</label>
                    <div class="relative">
                        <i data-lucide="lock" class="absolute left-3 top-3 text-gray-500" size="18"></i>
                        <input type="password" placeholder="Create a strong password" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 pl-10 text-sm focus:border-brand-accent focus:outline-none">
                    </div>
                </div>

                <div class="flex items-start gap-2 pt-2">
                    <input type="checkbox" class="mt-1 accent-brand-accent bg-brand-dark border-white/10 rounded">
                    <p class="text-xs text-gray-400 leading-tight">
                        I agree to the <a href="#" class="text-white hover:underline">Terms of Service</a> and <a href="#" class="text-white hover:underline">Privacy Policy</a>.
                    </p>
                </div>

                <button type="submit" class="w-full bg-brand-accent hover:bg-[#2fe080] text-black font-bold py-3 rounded-xl transition-transform hover:scale-[1.02] shadow-lg shadow-brand-accent/20 mt-4">
                    Create Account
                </button>
            </form>

            <p class="mt-8 text-center text-sm text-gray-400">
                Already have an account? 
                <a href="login.html" class="text-brand-accent font-bold hover:underline">Sign In</a>
            </p>
        </div>
    </main>

    <script>lucide.createIcons();</script>
</body>
</html>