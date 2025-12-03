<?php
session_start();
require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/controllers/venue_controller.php';
require_once PROJECT_ROOT . '/controllers/user_controller.php';

// 1. Fetch User Data
$initials = 'U';
$is_logged_in = isset($_SESSION['user_id']);

if ($is_logged_in) {
    $userController = new UserController();
    $current_user = $userController->get_user_by_id_ctr($_SESSION['user_id']);
    if ($current_user) {
        $initials = strtoupper(substr($current_user['user_name'], 0, 1));
    }
}

// 2. Fetch All Venues
$venues = get_all_venues_ctr(); 
$venue_count = is_array($venues) ? count($venues) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Venue Portal - Haaah Sports</title>
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
<body class="selection:bg-brand-accent selection:text-black">

    <!-- Navbar -->
    <nav class="border-b border-white/5 bg-brand-card px-6 py-4 flex justify-between items-center sticky top-0 z-50">
        <div class="flex items-center gap-4">
            <a href="homepage.php" class="flex items-center gap-2 text-gray-400 hover:text-white transition-colors">
                <i data-lucide="arrow-left" size="20"></i> <span class="font-bold hidden sm:inline">Home</span>
            </a>
            <h1 class="text-xl font-black tracking-tighter text-transparent bg-clip-text bg-gradient-to-r from-brand-accent to-[#00c6ff]">
                VENUE<span class="text-white text-sm font-normal tracking-widest ml-1">PORTAL</span>
            </h1>
        </div>
        
        <div class="flex items-center gap-4">
            <?php if($is_logged_in): ?>
                <!-- Link to Management Dashboard -->
                <a href="manage_venues.php" class="hidden sm:flex items-center gap-2 px-4 py-2 bg-brand-card border border-white/10 hover:border-brand-purple hover:text-brand-purple text-gray-300 font-bold rounded-full text-sm transition-all">
                    <i data-lucide="layout-dashboard" size="16"></i> My Venues
                </a>
            <?php endif; ?>

            <a href="create_venue.php" class="hidden sm:flex items-center gap-2 px-4 py-2 bg-brand-accent text-black font-bold rounded-full text-sm hover:bg-[#2fe080] transition-colors">
                <i data-lucide="plus" size="16"></i> List Venue
            </a>
            
            <?php if($is_logged_in): ?>
                <div class="w-9 h-9 rounded-full bg-purple-600 flex items-center justify-center font-bold text-sm text-white border-2 border-brand-card">
                    <?php echo $initials; ?>
                </div>
            <?php else: ?>
                <a href="login.php" class="text-sm font-bold text-white hover:text-brand-accent">Login</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 lg:px-8 py-10">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-end mb-10 gap-4">
            <div>
                <h2 class="text-3xl font-black text-white mb-2">Find Your Pitch</h2>
                <p class="text-gray-400">Discover <?php echo $venue_count; ?> top-rated venues available for booking today.</p>
            </div>
            
            <div class="flex gap-2">
                <div class="relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500" size="16"></i>
                    <input type="text" placeholder="Search locations..." class="pl-10 pr-4 py-2 bg-brand-card border border-white/10 rounded-xl text-sm focus:outline-none focus:border-brand-accent transition-colors w-48 md:w-64 text-white">
                </div>
                <button class="p-2 bg-brand-card border border-white/10 rounded-xl hover:bg-white/5 transition-colors text-gray-400 hover:text-white">
                    <i data-lucide="filter" size="20"></i>
                </button>
            </div>
        </div>

        <!-- Venue Grid -->
        <?php if (!empty($venues)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($venues as $venue): ?>
                    <?php 
                        $v_id = $venue['venue_id'];
                        $v_name = htmlspecialchars($venue['name']);
                        $v_addr = htmlspecialchars($venue['address']);
                        $v_img = htmlspecialchars($venue['cover_image']);
                        $v_rate = number_format(floatval($venue['cost_per_hour']), 2);
                        $v_rating = isset($venue['rating']) ? number_format($venue['rating'], 1) : null;
                        $link = "venue-profile.php?id=$v_id";
                    ?>
                    
                    <a href="<?php echo $link; ?>" class="group bg-brand-card rounded-2xl border border-white/5 overflow-hidden hover:border-brand-accent/50 transition-all duration-300 hover:-translate-y-1 shadow-lg hover:shadow-brand-accent/10 flex flex-col">
                        <div class="relative h-48 w-full overflow-hidden">
                            <img src="<?php echo $v_img; ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-brand-card via-transparent to-transparent opacity-80"></div>
                            
                            <?php if ($v_rating): ?>
                                <div class="absolute top-3 right-3 bg-black/60 backdrop-blur-md px-2 py-1 rounded-lg flex items-center gap-1 border border-white/10">
                                    <span class="text-xs font-bold text-white"><?php echo $v_rating; ?></span>
                                    <i data-lucide="star" size="10" class="text-yellow-500 fill-yellow-500"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="p-5 flex-1 flex flex-col">
                            <div class="mb-auto">
                                <h3 class="font-bold text-lg text-white mb-1 group-hover:text-brand-accent transition-colors line-clamp-1"><?php echo $v_name; ?></h3>
                                <p class="text-xs text-gray-400 flex items-center gap-1.5 mb-3 line-clamp-1">
                                    <i data-lucide="map-pin" size="12" class="text-brand-accent"></i> <?php echo $v_addr; ?>
                                </p>
                                
                                <div class="flex gap-2 mb-4">
                                    <?php 
                                        $badges = array_slice($venue['amenities'], 0, 2);
                                        if (empty($badges)) echo '<span class="text-[10px] text-gray-600 italic">No amenities listed</span>';
                                        foreach($badges as $badge): 
                                    ?>
                                        <span class="text-[10px] px-2 py-1 rounded bg-white/5 text-gray-400 border border-white/5"><?php echo htmlspecialchars($badge); ?></span>
                                    <?php endforeach; ?>
                                    <?php if(count($venue['amenities']) > 2): ?>
                                        <span class="text-[10px] px-2 py-1 rounded bg-white/5 text-gray-500">+<?php echo count($venue['amenities']) - 2; ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-white/5 flex justify-between items-center mt-2">
                                <div>
                                    <span class="block text-[10px] text-gray-500 font-bold uppercase tracking-wider">Rate</span>
                                    <span class="text-brand-accent font-bold">GHS <?php echo $v_rate; ?><span class="text-xs text-gray-500 font-normal">/hr</span></span>
                                </div>
                                <div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center group-hover:bg-brand-accent group-hover:text-black transition-colors">
                                    <i data-lucide="arrow-right" size="16"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-20 bg-brand-card rounded-2xl border border-white/5 border-dashed">
                <i data-lucide="map-pin-off" size="32" class="mx-auto mb-4 text-gray-600"></i>
                <h3 class="text-xl font-bold text-white mb-2">No Venues Found</h3>
                <p class="text-gray-400 text-sm max-w-xs mx-auto mb-6">We couldn't find any venues listed yet.</p>
                <a href="create_venue.php" class="inline-flex items-center gap-2 px-6 py-3 bg-brand-accent text-black font-bold rounded-xl hover:bg-[#2fe080] transition-colors">
                    Register a Venue
                </a>
            </div>
        <?php endif; ?>

    </div>

    <!-- Footer -->
    <footer class="border-t border-white/5 mt-12 bg-[#0a0a0d] py-12 px-8">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center text-xs text-gray-600">
            <p>&copy; 2025 Haaah Sports Ghana.</p>
            <div class="flex gap-6 mt-4 md:mt-0">
                <a href="#" class="hover:text-white">Privacy</a>
                <a href="#" class="hover:text-white">Terms</a>
                <a href="#" class="hover:text-white">Support</a>
            </div>
        </div>
    </footer>

    <script>lucide.createIcons();</script>
</body>
</html>