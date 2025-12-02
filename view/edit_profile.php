<?php
// 1. Bootstrap Core
require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/controllers/user_controller.php';

// 2. Security
check_login();

// 3. Fetch Current Data to Pre-fill Form
$user_id = get_user_id();
$userController = new UserController();
$user_data = $userController->get_user_by_id_ctr($user_id);

if (!$user_data) die("User not found.");

// 4. Parse Existing Tags
$current_tags = ['positions' => [], 'traits' => []];
if (!empty($user_data['profile_details'])) {
    $decoded = json_decode($user_data['profile_details'], true);
    if (is_array($decoded)) $current_tags = array_merge($current_tags, $decoded);
}

// 5. Define Available Options (For the checkboxes)
$available_positions = ['Goalkeeper', 'Defender', 'Midfielder', 'Forward', 'Winger', 'Striker'];
$available_traits = ['Competitive', 'Fun', 'Vocal', 'Playmaker', 'Speedster', 'Tactical'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Haaah Sports</title>
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
<body class="min-h-screen bg-brand-dark pb-20 selection:bg-brand-accent selection:text-black">

    <nav class="border-b border-white/5 bg-brand-dark px-6 py-4 sticky top-0 z-50">
        <div class="max-w-3xl mx-auto flex items-center justify-between">
            <a href="profile.php" class="flex items-center gap-2 text-gray-400 hover:text-white transition-colors text-sm font-bold">
                <i data-lucide="arrow-left" size="18"></i> Cancel
            </a>
            <span class="font-bold text-lg">Edit Profile</span>
            <div class="w-16"></div> <!-- Spacer for center alignment -->
        </div>
    </nav>

    <div class="max-w-3xl mx-auto px-4 py-8">
        
        <?php if (isset($_GET['error'])): ?>
            <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-sm font-bold text-center">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <form action="../actions/edit_profile_action.php" method="POST" class="space-y-8">
            
            <!-- Section 1: Basic Info -->
            <div class="bg-brand-card p-6 rounded-2xl border border-white/5">
                <h3 class="text-xl font-black mb-6 flex items-center gap-2">
                    <i data-lucide="user" class="text-brand-accent" size="20"></i> Basic Info
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">First Name</label>
                        <input type="text" name="first_name" value="<?php echo htmlspecialchars($user_data['first_name']); ?>" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 text-white focus:border-brand-accent focus:outline-none transition-colors" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Last Name</label>
                        <input type="text" name="last_name" value="<?php echo htmlspecialchars($user_data['last_name']); ?>" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 text-white focus:border-brand-accent focus:outline-none transition-colors" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Username</label>
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-gray-500">@</span>
                            <input type="text" name="user_name" value="<?php echo htmlspecialchars($user_data['user_name']); ?>" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 pl-8 text-white focus:border-brand-accent focus:outline-none transition-colors" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Email (Cannot Change)</label>
                        <input type="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" class="w-full bg-white/5 border border-white/5 rounded-xl p-3 text-gray-500 cursor-not-allowed" disabled>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Location / City</label>
                        <input type="text" name="location" value="<?php echo htmlspecialchars($user_data['location']); ?>" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 text-white focus:border-brand-accent focus:outline-none transition-colors" placeholder="e.g. Accra, Ghana">
                    </div>
                </div>
            </div>

            <!-- Section 2: Player Profile Tags -->
            <div class="bg-brand-card p-6 rounded-2xl border border-white/5">
                <h3 class="text-xl font-black mb-2 flex items-center gap-2">
                    <i data-lucide="tag" class="text-brand-purple" size="20"></i> Player Tags
                </h3>
                <p class="text-gray-400 text-sm mb-6">Select tags that describe your playstyle. These appear on your profile.</p>

                <!-- Positions -->
                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-3">Preferred Positions</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <?php foreach ($available_positions as $pos): ?>
                            <?php $checked = in_array($pos, $current_tags['positions']) ? 'checked' : ''; ?>
                            <label class="flex items-center gap-3 p-3 bg-brand-dark border border-white/10 rounded-xl cursor-pointer hover:border-white/30 transition-colors">
                                <input type="checkbox" name="positions[]" value="<?php echo $pos; ?>" <?php echo $checked; ?> class="w-4 h-4 rounded border-gray-600 text-brand-accent focus:ring-brand-accent bg-gray-700">
                                <span class="text-sm font-medium"><?php echo $pos; ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Traits -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-3">Personality / Traits</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <?php foreach ($available_traits as $trait): ?>
                            <?php $checked = in_array($trait, $current_tags['traits']) ? 'checked' : ''; ?>
                            <label class="flex items-center gap-3 p-3 bg-brand-dark border border-white/10 rounded-xl cursor-pointer hover:border-white/30 transition-colors">
                                <input type="checkbox" name="traits[]" value="<?php echo $trait; ?>" <?php echo $checked; ?> class="w-4 h-4 rounded border-gray-600 text-brand-purple focus:ring-brand-purple bg-gray-700">
                                <span class="text-sm font-medium"><?php echo $trait; ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex justify-end gap-4">
                <a href="profile.php" class="px-6 py-3 rounded-xl font-bold text-gray-400 hover:text-white transition-colors">Cancel</a>
                <button type="submit" class="px-8 py-3 bg-brand-accent text-black font-bold rounded-xl hover:bg-[#2fe080] transition-transform hover:scale-105 shadow-lg shadow-brand-accent/20">
                    Save Changes
                </button>
            </div>

        </form>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>