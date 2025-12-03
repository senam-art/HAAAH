<?php
session_start();
require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/controllers/admin_controller.php';

// 1. Security Check (Admin Role = 2)
// Updated to use $_SESSION['role'] matching your Login Controller
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 2) {
    // Not an admin? Bounce to login.
    header("Location: login.php");
    exit();
}

// 2. Fetch Data
$stats = get_admin_stats_ctr();
$pending_events = get_pending_events_ctr();
$all_venues = get_all_venues_admin_ctr();
$deleted_venues = get_deleted_venues_ctr();
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
    <style>@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap'); body { font-family: 'Inter', sans-serif; background-color: #0f0f13; color: white; }</style>
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
                    <i data-lucide="alert-circle" size="18"></i> Pending Events
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

            <!-- TAB 1: PENDING EVENTS -->
            <div id="tab-events" class="tab-content">
                <h2 class="text-2xl font-bold mb-6 flex items-center gap-2"><i data-lucide="calendar" class="text-brand-red"></i> Pending Events</h2>
                
                <?php if (empty($pending_events)): ?>
                    <div class="p-12 text-center border-2 border-dashed border-white/10 rounded-2xl text-gray-500">
                        No events waiting for approval.
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($pending_events as $event): ?>
                            <div class="bg-brand-card border border-white/5 p-6 rounded-xl flex items-center justify-between group hover:border-white/10 transition-all">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-brand-accent text-xs font-bold uppercase tracking-wider">Event #<?php echo $event['event_id']; ?></span>
                                        <span class="text-gray-500 text-xs">• <?php echo htmlspecialchars($event['venue_name']); ?></span>
                                    </div>
                                    <h3 class="text-xl font-bold text-white mb-1"><?php echo htmlspecialchars($event['title']); ?></h3>
                                    <p class="text-sm text-gray-400">
                                        Organizer: <span class="text-white"><?php echo htmlspecialchars($event['organizer_name']); ?></span> (<?php echo htmlspecialchars($event['organizer_email']); ?>)
                                    </p>
                                    <div class="mt-2 text-xs font-mono text-gray-500 bg-black/20 inline-block px-2 py-1 rounded">
                                        <?php echo $event['event_date'] . ' @ ' . $event['event_time']; ?>
                                    </div>
                                </div>
                                <div class="flex gap-3">
                                    <form action="../actions/admin_action.php" method="POST">
                                        <input type="hidden" name="action" value="reject_event">
                                        <input type="hidden" name="id" value="<?php echo $event['event_id']; ?>">
                                        <button type="submit" class="px-4 py-2 rounded-lg border border-red-500/30 text-red-500 hover:bg-red-500 hover:text-white font-bold text-sm transition-colors">Reject</button>
                                    </form>
                                    <form action="../actions/admin_action.php" method="POST">
                                        <input type="hidden" name="action" value="approve_event">
                                        <input type="hidden" name="id" value="<?php echo $event['event_id']; ?>">
                                        <button type="submit" class="px-6 py-2 rounded-lg bg-brand-accent text-black font-bold text-sm hover:bg-[#2fe080] transition-colors shadow-lg shadow-brand-accent/10">Approve & Publish</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
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