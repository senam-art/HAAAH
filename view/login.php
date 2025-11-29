<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Haaah Sports</title>
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
        <img src="https://images.unsplash.com/photo-1579952363873-27f3bade9f55?auto=format&fit=crop&q=80&w=1600" class="w-full h-full object-cover opacity-20 grayscale">
        <div class="absolute inset-0 bg-gradient-to-t from-brand-dark via-brand-dark/90 to-brand-dark/50"></div>
    </div>

    <!-- Nav -->
    <nav class="relative z-10 px-6 py-6">
        <a href="landing.html" class="font-black tracking-tighter text-2xl text-white">
            HAAAH<span class="text-brand-accent text-base font-normal tracking-widest ml-1">SPORTS</span>
        </a>
    </nav>

    <!-- Main Content -->
    <main class="flex-1 flex items-center justify-center px-4 relative z-10">
        <div class="w-full max-w-md bg-brand-card/90 backdrop-blur-xl border border-white/10 rounded-3xl p-8 shadow-2xl">
            
            <div class="text-center mb-8">
                <h1 class="text-3xl font-black mb-2">Welcome Back</h1>
                <p class="text-gray-400 text-sm">Enter your details to access your games.</p>
            </div>

            <form class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email Address</label>
                    <div class="relative">
                        <i data-lucide="mail" class="absolute left-3 top-3 text-gray-500" size="18"></i>
                        <input type="email" placeholder="you@example.com" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 pl-10 text-sm focus:border-brand-accent focus:outline-none transition-colors">
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-xs font-bold text-gray-500 uppercase">Password</label>
                        <a href="#" class="text-xs text-brand-accent hover:underline">Forgot?</a>
                    </div>
                    <div class="relative">
                        <i data-lucide="lock" class="absolute left-3 top-3 text-gray-500" size="18"></i>
                        <input type="password" placeholder="••••••••" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 pl-10 text-sm focus:border-brand-accent focus:outline-none transition-colors">
                    </div>
                </div>

                <button type="submit" class="w-full bg-brand-accent hover:bg-[#2fe080] text-black font-bold py-3 rounded-xl transition-transform hover:scale-[1.02] shadow-lg shadow-brand-accent/20">
                    Sign In
                </button>
            </form>

            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-white/10"></div></div>
                <div class="relative flex justify-center text-xs uppercase"><span class="bg-brand-card px-2 text-gray-500">Or continue with</span></div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <button class="flex items-center justify-center gap-2 bg-white/5 hover:bg-white/10 border border-white/10 py-2.5 rounded-xl transition-colors">
                    <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5">
                    <span class="text-sm font-medium">Google</span>
                </button>
                <button class="flex items-center justify-center gap-2 bg-white/5 hover:bg-white/10 border border-white/10 py-2.5 rounded-xl transition-colors">
                    <img src="https://www.svgrepo.com/show/475647/facebook-color.svg" class="w-5 h-5">
                    <span class="text-sm font-medium">Facebook</span>
                </button>
            </div>

            <p class="mt-8 text-center text-sm text-gray-400">
                Don't have an account? 
                <a href="signup.html" class="text-brand-accent font-bold hover:underline">Create one</a>
            </p>
        </div>
    </main>

    <script>lucide.createIcons();</script>
</body>
</html>