<?php
require_once __DIR__ . '/../settings/core.php';
hasLoggedIn();
?>
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
    <style>@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap'); body { font-family: 'Inter', sans-serif; background-color: #0f0f13; color: white; }
        /* Make scroll bar visible and styled */
        body { scrollbar-color: rgba(255,255,255,0.08) rgba(255,255,255,0.02); }
        ::-webkit-scrollbar { width: 10px; height: 10px; }
        ::-webkit-scrollbar-track { background: rgba(255,255,255,0.02); }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.08); border-radius: 999px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.12); }
    </style>
</head>
<body class="selection:bg-brand-accent selection:text-black min-h-screen flex flex-col relative overflow-auto">

    <!-- Background Elements -->
    <div class="absolute inset-0 z-0">
        <img src="../images/backgroundimage_landing.jpeg" class="w-full h-full object-cover opacity-60 grayscale">
        <div class="absolute inset-0 bg-gradient-to-t from-brand-dark via-brand-dark/20 to-brand-dark/50"></div>
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

            <form id="signupForm" class="space-y-4" novalidate>
                <div id="formMessage" class="text-center text-sm hidden mb-4"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">First Name</label>
                        <input id="firstName" name="first_name" required type="text" value="Test" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 text-sm focus:border-brand-accent focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Last Name</label>
                        <input id="lastName" name="last_name" required type="text" value="User" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 text-sm focus:border-brand-accent focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Location / Neighborhood</label>
                    <div class="relative group">
                        <i data-lucide="map-pin" class="absolute left-3 top-3 text-brand-accent" size="18"></i>
                        <input id="location" name="location" required type="text" placeholder="Search your area (e.g. East Legon)" value="Testville" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 pl-10 text-sm focus:border-brand-accent focus:outline-none">
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
                        <input id="email" name="email" required type="email" placeholder="you@example.com" value="test+1@example.com" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 pl-10 text-sm focus:border-brand-accent focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Username</label>
                    <div class="relative">
                        <i data-lucide="mail" class="absolute left-3 top-3 text-gray-500" size="18"></i>
                        <input id="username" name="username" required type="text" placeholder="eg.r2trappy" value="testuser" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 pl-10 text-sm focus:border-brand-accent focus:outline-none">
                        <p id="usernameError" class="mt-2 text-xs text-red-400 hidden">Username must be at least 4 characters.</p>
                    </div>
                </div>

                <div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Password</label>
                            <div class="relative">
                                <i data-lucide="lock" class="absolute left-3 top-3 text-gray-500" size="18"></i>
                                <input id="password" type="password" name="password" required placeholder="Create a strong password" value="TestPass123!" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 pl-10 text-sm focus:border-brand-accent focus:outline-none">
                                <p id="passwordError" class="mt-2 text-xs text-red-400 hidden">Password must be at least 8 characters.</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Confirm Password</label>
                            <div class="relative">
                                <i data-lucide="lock" class="absolute left-3 top-3 text-gray-500" size="18"></i>
                                <input id="confirmPassword" type="password" name="confirm_password" required placeholder="Repeat your password" value="TestPass123!" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 pl-10 text-sm focus:border-brand-accent focus:outline-none">
                                <p id="confirmError" class="mt-2 text-xs text-red-400 hidden">Passwords do not match.</p>
                            </div>
                        </div>
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

    <script src="../js/sign_up.js"></script>
</body>
</html>