<?php
session_start();
// Enable Error Reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/controllers/venue_controller.php';

// 1. Force Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Fetch User's Venues
$my_venues = get_my_venues_ctr();
$total_venues = count($my_venues);
$active_count = 0;
foreach($my_venues as $v) { if($v['is_active']) $active_count++; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Venues - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { brand: { dark: '#0f0f13', card: '#1a1a23', accent: '#3dff92', purple: '#7000ff' } }, fontFamily: { sans: ['Inter', 'sans-serif'] } } } }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap'); 
        body { font-family: 'Inter', sans-serif; background-color: #0f0f13; color: white; }
    </style>
</head>
<body class="selection:bg-brand-accent selection:text-black bg-brand-dark min-h-screen flex flex-col">

    <!-- Navbar -->
    <nav class="sticky top-0 z-50 border-b border-white/5 bg-brand-card/80 backdrop-blur-xl px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-4">
            <h1 class="text-xl font-black text-white tracking-tighter">MY<span class="text-brand-purple">VENUES</span></h1>
            <div class="h-6 w-px bg-white/10 mx-2 hidden sm:block"></div>
            <a href="venue-portal.php" class="hidden sm:flex items-center gap-2 text-xs font-bold text-gray-400 hover:text-white transition-colors uppercase tracking-wider">
                <i data-lucide="layout-grid" size="14"></i> Public Portal
            </a>
        </div>
        
        <div class="flex items-center gap-4">
            <a href="homepage.php" class="text-sm font-bold text-gray-400 hover:text-white transition-colors">Back to App</a>
            <a href="create_venue.php" class="flex items-center gap-2 px-4 py-2 bg-brand-purple hover:bg-purple-600 shadow-lg shadow-brand-purple/20 rounded-lg text-sm font-bold transition-all transform hover:scale-105">
                <i data-lucide="plus" size="16"></i> Add Venue
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="flex-1 max-w-7xl mx-auto px-4 py-12 w-full">
        
        <!-- Header & Stats -->
        <div class="flex flex-col md:flex-row justify-between items-end mb-10 gap-6">
            <div>
                <h2 class="text-3xl md:text-4xl font-black text-white mb-2">Management Console</h2>
                <p class="text-gray-400">Overview of your sports facilities and performance.</p>
            </div>
            
            <!-- Mini Stats -->
            <div class="flex gap-4">
                <div class="px-5 py-3 bg-brand-card border border-white/5 rounded-xl">
                    <span class="block text-[10px] text-gray-500 uppercase font-bold tracking-wider">Total Venues</span>
                    <span class="text-2xl font-black text-white"><?php echo $total_venues; ?></span>
                </div>
                <div class="px-5 py-3 bg-brand-card border border-white/5 rounded-xl">
                    <span class="block text-[10px] text-gray-500 uppercase font-bold tracking-wider">Active</span>
                    <span class="text-2xl font-black text-brand-accent"><?php echo $active_count; ?></span>
                </div>
            </div>
        </div>

        <?php if (empty($my_venues)): ?>
            <!-- Empty State -->
            <div class="flex flex-col items-center justify-center py-32 bg-brand-card/50 rounded-3xl border-2 border-dashed border-white/5">
                <div class="w-20 h-20 bg-white/5 rounded-full flex items-center justify-center mb-6 animate-pulse">
                    <i data-lucide="map-pin-off" size="40" class="text-gray-600"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">No Venues Listed</h3>
                <p class="text-gray-400 text-sm mb-8 max-w-xs text-center leading-relaxed">
                    You haven't listed any pitches yet. Start earning revenue by adding your first venue to the network.
                </p>
                <a href="create_venue.php" class="inline-flex items-center gap-2 px-8 py-4 bg-brand-accent text-black font-bold rounded-xl hover:bg-[#2fe080] transition-all shadow-lg shadow-brand-accent/10">
                    <i data-lucide="plus-circle" size="20"></i> List Your First Venue
                </a>
            </div>
        <?php else: ?>
            <!-- Venue Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($my_venues as $venue): ?>
                    <div class="bg-brand-card rounded-2xl border border-white/5 overflow-hidden hover:border-brand-purple/50 transition-all duration-300 hover:shadow-2xl hover:shadow-brand-purple/10 flex flex-col group relative">
                        
                        <!-- Image Area -->
                        <div class="h-56 relative overflow-hidden">
                            <img src="<?php echo htmlspecialchars($venue['cover_image']); ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-brand-card via-transparent to-transparent opacity-90"></div>
                            
                            <!-- Status Badge -->
                            <div class="absolute top-4 right-4">
                                <?php if($venue['is_active']): ?>
                                    <span class="px-3 py-1 bg-green-500/10 text-green-400 backdrop-blur-md rounded-full text-[10px] font-bold border border-green-500/20 flex items-center gap-1.5 shadow-sm">
                                        <span class="relative flex h-2 w-2">
                                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                          <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                        </span>
                                        LIVE
                                    </span>
                                <?php else: ?>
                                    <span class="px-3 py-1 bg-yellow-500/10 text-yellow-400 backdrop-blur-md rounded-full text-[10px] font-bold border border-yellow-500/20 shadow-sm flex items-center gap-1.5">
                                        <i data-lucide="clock" size="10"></i> PENDING
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6 flex-1 flex flex-col -mt-12 relative z-10">
                            <div class="mb-4">
                                <h3 class="font-bold text-xl mb-1 text-white leading-tight"><?php echo htmlspecialchars($venue['name']); ?></h3>
                                <p class="text-xs text-gray-400 flex items-center gap-1.5 truncate">
                                    <i data-lucide="map-pin" size="12" class="text-brand-purple"></i> <?php echo htmlspecialchars($venue['address']); ?>
                                </p>
                            </div>
                            
                            <!-- Quick Stats -->
                            <div class="grid grid-cols-2 gap-3 mb-6">
                                <div class="bg-black/40 rounded-lg p-3 border border-white/5">
                                    <span class="block text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Rate</span>
                                    <span class="text-base font-mono font-bold text-brand-accent">₵<?php echo number_format($venue['cost_per_hour'], 0); ?><span class="text-xs text-gray-500 font-normal">/hr</span></span>
                                </div>
                                <div class="bg-black/40 rounded-lg p-3 border border-white/5">
                                    <span class="block text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Capacity</span>
                                    <span class="text-base font-mono font-bold text-white flex items-center gap-1">
                                        <i data-lucide="users" size="14" class="text-gray-500"></i> <?php echo $venue['capacity']; ?>
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="mt-auto grid grid-cols-6 gap-3">
                                <!-- Edit Button -->
                                <a href="edit_venue.php?id=<?php echo $venue['venue_id']; ?>" class="col-span-3 py-3 bg-white/5 hover:bg-white/10 text-white rounded-xl text-xs font-bold text-center border border-white/5 transition-colors flex items-center justify-center gap-2 group/edit">
                                    <i data-lucide="settings-2" size="14" class="group-hover/edit:rotate-45 transition-transform text-brand-purple"></i> Manage
                                </a>
                                
                                <!-- Delete Button -->
                                <button onclick="openDeleteModal(<?php echo $venue['venue_id']; ?>, '<?php echo addslashes(htmlspecialchars($venue['name'])); ?>')" class="col-span-1 py-3 bg-red-500/10 hover:bg-red-500/20 text-red-500 rounded-xl text-xs font-bold text-center border border-red-500/20 transition-colors flex items-center justify-center">
                                    <i data-lucide="trash-2" size="14"></i>
                                </button>
                                
                                <!-- View Page Link -->
                                <a href="venue-profile.php?id=<?php echo $venue['venue_id']; ?>" class="col-span-2 py-3 bg-brand-purple/10 hover:bg-brand-purple/20 text-brand-purple border border-brand-purple/20 hover:border-brand-purple/50 rounded-xl text-xs font-bold text-center transition-all flex items-center justify-center gap-2">
                                    <i data-lucide="arrow-up-right" size="14"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="border-t border-white/5 bg-[#0a0a0d] py-8 mt-auto">
        <div class="max-w-7xl mx-auto px-4 flex flex-col md:flex-row justify-between items-center text-xs text-gray-600">
            <p>&copy; 2025 Haaah Sports. Venue Partner Program.</p>
            <div class="flex gap-4 mt-2 md:mt-0">
                <a href="#" class="hover:text-white">Partner Help</a>
                <a href="#" class="hover:text-white">Terms</a>
            </div>
        </div>
    </footer>

    <!-- DELETE CONFIRMATION MODAL -->
    <div id="delete-modal" class="fixed inset-0 z-[100] bg-black/80 backdrop-blur-sm hidden flex items-center justify-center opacity-0 transition-opacity duration-300">
        <div class="bg-brand-card border border-white/10 rounded-2xl p-8 max-w-sm w-full shadow-2xl transform transition-all scale-95" id="delete-modal-content">
            <div class="text-center">
                <div class="w-16 h-16 bg-red-500/10 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4 border border-red-500/20">
                    <i data-lucide="alert-triangle" class="w-8 h-8"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Delete Venue?</h3>
                <p class="text-gray-400 text-sm mb-6">
                    Are you sure you want to delete <strong id="delete-venue-name" class="text-white">this venue</strong>? This action cannot be undone.
                </p>
                
                <form action="../actions/delete_venue_action.php" method="POST" class="flex gap-3">
                    <input type="hidden" name="venue_id" id="delete-venue-id">
                    <button type="button" onclick="closeDeleteModal()" class="flex-1 py-3 bg-white/5 hover:bg-white/10 text-white font-bold rounded-xl transition-colors border border-white/5">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 py-3 bg-red-500 hover:bg-red-600 text-white font-bold rounded-xl transition-colors shadow-lg shadow-red-500/20">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function openDeleteModal(id, name) {
            const modal = document.getElementById('delete-modal');
            const content = document.getElementById('delete-modal-content');
            
            document.getElementById('delete-venue-id').value = id;
            document.getElementById('delete-venue-name').textContent = name;
            
            modal.classList.remove('hidden');
            // Small delay for transition
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                content.classList.remove('scale-95');
                content.classList.add('scale-100');
            }, 10);
        }

        function closeDeleteModal() {
            const modal = document.getElementById('delete-modal');
            const content = document.getElementById('delete-modal-content');
            
            modal.classList.add('opacity-0');
            content.classList.add('scale-95');
            content.classList.remove('scale-100');
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
    </script>
</body>
</html>