<?php
// 1. Bootstrap Core
require_once __DIR__ . '/../settings/core.php';

// 2. Load Logic
require_once PROJECT_ROOT . '/actions/get_profile_data.php';

// 3. Determine Profile Picture
$profile_pic_path = $profile_tags['profile_image'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $username_display; ?> - Haaah Profile</title>
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
    <nav class="border-b border-white/5 bg-brand-dark px-6 py-4 flex justify-between items-center sticky top-0 z-50 backdrop-blur-md bg-opacity-95">
        <a href="homepage.php" class="font-black tracking-tighter text-xl flex items-center gap-2 hover:opacity-80 transition-opacity">
            HAAAH<span class="text-gray-500 text-xs font-normal">SPORTS</span>
        </a>
        <div class="flex items-center gap-4">
            <?php if ($is_own_profile): ?>
                <a href="dashboard.php" class="hidden sm:flex items-center gap-2 px-4 py-2 bg-white/5 border border-white/10 rounded-full text-xs font-bold hover:bg-white/10 transition-colors">
                    <i data-lucide="layout-dashboard" size="14"></i> Switch to Organizer
                </a>
            <?php endif; ?>
            
            <!-- Small Nav Avatar -->
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-brand-accent to-brand-purple flex items-center justify-center font-bold text-sm text-black overflow-hidden">
                <?php if ($profile_pic_path): ?>
                    <img src="<?php echo htmlspecialchars($profile_pic_path); ?>" class="w-full h-full object-cover">
                <?php else: ?>
                    <?php echo $initials; ?>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="max-w-5xl mx-auto px-4 py-8">

        <!-- Error/Success Messages -->
        <?php if (isset($_GET['error'])): ?>
            <?php
                // Map error codes to friendly messages
                $error_code = $_GET['error'];
                $error_msg = "An unknown error occurred.";
                
                switch($error_code) {
                    case 'directory_create_failed':
                        $error_msg = "Server Error: Could not create upload directory. Please contact support.";
                        break;
                    case 'upload_failed':
                        $error_msg = "Upload failed. Please select a valid file.";
                        break;
                    case 'invalid_file_type':
                        $error_msg = "Invalid file type. Please use JPG, PNG, or WEBP.";
                        break;
                    case 'file_too_large':
                        $error_msg = "File is too large. Maximum size is 5MB.";
                        break;
                    case 'db_update_failed':
                        $error_msg = "Image uploaded, but database update failed.";
                        break;
                    case 'move_failed':
                        $error_msg = "Failed to save file to server.";
                        break;
                    default:
                        $error_msg = "Error: " . htmlspecialchars($error_code);
                }
            ?>
            <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-sm font-bold text-center">
                <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'avatar_updated'): ?>
            <div class="mb-6 p-4 bg-green-500/10 border border-green-500/20 rounded-xl text-green-400 text-sm font-bold text-center">
                Profile picture updated successfully!
            </div>
        <?php endif; ?>

        <!-- Profile Header -->
        <div class="relative bg-brand-card rounded-3xl p-8 mb-8 border border-white/5 overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-brand-accent/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row items-center md:items-start gap-8">
                
                <!-- Main Avatar -->
                <div class="relative group flex-shrink-0">
                    <div class="w-32 h-32 rounded-full bg-gradient-to-br from-brand-purple to-blue-600 p-1">
                        <div class="w-full h-full rounded-full bg-brand-card flex items-center justify-center text-4xl font-black text-white overflow-hidden relative">
                            <?php if ($profile_pic_path): ?>
                                <img src="<?php echo htmlspecialchars($profile_pic_path); ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <?php echo $initials; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if ($is_own_profile): ?>
                        <!-- Camera Button (Triggers Upload) -->
                        <form id="avatarForm" action="../actions/upload_image_action.php" method="POST" enctype="multipart/form-data">
                            <label class="absolute bottom-0 right-2 p-2 bg-brand-card border border-white/10 rounded-full text-brand-accent hover:text-white transition-colors cursor-pointer shadow-lg">
                                <i data-lucide="camera" size="16"></i>
                                <input type="file" name="profile_pic" accept="image/*" class="hidden" onchange="document.getElementById('avatarForm').submit();">
                            </label>
                        </form>
                    <?php endif; ?>
                </div>

                <!-- Info -->
                <div class="flex-1 text-center md:text-left">
                    <div class="flex flex-col md:flex-row items-center md:justify-between mb-4">
                        <div>
                            <h1 class="text-3xl font-black mb-1"><?php echo $full_name; ?></h1>
                            <p class="text-gray-400 flex items-center justify-center md:justify-start gap-2 text-sm">
                                <span class="text-brand-accent font-bold"><?php echo $username_display; ?></span>
                                <span class="w-1 h-1 rounded-full bg-gray-600"></span>
                                <span>Member since <?php echo $join_date; ?></span>
                            </p>
                        </div>
                        
                        <?php if ($is_own_profile): ?>
                            <a href="edit_profile.php" class="mt-4 md:mt-0 px-6 py-2 border border-white/20 rounded-xl font-bold text-sm hover:bg-white/5 transition-colors inline-block">
                                Edit Profile
                            </a>
                        <?php else: ?>
                            <button class="mt-4 md:mt-0 px-6 py-2 bg-brand-accent text-black rounded-xl font-bold text-sm hover:bg-brand-accent/90 transition-colors flex items-center gap-2">
                                <i data-lucide="message-circle" size="16"></i> Message
                            </button>
                        <?php endif; ?>
                    </div>

                    <!-- Tags -->
                    <div class="flex flex-wrap justify-center md:justify-start gap-2 mb-2">
                        <?php if (!empty($profile_tags['positions'])): ?>
                            <?php foreach ($profile_tags['positions'] as $position): ?>
                                <span class="px-3 py-1 rounded-lg bg-white/5 text-xs font-bold text-gray-300 flex items-center gap-1">
                                    ⚽ <?php echo htmlspecialchars($position); ?>
                                </span>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?php if (!empty($profile_tags['traits'])): ?>
                            <?php foreach ($profile_tags['traits'] as $trait): ?>
                                <span class="px-3 py-1 rounded-lg bg-brand-accent/10 text-xs font-bold text-brand-accent flex items-center gap-1">
                                    🔥 <?php echo htmlspecialchars($trait); ?>
                                </span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <?php if (empty($profile_tags['positions']) && empty($profile_tags['traits'])): ?>
                            <span class="text-gray-600 text-xs italic">No tags set yet.</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Stats Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-brand-card p-5 rounded-2xl border border-white/5 text-center">
                <div class="text-3xl font-black mb-1"><?php echo $user_stats['matches']; ?></div>
                <div class="text-xs text-gray-400 font-bold uppercase tracking-wider">Matches</div>
            </div>
            <div class="bg-brand-card p-5 rounded-2xl border border-white/5 text-center">
                <div class="text-3xl font-black mb-1 text-brand-accent"><?php echo $user_stats['goals']; ?></div>
                <div class="text-xs text-gray-400 font-bold uppercase tracking-wider">Goals</div>
            </div>
            <div class="bg-brand-card p-5 rounded-2xl border border-white/5 text-center">
                <div class="text-3xl font-black mb-1 text-brand-purple"><?php echo $user_stats['mvps']; ?></div>
                <div class="text-xs text-gray-400 font-bold uppercase tracking-wider">MVPs</div>
            </div>
            <div class="bg-brand-card p-5 rounded-2xl border border-white/5 text-center">
                <div class="text-3xl font-black mb-1"><?php echo $user_stats['rating']; ?></div>
                <div class="text-xs text-gray-400 font-bold uppercase tracking-wider">Rating</div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: Activity -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Tabs -->
                <div class="flex items-center gap-6 border-b border-white/10 mb-2">
                    <button id="tab-btn-organized" onclick="switchTab('organized')" class="pb-3 border-b-2 border-brand-accent text-white font-bold text-sm flex items-center gap-2 transition-colors">
                        <i data-lucide="calendar-clock" size="16"></i> Organized Events
                    </button>
                    <?php if ($is_own_profile): ?>
                        <button id="tab-btn-booked" onclick="switchTab('booked')" class="pb-3 border-b-2 border-transparent text-gray-400 font-bold text-sm flex items-center gap-2 hover:text-white transition-colors">
                            <i data-lucide="ticket" size="16"></i> Game Committed
                        </button>
                    <?php endif; ?>
                </div>

                <!-- Organized List -->
                <div id="tab-content-organized" class="space-y-4">
                    <?php if (!empty($organized_events)): ?>
                        <?php foreach ($organized_events as $event): ?>
                            <div class="bg-brand-card rounded-xl p-5 border-l-4 <?php echo ($event['is_approved'] == 1) ? 'border-brand-accent' : 'border-yellow-500'; ?> border-y border-r border-white/5 hover:border-r-white/20 transition-all cursor-pointer group">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <span class="px-2 py-1 <?php echo ($event['is_approved'] == 1) ? 'bg-brand-accent/10 text-brand-accent' : 'bg-yellow-500/10 text-yellow-500'; ?> text-[10px] font-bold uppercase tracking-wider rounded">
                                            <?php echo ($event['is_approved'] == 1) ? 'Active' : 'Pending Review'; ?>
                                        </span>
                                        <h4 class="font-bold text-lg mt-2 group-hover:text-brand-accent transition-colors"><?php echo htmlspecialchars($event['title']); ?></h4>
                                        <p class="text-gray-400 text-sm flex items-center gap-1 mt-1">
                                            <i data-lucide="map-pin" size="12"></i> <?php echo isset($event['venue_name']) ? htmlspecialchars($event['venue_name']) : 'Venue TBD'; ?>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-2xl font-black"><?php echo date('H:i', strtotime($event['event_time'])); ?></div>
                                        <div class="text-xs text-gray-500 uppercase font-bold"><?php echo date('M d', strtotime($event['event_date'])); ?></div>
                                    </div>
                                </div>
                                <div class="mt-4 pt-4 border-t border-white/5 flex justify-between items-center">
                                     <div class="text-xs text-gray-500 font-medium">
                                         <?php echo $event['current_players']; ?> / <?php echo $event['min_players']; ?> Players Joined
                                     </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="p-8 text-center border border-dashed border-white/10 rounded-xl">
                            <p class="text-gray-500">No organized events yet.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Booked List (Owner Only) -->
                <?php if ($is_own_profile): ?>
                    <div id="tab-content-booked" class="space-y-4 hidden">
                        <?php if (!empty($booked_events)): ?>
                            <?php foreach ($booked_events as $booking): ?>
                                <div class="bg-brand-card rounded-xl p-5 border-l-4 border-blue-500 border-y border-r border-white/5 hover:border-r-white/20 transition-all cursor-pointer group">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <span class="px-2 py-1 bg-blue-500/10 text-blue-400 text-[10px] font-bold uppercase tracking-wider rounded">
                                                Game Committed
                                            </span>
                                            <h4 class="font-bold text-lg mt-2 group-hover:text-blue-400 transition-colors"><?php echo htmlspecialchars($booking['title']); ?></h4>
                                            <p class="text-gray-400 text-sm mt-1">
                                                Booked on <?php echo isset($booking['booked_at']) ? date('M j, Y', strtotime($booking['booked_at'])) : 'Unknown Date'; ?>
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-xl font-black text-gray-300">GHS <?php echo isset($booking['cost_per_player']) ? number_format($booking['cost_per_player'], 2) : '--'; ?></div>
                                        </div>
                                    </div>
                                    <div class="mt-2 flex items-center gap-2 text-xs text-green-400 font-bold">
                                        <i data-lucide="check-circle" size="14"></i> Payment Verified
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="p-8 text-center border border-dashed border-white/10 rounded-xl">
                                <p class="text-gray-500">No committed games yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right: Settings/Actions -->
            <div class="space-y-6">
                <?php if ($is_own_profile): ?>
                    <div class="flex items-center justify-between">
                         <h3 class="font-bold text-xl text-gray-300">Settings</h3>
                         <span class="text-[10px] font-bold uppercase text-brand-accent bg-brand-accent/10 px-2 py-1 rounded">Coming Soon</span>
                    </div>
                    
                    <div class="bg-brand-card rounded-xl border border-white/5 overflow-hidden opacity-50 grayscale pointer-events-none select-none relative">
                        <div class="absolute inset-0 z-10"></div> 
                        <div class="flex items-center justify-between p-4 border-b border-white/5">
                            <div class="flex items-center gap-3">
                                <i data-lucide="credit-card" size="18" class="text-gray-400"></i>
                                <span class="text-sm font-medium">Payment Methods</span>
                            </div>
                            <i data-lucide="chevron-right" size="16" class="text-gray-500"></i>
                        </div>
                        <div class="flex items-center justify-between p-4">
                            <div class="flex items-center gap-3">
                                <i data-lucide="lock" size="18" class="text-gray-400"></i>
                                <span class="text-sm font-medium">Privacy & Security</span>
                            </div>
                            <i data-lucide="chevron-right" size="16" class="text-gray-500"></i>
                        </div>
                    </div>

                    <div class="bg-red-500/10 rounded-xl p-4 border border-red-500/20 hover:bg-red-500/20 transition-colors cursor-pointer">
                        <a href="../actions/logout.php" class="w-full text-red-400 font-bold text-sm flex items-center justify-center gap-2">
                            <i data-lucide="log-out" size="16"></i> Log Out
                        </a>
                    </div>
                <?php else: ?>
                     <h3 class="font-bold text-xl text-gray-300">Actions</h3>
                     <div class="bg-brand-card rounded-xl border border-white/5 overflow-hidden">
                        <button class="w-full flex items-center justify-between p-4 hover:bg-white/5 transition-colors border-b border-white/5 text-left">
                            <div class="flex items-center gap-3">
                                <i data-lucide="share-2" size="18" class="text-gray-400"></i>
                                <span class="text-sm font-medium">Share Profile</span>
                            </div>
                        </button>
                        <button class="w-full flex items-center justify-between p-4 hover:bg-white/5 transition-colors text-left">
                            <div class="flex items-center gap-3">
                                <i data-lucide="flag" size="18" class="text-red-400"></i>
                                <span class="text-sm font-medium text-red-400">Report User</span>
                            </div>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function switchTab(tabName) {
            document.getElementById('tab-content-organized').classList.add('hidden');
            const bookedContent = document.getElementById('tab-content-booked');
            if (bookedContent) bookedContent.classList.add('hidden');
            
            document.getElementById('tab-btn-organized').classList.remove('border-brand-accent', 'text-white');
            document.getElementById('tab-btn-organized').classList.add('border-transparent', 'text-gray-400');
            
            const bookedBtn = document.getElementById('tab-btn-booked');
            if (bookedBtn) {
                bookedBtn.classList.remove('border-brand-accent', 'text-white');
                bookedBtn.classList.add('border-transparent', 'text-gray-400');
            }

            document.getElementById('tab-content-' + tabName).classList.remove('hidden');
            const activeBtn = document.getElementById('tab-btn-' + tabName);
            if (activeBtn) {
                activeBtn.classList.remove('border-transparent', 'text-gray-400');
                activeBtn.classList.add('border-brand-accent', 'text-white');
            }
        }
    </script>
</body>
</html>