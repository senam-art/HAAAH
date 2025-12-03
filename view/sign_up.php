<?php 
require_once __DIR__ . '/../settings/core.php'; 
// redirectIfLoggedIn() // Uncomment if your core.php is set up
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Account - Haaah Sports</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { brand: { dark: '#0f0f13', card: '#1a1a23', accent: '#3dff92', purple: '#7000ff' } },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap'); body { font-family: 'Inter', sans-serif; background-color: #0f0f13; color: white; }</style>
</head>
<body class="selection:bg-brand-accent selection:text-black min-h-screen flex flex-col relative pb-20">

    <nav class="relative z-10 px-6 py-6 flex justify-between items-center">
        <a href="../landing.php" class="font-black tracking-tighter text-2xl text-white">HAAAH<span class="text-brand-accent text-base font-normal tracking-widest ml-1">SPORTS</span></a>
        <a href="login.php" class="text-sm font-bold text-gray-400 hover:text-white transition-colors">Sign In</a>
    </nav>

    <main class="flex-1 flex items-center justify-center px-4 relative z-10 py-8">
        <div class="w-full max-w-lg bg-brand-card/90 backdrop-blur-xl border border-white/10 rounded-3xl p-8 shadow-2xl">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-black mb-2">Join the Squad</h1>
                <p class="text-gray-400 text-sm">Create your profile to get started.</p>
            </div>

            <form id="signupForm" class="space-y-5" novalidate>
                <div id="formMessage" class="hidden"></div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">I am a...</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer relative">
                            <input type="radio" name="role" value="0" checked class="peer sr-only">
                            <div class="p-4 rounded-xl bg-brand-dark border border-white/10 text-center transition-all peer-checked:border-brand-accent peer-checked:bg-brand-accent/10 peer-checked:text-brand-accent hover:bg-white/5">
                                <i data-lucide="user" class="mx-auto mb-1" size="20"></i>
                                <span class="text-sm font-bold">Player</span>
                            </div>
                        </label>
                        <label class="cursor-pointer relative">
                            <input type="radio" name="role" value="1" class="peer sr-only">
                            <div class="p-4 rounded-xl bg-brand-dark border border-white/10 text-center transition-all peer-checked:border-brand-purple peer-checked:bg-brand-purple/10 peer-checked:text-brand-purple hover:bg-white/5">
                                <i data-lucide="building-2" class="mx-auto mb-1" size="20"></i>
                                <span class="text-sm font-bold">Venue Manager</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">First Name</label>
                        <input name="first_name" required type="text" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 text-sm focus:border-brand-accent focus:outline-none text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Last Name</label>
                        <input name="last_name" required type="text" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 text-sm focus:border-brand-accent focus:outline-none text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Username</label>
                    <input name="user_name" required type="text" placeholder="@username" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 text-sm focus:border-brand-accent focus:outline-none text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email</label>
                    <input name="email" required type="email" placeholder="you@example.com" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 text-sm focus:border-brand-accent focus:outline-none text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Location</label>
                    <input name="location" required type="text" placeholder="e.g. East Legon" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 text-sm focus:border-brand-accent focus:outline-none text-white">
                </div>

                <div id="playerAttributesSection" class="bg-black/20 p-5 rounded-2xl border border-white/5 space-y-4 animate-fade-in transition-all duration-300">
                    <div class="flex items-center gap-2 mb-2">
                        <i data-lucide="shirt" class="text-brand-accent" size="18"></i>
                        <span class="text-sm font-bold text-white">Player Stats</span>
                    </div>
                    
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2">Positions</label>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach(['GK', 'DEF', 'MID', 'FWD', 'Winger', 'Striker'] as $pos): ?>
                            <label class="cursor-pointer">
                                <input type="checkbox" name="positions[]" value="<?php echo $pos; ?>" class="peer sr-only">
                                <span class="px-3 py-1.5 rounded-lg bg-white/5 border border-white/10 text-xs text-gray-400 peer-checked:bg-brand-accent peer-checked:text-black peer-checked:font-bold transition-all hover:bg-white/10 select-none"><?php echo $pos; ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2">Traits</label>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach(['Vocal', 'Speed', 'Tactical', 'Captain', 'Playmaker'] as $trait): ?>
                            <label class="cursor-pointer">
                                <input type="checkbox" name="traits[]" value="<?php echo $trait; ?>" class="peer sr-only">
                                <span class="px-3 py-1.5 rounded-lg bg-white/5 border border-white/10 text-xs text-gray-400 peer-checked:bg-brand-purple peer-checked:text-white peer-checked:font-bold transition-all hover:bg-white/10 select-none"><?php echo $trait; ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Password</label>
                        <input id="password" type="password" name="password" required class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 text-sm focus:border-brand-accent focus:outline-none text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Confirm</label>
                        <input id="confirmPassword" type="password" name="confirm_password" required class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 text-sm focus:border-brand-accent focus:outline-none text-white">
                    </div>
                </div>

                <button type="submit" id="submitBtn" class="w-full bg-brand-accent hover:bg-[#2fe080] text-black font-bold py-3.5 rounded-xl transition-all shadow-lg shadow-brand-accent/20 mt-4 flex items-center justify-center gap-2">
                    Create Account
                </button>
            </form>
        </div>
    </main>

    <script src="../js/sign_up.js"></script> 
    
    <script>
        // Only initializing icons here. Logic is in the external file.
        lucide.createIcons();
    </script>
</body>
</html>