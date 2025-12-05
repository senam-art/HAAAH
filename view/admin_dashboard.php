<?php

require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/controllers/admin_controller.php';

// 1. Security Check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 2) {
    header("Location: login.php");
    exit();
}

// 2. Fetch Data
$stats = get_admin_stats_ctr();
$pending_events = get_pending_events_ctr();
$active_events = get_active_events_ctr(); // Fetch Active Events
$all_venues = get_all_venues_admin_ctr();
$deleted_venues = get_deleted_venues_ctr();

// 3. Capture Flash Message
$flash = isset($_SESSION['flash']) ? $_SESSION['flash'] : null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Haaah Sports</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { brand: { dark: '#0f0f13', card: '#1a1a23', accent: '#3dff92', purple: '#7000ff', red: '#ef4444' } }, fontFamily: { sans: ['Inter', 'sans-serif'] } } } }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap'); 
        body { font-family: 'Inter', sans-serif; background-color: #0f0f13; color: white; }
        .toast-animate { animation: slideIn 0.5s ease-out forwards; }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    </style>
</head>
<body class="selection:bg-brand-accent selection:text-black bg-brand-dark">

    <div class="flex min-h-screen">
        
        <!-- Sidebar -->
        <aside class="w-64 bg-brand-card border-r border-white/5 flex flex-col fixed h-full z-10">
            <div class="p-6 border-b border-white/5">
                <h1 class="text-xl font-black tracking-tighter text-white">HAAAH<span class="text-brand-red ml-1">ADMIN</span></h1>
            </div>
            
            <nav class="flex-1 p-4 space-y-2">
                <button onclick="showTab('events')" id="nav-events" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-colors bg-brand-red text-white">
                    <i data-lucide="alert-circle" size="18"></i> Events Management
                    <?php if($stats['pending_events'] > 0): ?>
                        <span class="ml-auto bg-white text-brand-red px-2 py-0.5 rounded-full text-xs"><?php echo $stats['pending_events']; ?></span>
                    <?php endif; ?>
                </button>
                
                <button onclick="showTab('venues')" id="nav-venues" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white text-sm font-bold transition-colors">
                    <i data-lucide="map-pin" size="18"></i> Manage Venues
                </button>
                
                <button onclick="showTab('deleted')" id="nav-deleted" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white text-sm font-bold transition-colors">
                    <i data-lucide="trash-2" size="18"></i> Deleted Venues
                    <?php if($stats['deleted_venues'] > 0): ?>
                        <span class="ml-auto bg-white/10 text-gray-400 px-2 py-0.5 rounded-full text-xs"><?php echo $stats['deleted_venues']; ?></span>
                    <?php endif; ?>
                </button>
            </nav>

            <div class="p-4 border-t border-white/5">
                <a href="../actions/logout.php" class="flex items-center gap-3 px-4 py-3 text-red-400 hover:bg-red-500/10 rounded-xl text-sm font-bold transition-colors">
                    <i data-lucide="log-out" size="18"></i> Log Out
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-64 p-8">
            
            <!-- Stats Row -->
            <div class="grid grid-cols-4 gap-6 mb-10">
                <div class="bg-brand-card border border-white/5 p-5 rounded-xl">
                    <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Pending Approvals</div>
                    <div class="text-3xl font-black text-brand-red"><?php echo $stats['pending_events']; ?></div>
                </div>
                <div class="bg-brand-card border border-white/5 p-5 rounded-xl">
                    <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Total Venues</div>
                    <div class="text-3xl font-black text-white"><?php echo $stats['total_venues']; ?></div>
                </div>
                <div class="bg-brand-card border border-white/5 p-5 rounded-xl">
                    <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Deleted Items</div>
                    <div class="text-3xl font-black text-gray-500"><?php echo $stats['deleted_venues']; ?></div>
                </div>
                <div class="bg-brand-card border border-white/5 p-5 rounded-xl">
                    <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Total Users</div>
                    <div class="text-3xl font-black text-brand-purple"><?php echo $stats['total_users']; ?></div>
                </div>
            </div>

            <!-- TAB 1: EVENTS MANAGEMENT (Active + Pending) -->
            <div id="tab-events" class="tab-content">
                
                <!-- SECTION: ACTIVE EVENTS -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
                        <i data-lucide="zap" class="text-brand-accent"></i> Live & Active Events
                    </h2>

                    <?php if (empty($active_events)): ?>
                        <div class="p-8 text-center border border-white/5 bg-white/5 rounded-xl text-gray-500 text-sm">
                            No active upcoming events.
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($active_events as $event): ?>
                                <div class="bg-brand-card border border-white/5 p-5 rounded-xl flex flex-col md:flex-row gap-6 opacity-90 hover:opacity-100 hover:border-brand-accent/30 transition-all">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="bg-brand-accent text-black px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">Active</span>
                                            <span class="text-gray-500 text-xs font-mono">#<?php echo $event['event_id']; ?></span>
                                            <span class="bg-brand-purple/20 text-brand-purple px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider"><?php echo htmlspecialchars($event['sport'] ?? 'Sport'); ?></span>
                                        </div>
                                        <h3 class="text-xl font-bold text-white mb-2"><?php echo htmlspecialchars($event['title']); ?></h3>
                                        
                                        <div class="flex items-center gap-4 text-xs text-gray-400">
                                            <span class="flex items-center gap-1 text-white"><i data-lucide="calendar" size="12" class="text-brand-accent"></i> <?php echo date('M d, Y', strtotime($event['event_date'])); ?></span>
                                            <span class="flex items-center gap-1 text-white"><i data-lucide="clock" size="12" class="text-brand-accent"></i> <?php echo date('g:i A', strtotime($event['event_time'])); ?></span>
                                            <span>• <?php echo htmlspecialchars($event['duration']); ?>h</span>
                                            <span>• <?php echo htmlspecialchars($event['format']); ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center gap-3 border-l border-white/5 pl-6">
                                        <a href="venue-profile.php?id=<?php echo $event['venue_id']; ?>" target="_blank" class="text-xs font-bold text-brand-purple hover:underline flex items-center gap-1">
                                            <i data-lucide="map-pin" size="12"></i> <?php echo htmlspecialchars($event['venue_name']); ?>
                                        </a>
                                        <!-- Only Cancel Action needed for Active events -->
                                        <form action="../actions/admin_action.php" method="POST" onsubmit="return confirm('WARNING: Cancelling an active event will notify players. Proceed?');">
                                            <input type="hidden" name="action" value="reject_event">
                                            <input type="hidden" name="id" value="<?php echo $event['event_id']; ?>">
                                            <button type="submit" class="px-3 py-1.5 rounded bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white font-bold text-xs transition-colors flex items-center gap-1">
                                                <i data-lucide="slash" size="12"></i> Cancel
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- SECTION: PENDING EVENTS -->
                <div>
                    <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
                        <i data-lucide="alert-circle" class="text-brand-red"></i> Pending Approvals
                    </h2>
                    
                    <?php if (empty($pending_events)): ?>
                        <div class="p-12 text-center border-2 border-dashed border-white/10 rounded-2xl text-gray-500">
                            No events waiting for approval.
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($pending_events as $event): ?>
                                <div class="bg-brand-card border border-white/5 p-6 rounded-xl flex flex-col md:flex-row gap-6 group hover:border-white/10 transition-all">
                                    
                                    <!-- Event Info Column -->
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="bg-brand-red/20 text-brand-red px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">Reviewing</span>
                                            <span class="text-gray-500 text-xs font-mono">ID: <?php echo $event['event_id']; ?></span>
                                            <span class="bg-brand-purple/20 text-brand-purple px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">
                                                <?php echo htmlspecialchars($event['sport'] ?? 'Sport'); ?>
                                            </span>
                                        </div>
                                        
                                        <h3 class="text-2xl font-bold text-white mb-4"><?php echo htmlspecialchars($event['title']); ?></h3>
                                        
                                        <!-- Detailed Info Grid -->
                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 bg-black/20 rounded-lg border border-white/5 mb-4">
                                            <div>
                                                <div class="text-[10px] uppercase tracking-wider text-gray-500 font-bold mb-1">Date</div>
                                                <div class="text-sm font-bold text-white flex items-center gap-1">
                                                    <i data-lucide="calendar" size="12" class="text-brand-accent"></i>
                                                    <?php echo date('M d, Y', strtotime($event['event_date'])); ?>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="text-[10px] uppercase tracking-wider text-gray-500 font-bold mb-1">Time</div>
                                                <div class="text-sm font-bold text-white flex items-center gap-1">
                                                    <i data-lucide="clock" size="12" class="text-brand-accent"></i>
                                                    <?php echo date('g:i A', strtotime($event['event_time'])); ?>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="text-[10px] uppercase tracking-wider text-gray-500 font-bold mb-1">Details</div>
                                                <div class="text-sm font-bold text-white">
                                                    <?php echo htmlspecialchars($event['duration']); ?>h • <?php echo htmlspecialchars($event['format']); ?>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="text-[10px] uppercase tracking-wider text-gray-500 font-bold mb-1">Cost</div>
                                                <div class="text-sm font-bold text-brand-accent">
                                                    <?php echo htmlspecialchars($event['cost_per_player']); ?> GHS
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-6 text-sm text-gray-400">
                                            <div class="flex items-center gap-2">
                                                <i data-lucide="user" size="14"></i> 
                                                <span>Organizer: <span class="text-white font-bold"><?php echo htmlspecialchars($event['organizer_name']); ?></span></span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <i data-lucide="map-pin" size="14"></i> 
                                                <span>Venue: 
                                                    <a href="venue-profile.php?id=<?php echo $event['venue_id']; ?>" target="_blank" class="text-brand-purple hover:text-white hover:underline font-bold transition-colors">
                                                        <?php echo htmlspecialchars($event['venue_name']); ?>
                                                    </a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions Column -->
                                    <div class="flex flex-col justify-center gap-3 min-w-[160px] border-t md:border-t-0 md:border-l border-white/5 pt-4 md:pt-0 md:pl-6">
                                        <a href="venue-profile.php?id=<?php echo $event['venue_id']; ?>" target="_blank" class="px-4 py-2 rounded-lg bg-white/5 text-gray-300 font-bold text-xs hover:bg-white/10 hover:text-white transition-colors flex items-center justify-center gap-2">
                                            <i data-lucide="external-link" size="14"></i> Inspect Venue
                                        </a>
                                        
                                        <form action="../actions/admin_action.php" method="POST" onsubmit="return confirm('Are you sure you want to REJECT this event? This action cannot be undone.');">
                                            <input type="hidden" name="action" value="reject_event">
                                            <input type="hidden" name="id" value="<?php echo $event['event_id']; ?>">
                                            <button type="submit" class="w-full px-4 py-2 rounded-lg border border-red-500/30 text-red-500 hover:bg-red-500 hover:text-white font-bold text-sm transition-colors flex items-center justify-center gap-2">
                                                <i data-lucide="x-circle" size="16"></i> Reject
                                            </button>
                                        </form>
                                        
                                        <form action="../actions/admin_action.php" method="POST">
                                            <input type="hidden" name="action" value="approve_event">
                                            <input type="hidden" name="id" value="<?php echo $event['event_id']; ?>">
                                            <button type="submit" class="w-full px-4 py-2 rounded-lg bg-brand-accent text-black font-bold text-sm hover:bg-[#2fe080] transition-colors shadow-lg shadow-brand-accent/10 flex items-center justify-center gap-2">
                                                <i data-lucide="check-circle" size="16"></i> Approve
                                            </button>
                                        </form>
                                    </div>

                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- TAB 2: MANAGE VENUES -->
            <div id="tab-venues" class="tab-content hidden">
                <h2 class="text-2xl font-bold mb-6 flex items-center gap-2"><i data-lucide="map" class="text-brand-purple"></i> All Venues</h2>
                
                <div class="bg-brand-card border border-white/5 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-white/5 text-gray-400 uppercase font-bold text-xs">
                            <tr>
                                <th class="p-4">Venue</th>
                                <th class="p-4">Location</th>
                                <th class="p-4">Owner</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <?php foreach ($all_venues as $venue): ?>
                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="p-4 font-bold text-white"><?php echo htmlspecialchars($venue['name']); ?></td>
                                    <td class="p-4 text-gray-400"><?php echo htmlspecialchars($venue['address']); ?></td>
                                    <td class="p-4 text-gray-400"><?php echo htmlspecialchars($venue['owner_name']); ?></td>
                                    <td class="p-4">
                                        <?php if($venue['is_active']): ?>
                                            <span class="text-green-400 font-bold text-xs bg-green-500/10 px-2 py-1 rounded">Active</span>
                                        <?php else: ?>
                                            <span class="text-yellow-500 font-bold text-xs bg-yellow-500/10 px-2 py-1 rounded">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-4 text-right">
                                        <form action="../actions/admin_action.php" method="POST" class="inline-block">
                                            <input type="hidden" name="id" value="<?php echo $venue['venue_id']; ?>">
                                            <?php if($venue['is_active']): ?>
                                                <input type="hidden" name="action" value="deactivate_venue">
                                                <button class="text-xs font-bold text-red-400 hover:text-white hover:underline">Deactivate</button>
                                            <?php else: ?>
                                                <input type="hidden" name="action" value="activate_venue">
                                                <button class="text-xs font-bold text-green-400 hover:text-white hover:underline">Activate</button>
                                            <?php endif; ?>
                                        </form>
                                        <a href="venue-profile.php?id=<?php echo $venue['venue_id']; ?>" target="_blank" class="ml-3 text-xs font-bold text-brand-purple hover:text-white hover:underline">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 3: DELETED VENUES -->
            <div id="tab-deleted" class="tab-content hidden">
                <h2 class="text-2xl font-bold mb-6 flex items-center gap-2"><i data-lucide="trash" class="text-gray-500"></i> Deleted Venues</h2>
                
                <?php if (empty($deleted_venues)): ?>
                    <div class="p-12 text-center border-2 border-dashed border-white/10 rounded-2xl text-gray-500">
                        No deleted venues found.
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($deleted_venues as $d_venue): ?>
                            <div class="bg-brand-card border border-white/5 rounded-xl p-5 opacity-75 hover:opacity-100 transition-opacity">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-bold text-white"><?php echo htmlspecialchars($d_venue['name']); ?></h3>
                                    <span class="text-[10px] bg-red-500/20 text-red-400 px-2 py-0.5 rounded uppercase font-bold">Deleted</span>
                                </div>
                                <p class="text-xs text-gray-500 mb-4">Owner: <?php echo htmlspecialchars($d_venue['owner_name']); ?></p>
                                
                                <form action="../actions/admin_action.php" method="POST">
                                    <input type="hidden" name="action" value="restore_venue">
                                    <input type="hidden" name="id" value="<?php echo $d_venue['venue_id']; ?>">
                                    <button type="submit" class="w-full py-2 bg-white/5 hover:bg-white/10 border border-white/10 rounded-lg text-xs font-bold text-gray-300 transition-colors flex items-center justify-center gap-2">
                                        <i data-lucide="rotate-ccw" size="12"></i> Restore Venue
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </main>
    </div>

    <!-- FLASH MESSAGE -->
    <?php if ($flash): ?>
    <div id="flash-message" class="fixed bottom-6 right-6 z-50 toast-animate max-w-sm w-full">
        <div class="bg-brand-card border-l-4 <?php echo ($flash['type'] == 'success') ? 'border-brand-accent' : ($flash['type'] == 'warning' ? 'border-yellow-500' : 'border-red-500'); ?> p-4 rounded-r shadow-2xl flex items-start gap-3 relative">
            
            <div class="<?php echo ($flash['type'] == 'success') ? 'text-brand-accent' : ($flash['type'] == 'warning' ? 'text-yellow-500' : 'text-red-500'); ?>">
                <?php if($flash['type'] == 'success'): ?>
                    <i data-lucide="check-circle" size="24"></i>
                <?php elseif($flash['type'] == 'warning'): ?>
                    <i data-lucide="alert-triangle" size="24"></i>
                <?php else: ?>
                    <i data-lucide="x-circle" size="24"></i>
                <?php endif; ?>
            </div>
            
            <div>
                <h4 class="font-bold text-white text-sm"><?php echo $flash['title']; ?></h4>
                <p class="text-gray-400 text-xs mt-1"><?php echo $flash['message']; ?></p>
            </div>

            <button onclick="document.getElementById('flash-message').remove()" class="absolute top-2 right-2 text-gray-500 hover:text-white">
                <i data-lucide="x" size="14"></i>
            </button>
        </div>
    </div>
    <script>
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            const flash = document.getElementById('flash-message');
            if(flash) {
                flash.style.opacity = '0';
                flash.style.transform = 'translateY(10px)';
                flash.style.transition = 'all 0.5s ease';
                setTimeout(() => flash.remove(), 500);
            }
        }, 5000);
    </script>
    <?php endif; ?>

    <script>
        lucide.createIcons();

        function showTab(tabId) {
            // Hide all contents
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            // Reset Nav Styles
            const navBtns = ['events', 'venues', 'deleted'];
            navBtns.forEach(id => {
                const btn = document.getElementById('nav-' + id);
                btn.className = "w-full flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white text-sm font-bold transition-colors";
            });

            // Show selected content
            document.getElementById('tab-' + tabId).classList.remove('hidden');
            
            // Highlight active nav
            const activeBtn = document.getElementById('nav-' + tabId);
            if (tabId === 'events') {
                activeBtn.className = "w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-colors bg-brand-red text-white";
            } else {
                activeBtn.className = "w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-colors bg-white/10 text-white";
            }
        }
    </script>
</body>
</html>