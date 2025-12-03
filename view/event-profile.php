<?php
session_start();

// 1. Include Controller
require_once __DIR__ . '/../controllers/guest_controller.php';

// 2. Get Event ID
if (!isset($_GET['id'])) {
    header("Location: homepage.php");
    exit();
}
$event_id = $_GET['id'];

// 3. Fetch Data
$event = get_event_details_ctr($event_id);
// Ensure players is always an array to prevent count() errors
$players = get_event_players_ctr($event_id) ?: [];

// 4. Check if event exists
if (!$event) {
    die("Event not found.");
}

// 5. Logic & Calculations
$min_players = intval($event['min_players'] ?? 0);
if ($min_players === 0) $min_players = 10; 

// --- FEATURE: Substitutes Buffer ---
$buffer_slots = 3; 
$max_capacity = $min_players + $buffer_slots;

$current_players_db = intval($event['current_players'] ?? 0);
$players_list_count = count($players);
$current_players = max($players_list_count, $current_players_db);

$spots_left_to_confirm = max(0, $min_players - $current_players);
$total_spots_left = max(0, $max_capacity - $current_players);
$progress_percent = ($min_players > 0) ? min(100, ($current_players / $min_players) * 100) : 0;

$status = $event['status'] ?? 'pending';
$is_confirmed = ($status === 'confirmed' || $current_players >= $min_players);

$organizer_username = $event['organizer_username'] ?? 'Unknown';
$organizer_name = '@' . $organizer_username; 
$organizer_id = intval($event['organizer_id'] ?? 0);

// Calculate Fees
$entry_fee = floatval($event['cost_per_player'] ?? 0);
$service_fee = $entry_fee * 0.10; 
$total_cost = $entry_fee + $service_fee;

// Format Dates
$event_date_str = $event['event_date'] ?? 'now';
$event_time_str = $event['event_time'] ?? '00:00';
$formatted_date = date('D, M j', strtotime($event_date_str));
$formatted_time = date('H:i', strtotime($event_time_str));

// Location Data
$venue_name = $event['venue_name'] ?? 'Unknown Venue';
$venue_address = $event['venue_address'] ?? 'Accra, Ghana';
$venue_lat = isset($event['latitude']) ? floatval($event['latitude']) : null;
$venue_lng = isset($event['longitude']) ? floatval($event['longitude']) : null;

$event_title = $event['title'] ?? 'Untitled Event';
$event_format = $event['format'] ?? 'Sport';

// Check Membership
$is_joined = false;
$organizer_is_playing = false;
$is_current_user_organizer = (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $organizer_id);
$viewer_logged_in = isset($_SESSION['user_id']); 

if (!empty($players)) {
    foreach ($players as $p) {
        $pid = intval($p['id'] ?? 0);
        if (isset($_SESSION['user_id']) && $pid == $_SESSION['user_id']) {
            $is_joined = true;
        }
        if ($pid == $organizer_id) {
            $organizer_is_playing = true;
        }
    }
}

// --- PHP MODAL LOGIC ---
$modal_type = isset($_GET['msg']) ? $_GET['msg'] : '';
$show_modal = false;
$modal_title = '';
$modal_msg = '';
$modal_icon = 'info'; 

if ($modal_type === 'already_joined') {
    $show_modal = true; $modal_title = "You're already in!"; $modal_msg = "You are already on the squad list."; $modal_icon = 'check-circle';
} elseif ($modal_type === 'already_published') {
    $show_modal = true; $modal_title = "Already Published"; $modal_msg = "This event is already live."; $modal_icon = 'check-circle';
} elseif ($modal_type === 'joined') {
    $show_modal = true; $modal_title = "Welcome to the Squad!"; $modal_msg = "Payment successful. You have secured your spot."; $modal_icon = 'check-circle';
} elseif ($modal_type === 'published') {
    $show_modal = true; $modal_title = "Event Published!"; $modal_msg = "Your game is now live."; $modal_icon = 'check-circle';
} elseif ($modal_type === 'host_joined') {
    $show_modal = true; $modal_title = "You're Playing!"; $modal_msg = "You have successfully added yourself."; $modal_icon = 'check-circle';
} elseif ($modal_type === 'host_left') {
    $show_modal = true; $modal_title = "Spot Removed"; $modal_msg = "You have removed yourself from the roster."; $modal_icon = 'info';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($event_title); ?> - Match Lobby</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDgP6xqZcN4y50x2kq8cbytyD-k4OY1Sis&libraries=places"></script>

    <script>
        tailwind.config = {
            theme: { extend: { colors: { brand: { dark: '#0f0f13', card: '#1a1a23', accent: '#3dff92', purple: '#7000ff' } }, fontFamily: { sans: ['Inter', 'sans-serif'] } } }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap'); 
        body { font-family: 'Inter', sans-serif; background-color: #0f0f13; color: white; }
        .animate-fade-in { animation: fadeIn 0.3s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    </style>
</head>
<body class="selection:bg-brand-accent selection:text-black pb-20">

    <!-- Navbar -->
    <nav class="border-b border-white/5 bg-brand-card px-6 py-4 flex justify-between items-center sticky top-0 z-50">
        <a href="homepage.php" class="flex items-center gap-2 text-gray-400 hover:text-white">
            <i data-lucide="arrow-left" size="20"></i> Back to Games
        </a>
        <div class="flex items-center gap-4">
            <button onclick="shareEvent()" class="p-2 text-gray-400 hover:text-white transition-colors" title="Share Link">
                <i data-lucide="share-2" size="20"></i>
            </button>
            <?php if (!$is_joined && !$is_current_user_organizer): ?>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                </a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- LEFT COLUMN -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Hero Card -->
                <div class="bg-brand-card rounded-2xl p-6 border border-white/5 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-32 bg-brand-accent/5 blur-[80px] rounded-full"></div>
                    <div class="flex justify-between items-start relative z-10 mb-6">
                        <div>
                            <span class="px-3 py-1 bg-brand-purple/20 text-brand-purple text-xs font-bold uppercase tracking-wider rounded-full mb-2 inline-block">
                                <?php echo htmlspecialchars($event_format); ?>
                            </span>
                            <h1 class="text-3xl font-black mb-2"><?php echo htmlspecialchars($event_title); ?></h1>
                            <div class="flex items-center gap-4 text-sm text-gray-400">
                                <span class="flex items-center gap-1"><i data-lucide="calendar" size="14"></i> <?php echo $formatted_date; ?></span>
                                <span class="flex items-center gap-1"><i data-lucide="clock" size="14"></i> <?php echo $formatted_time; ?></span>
                                <span class="flex items-center gap-1"><i data-lucide="map-pin" size="14"></i> <?php echo htmlspecialchars($venue_name); ?></span>
                            </div>
                        </div>
                        <div class="text-center bg-black/30 p-3 rounded-xl border border-white/10">
                            <div class="text-xs text-gray-400 uppercase font-bold">Entry Fee</div>
                            <div class="text-xl font-black text-brand-accent">GHS <?php echo number_format($entry_fee, 2); ?></div>
                        </div>
                    </div>

                    <!-- Threshold Meter -->
                    <div class="bg-black/20 rounded-xl p-4 border border-white/5">
                        <div class="flex justify-between text-sm mb-2">
                            <?php if ($is_confirmed): ?>
                                <span class="font-bold text-brand-accent flex items-center gap-2">
                                    <i data-lucide="check-circle" size="14"></i> Green Light Confirmed
                                </span>
                            <?php else: ?>
                                <span class="font-bold text-yellow-500 flex items-center gap-2">
                                    <i data-lucide="clock" size="14"></i> Pending Green Light
                                </span>
                            <?php endif; ?>
                            <span class="text-gray-400"><?php echo $current_players; ?> / <?php echo $min_players; ?> Main Squad (+<?php echo $buffer_slots; ?> Subs)</span>
                        </div>
                        <div class="w-full bg-white/10 rounded-full h-3 mb-2 overflow-hidden">
                            <div class="bg-gradient-to-r <?php echo $is_confirmed ? 'from-green-600 to-brand-accent' : 'from-yellow-600 to-yellow-400'; ?> h-full rounded-full relative" style="width: <?php echo $progress_percent; ?>%">
                                <div class="absolute right-0 top-0 bottom-0 w-1 bg-white/50 animate-pulse"></div>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500">
                            <?php if ($is_confirmed): ?>
                                <?php if($total_spots_left > 0): ?>
                                    This game is ON! <strong><?php echo $total_spots_left; ?> substitute spots available</strong> for late joiners.
                                <?php else: ?>
                                    Full House! No substitute spots left.
                                <?php endif; ?>
                            <?php else: ?>
                                <?php echo $spots_left_to_confirm; ?> more players needed to confirm venue booking.
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <!-- The Squad -->
                <div>
                    <h3 class="font-bold text-xl mb-4 flex items-center gap-2">
                        <i data-lucide="users" class="text-brand-accent"></i> Squad List
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        
                        <!-- Host (Linked if logged in, redirects to login if guest) -->
                        <?php 
                            $hostTag = 'a';
                            $hostHref = $viewer_logged_in ? 'href="profile.php?id=' . $organizer_id . '"' : 'href="login.php?msg=login_to_view_profile"';
                        ?>
                        <<?php echo $hostTag; ?> <?php echo $hostHref; ?> class="bg-brand-card p-4 rounded-xl border <?php echo $organizer_is_playing ? 'border-brand-accent shadow-[0_0_10px_rgba(61,255,146,0.1)]' : 'border-white/10 border-dashed'; ?> relative transition-all hover:bg-white/5 cursor-pointer">
                            <span class="absolute top-2 right-2 text-[10px] font-bold px-2 py-0.5 rounded <?php echo $organizer_is_playing ? 'bg-brand-accent text-black' : 'bg-white/10 text-gray-400'; ?>">
                                <?php echo $organizer_is_playing ? 'HOST • PLAYING' : 'HOST ONLY'; ?>
                            </span>
                            <div class="flex items-center gap-3 mb-2 mt-2">
                                <div class="w-10 h-10 rounded-full bg-purple-600 flex items-center justify-center font-bold text-sm text-white uppercase">
                                    <?php echo substr($organizer_username, 0, 2); ?>
                                </div>
                                <div>
                                    <div class="font-bold text-sm truncate max-w-[80px]"><?php echo $organizer_name; ?></div>
                                    <div class="text-xs text-gray-500">Organizer</div>
                                </div>
                            </div>
                        </<?php echo $hostTag; ?>>

                        <!-- Players (Linked if logged in, redirects to login if guest) -->
                        <?php if (!empty($players)): ?>
                            <?php 
                                $p_index = 0; 
                                foreach($players as $player): 
                                    $p_id = intval($player['id'] ?? 0);
                                    if($p_id == $organizer_id) continue; 
                                    $p_username = $player['user_name'] ?? 'Player';
                                    
                                    $is_sub = ($p_index + 1) > $min_players;
                                    $p_index++;
                                    
                                    // Dynamic Link Logic
                                    $pTag = 'a';
                                    $pHref = $viewer_logged_in ? 'href="profile.php?id=' . $p_id . '"' : 'href="login.php?msg=login_to_view_profile"';
                            ?>
                                <<?php echo $pTag; ?> <?php echo $pHref; ?> class="bg-brand-card p-4 rounded-xl border <?php echo $is_sub ? 'border-orange-500/30' : 'border-white/5'; ?> relative transition-colors hover:border-white/20 cursor-pointer">
                                    <?php if($is_sub): ?>
                                        <span class="absolute top-2 right-2 text-[8px] font-bold px-1.5 py-0.5 rounded bg-orange-500/20 text-orange-500 uppercase">Sub</span>
                                    <?php endif; ?>
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center font-bold text-sm text-white uppercase">
                                            <?php echo substr($p_username, 0, 2); ?>
                                        </div>
                                        <div>
                                            <div class="font-bold text-sm truncate max-w-[80px]">@<?php echo htmlspecialchars($p_username); ?></div>
                                            <div class="text-xs text-gray-500"><?php echo $is_sub ? 'Substitute' : 'Player'; ?></div>
                                        </div>
                                    </div>
                                </<?php echo $pTag; ?>>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- Empty Slots -->
                        <?php 
                            $slots_occupied = $current_players; 
                            $target_slots = ($min_players > 0) ? $min_players : 10;
                            $empty_slots_count = max(0, $max_capacity - $slots_occupied);
                        ?>
                        <?php for($i = 0; $i < $empty_slots_count; $i++): ?>
                            <?php 
                                $logical_slot_number = $slots_occupied + $i + 1;
                                $is_sub_slot = $logical_slot_number > $min_players;
                                
                                $slot_border = $is_sub_slot ? 'border-orange-500/20 border-dashed' : 'border-white/10 border-dashed border-2';
                                $slot_text = $is_sub_slot ? 'Open Sub' : 'Open Slot';
                                $slot_icon_color = $is_sub_slot ? 'text-orange-500' : 'text-gray-400';
                                
                                $slot_link = "checkout.php?event_id=$event_id&type=join_game";
                                $slot_onclick = "";
                                
                                if ($is_joined) {
                                    $slot_link = "javascript:void(0);";
                                    $slot_onclick = "onclick=\"showJsModal('Slot Occupied', 'You are already occupying a slot in this squad!');\"";
                                }
                            ?>
                            <a href="<?php echo $slot_link; ?>" <?php echo $slot_onclick; ?> class="bg-brand-dark p-4 rounded-xl <?php echo $slot_border; ?> hover:bg-white/5 transition-all group flex flex-col items-center justify-center gap-2 h-full cursor-pointer relative">
                                <?php if($is_sub_slot): ?>
                                    <span class="absolute top-2 right-2 text-[8px] font-bold px-1.5 py-0.5 rounded bg-orange-500/10 text-orange-500 uppercase">Buffer</span>
                                <?php endif; ?>
                                <div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center group-hover:bg-brand-accent group-hover:text-black transition-colors <?php echo $slot_icon_color; ?>">
                                    <i data-lucide="plus" size="16"></i>
                                </div>
                                <span class="text-xs font-bold text-gray-400 group-hover:text-white"><?php echo $slot_text; ?></span>
                            </a>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <!-- Map Section -->
                <div class="bg-brand-card rounded-2xl p-6 border border-white/5">
                    <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
                        <i data-lucide="map" class="text-brand-accent"></i> Location
                    </h3>
                    <div id="map" class="w-full h-[300px] md:h-[400px] rounded-xl border border-white/10 mb-4 bg-[#2a2a35] relative group overflow-hidden"></div>
                    <div class="flex items-start gap-3">
                        <div class="p-2 bg-brand-dark rounded-lg border border-white/5"><i data-lucide="navigation" size="18" class="text-brand-accent"></i></div>
                        <div>
                            <h4 class="font-bold text-sm text-white"><?php echo htmlspecialchars($venue_name); ?></h4>
                            <p class="text-xs text-gray-500"><?php echo htmlspecialchars($venue_address); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN -->
            <div class="space-y-6">
                <!-- Action Card -->
                <div class="bg-brand-card rounded-2xl p-6 border border-white/5 shadow-xl sticky top-24">
                    <?php if ($is_joined): ?>
                        <!-- ALREADY JOINED -->
                        <div class="text-center py-4">
                            <div class="w-16 h-16 bg-green-500/20 text-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="check" size="32"></i>
                            </div>
                            <h3 class="font-bold text-xl text-white">You're on the list!</h3>
                            <p class="text-sm text-gray-400 mt-2">See you on the pitch.</p>
                            
                            <?php if ($is_current_user_organizer): ?>
                                <!-- ORGANIZER LEAVE OPTION -->
                                <form action="../actions/organizer_player_toggle.php" method="POST" class="mt-6">
                                    <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
                                    <input type="hidden" name="action" value="leave">
                                    <button type="submit" class="px-4 py-2 bg-red-500/10 hover:bg-red-500/20 text-red-500 font-bold text-xs rounded-lg transition-colors border border-red-500/20">
                                        Remove me from squad
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <!-- NOT JOINED YET -->
                        <?php if ($is_current_user_organizer): ?>
                             <!-- ORGANIZER VIEW: Free Join -->
                             <h3 class="font-bold text-lg mb-4 text-brand-accent">Hop in, Coach?</h3>
                             <p class="text-sm text-gray-400 mb-6">As the host, you can take a spot on the roster instantly without extra fees.</p>
                             
                             <form action="../actions/organizer_player_toggle.php" method="POST">
                                 <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
                                 <input type="hidden" name="action" value="join">
                                 <button type="submit" class="block w-full py-3 bg-brand-accent hover:bg-[#2fe080] text-black font-bold text-center rounded-xl transition-transform hover:scale-105">
                                     Join Squad (Host)
                                 </button>
                             </form>
                        <?php else: ?>
                            <!-- PLAYER VIEW: Pay to Join -->
                            <h3 class="font-bold text-lg mb-4">Join this Game</h3>
                            <div class="space-y-3 mb-6">
                                <div class="flex justify-between text-sm"><span class="text-gray-400">Spot Price</span><span>GHS <?php echo number_format($entry_fee, 2); ?></span></div>
                                <div class="flex justify-between text-sm"><span class="text-gray-400">Booking Fee</span><span>GHS <?php echo number_format($service_fee, 2); ?></span></div>
                                <div class="h-px bg-white/10"></div>
                                <div class="flex justify-between font-bold text-brand-accent"><span>Total</span><span>GHS <?php echo number_format($total_cost, 2); ?></span></div>
                            </div>
                            <a href="checkout.php?event_id=<?php echo $event_id; ?>&type=join_game" class="block w-full py-3 bg-brand-accent hover:bg-[#2fe080] text-black font-bold text-center rounded-xl transition-transform hover:scale-105 mb-3">
                                Join Squad (GHS <?php echo number_format($total_cost, 2); ?>)
                            </a>
                            <p class="text-[10px] text-center text-gray-500 mt-3">
                                <i data-lucide="shield-check" size="10" class="inline"></i> Refunded automatically if game is cancelled.
                            </p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Live Chat Placeholder -->
                <div class="bg-brand-card rounded-2xl border border-white/5 h-[400px] flex flex-col opacity-75">
                    <div class="p-4 border-b border-white/5 font-bold flex items-center justify-between">
                        <span>Lobby Chat</span>
                        <span class="text-xs text-gray-500">Coming Soon</span>
                    </div>
                    <div class="flex-1 flex items-center justify-center text-gray-600 text-sm">
                        Chat will be available once the game is confirmed.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SHARE MODAL -->
    <div id="share-modal" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 hidden opacity-0 transition-opacity duration-300">
        <div class="bg-[#1a1a23] border border-white/10 rounded-2xl p-6 max-w-sm w-full shadow-2xl transform transition-all scale-95">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-white">Share Event</h3>
                <button onclick="closeShareModal()" class="text-gray-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            
            <div class="bg-black/30 p-3 rounded-xl border border-white/5 mb-4 flex items-center gap-2">
                <input type="text" id="share-url" class="bg-transparent border-none text-gray-400 text-sm flex-1 focus:outline-none truncate" readonly>
                <button onclick="copyToClipboard()" class="p-2 bg-brand-accent/10 text-brand-accent rounded-lg hover:bg-brand-accent/20 transition-colors" title="Copy">
                    <i data-lucide="copy" class="w-4 h-4"></i>
                </button>
            </div>

            <button id="native-share-btn" onclick="triggerNativeShare()" class="w-full py-3 bg-white/5 hover:bg-white/10 text-white font-bold rounded-xl transition-colors border border-white/10 flex items-center justify-center gap-2 hidden">
                <i data-lucide="share-2" class="w-4 h-4"></i> Share via...
            </button>
            
            <div id="copy-feedback" class="text-center text-xs text-green-500 mt-2 hidden">Link copied!</div>
        </div>
    </div>

    <!-- PHP NOTIFICATION MODAL -->
    <?php if($show_modal): ?>
    <div id="notification-modal" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 animate-fade-in">
        <div class="bg-[#1a1a23] border border-white/10 rounded-2xl p-8 max-w-sm w-full shadow-2xl transform transition-all scale-100">
            <div class="text-center">
                <div class="w-16 h-16 bg-brand-accent/10 text-brand-accent rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="<?php echo $modal_icon; ?>" class="w-8 h-8"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2"><?php echo $modal_title; ?></h3>
                <p class="text-gray-400 text-sm mb-6"><?php echo $modal_msg; ?></p>
                <button onclick="closeModal()" class="w-full py-3 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl transition-colors border border-white/5">
                    Got it
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- JS ALERT MODAL (Dynamic) -->
    <div id="js-alert-modal" class="fixed inset-0 z-[70] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 hidden opacity-0 transition-opacity duration-300">
        <div class="bg-[#1a1a23] border border-red-500/30 rounded-2xl p-8 max-w-sm w-full shadow-2xl transform transition-all scale-95">
            <div class="text-center">
                <div class="w-16 h-16 bg-red-500/10 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="alert-circle" class="w-8 h-8"></i>
                </div>
                <h3 id="js-modal-title" class="text-xl font-bold text-white mb-2">Alert</h3>
                <p id="js-modal-msg" class="text-gray-400 text-sm mb-6">Message</p>
                <button onclick="closeJsModal()" class="w-full py-3 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl transition-colors border border-white/5">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // --- JS MODAL LOGIC ---
        function showJsModal(title, msg) {
            const modal = document.getElementById('js-alert-modal');
            document.getElementById('js-modal-title').textContent = title;
            document.getElementById('js-modal-msg').textContent = msg;
            
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.querySelector('div').classList.remove('scale-95');
                modal.querySelector('div').classList.add('scale-100');
            }, 10);
        }

        function closeJsModal() {
            const modal = document.getElementById('js-alert-modal');
            modal.classList.add('opacity-0');
            modal.querySelector('div').classList.remove('scale-100');
            modal.querySelector('div').classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // --- SHARE MODAL LOGIC ---
        function shareEvent() {
            const modal = document.getElementById('share-modal');
            const urlInput = document.getElementById('share-url');
            const nativeBtn = document.getElementById('native-share-btn');
            
            urlInput.value = window.location.href;
            
            if (navigator.share) {
                nativeBtn.classList.remove('hidden');
            } else {
                nativeBtn.classList.add('hidden');
            }

            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.querySelector('div').classList.remove('scale-95');
                modal.querySelector('div').classList.add('scale-100');
            }, 10);
        }

        function closeShareModal() {
            const modal = document.getElementById('share-modal');
            modal.classList.add('opacity-0');
            modal.querySelector('div').classList.remove('scale-100');
            modal.querySelector('div').classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                document.getElementById('copy-feedback').classList.add('hidden');
            }, 300);
        }

        function copyToClipboard() {
            const copyText = document.getElementById("share-url");
            copyText.select();
            copyText.setSelectionRange(0, 99999); 
            if (navigator.clipboard) {
                 navigator.clipboard.writeText(copyText.value).then(() => showCopyFeedback());
            } else {
                document.execCommand("copy");
                showCopyFeedback();
            }
        }

        function showCopyFeedback() {
             const feedback = document.getElementById('copy-feedback');
             feedback.classList.remove('hidden');
             setTimeout(() => feedback.classList.add('hidden'), 2000);
        }

        function triggerNativeShare() {
            const title = <?php echo json_encode($event_title); ?>;
            const text = 'Check out this game on Haaah Sports!';
            const url = window.location.href;
            if (navigator.share) navigator.share({ title, text, url }).catch(console.error);
        }

        function closeModal() {
            const modal = document.getElementById('notification-modal');
            if(modal) {
                modal.style.opacity = '0';
                modal.style.transition = 'opacity 0.3s ease';
                setTimeout(() => modal.remove(), 300); 
            }
            const url = new URL(window.location);
            url.searchParams.delete('msg');
            window.history.replaceState({}, '', url);
        }

        // Map Logic
        function initMap() {
            const venueLat = <?php echo $venue_lat ? $venue_lat : 'null'; ?>;
            const venueLng = <?php echo $venue_lng ? $venue_lng : 'null'; ?>;
            const venueName = "<?php echo htmlspecialchars($venue_name); ?>";
            const venueAddr = "<?php echo htmlspecialchars($venue_address); ?>";

            const darkMapStyle = [
                { elementType: "geometry", stylers: [{ color: "#242f3e" }] },
                { elementType: "labels.text.stroke", stylers: [{ color: "#242f3e" }] },
                { elementType: "labels.text.fill", stylers: [{ color: "#746855" }] },
                { featureType: "road", elementType: "geometry", stylers: [{ color: "#38414e" }] },
                { featureType: "road", elementType: "geometry.stroke", stylers: [{ color: "#212a37" }] },
                { featureType: "road", elementType: "labels.text.fill", stylers: [{ color: "#9ca5b3" }] },
                { featureType: "water", elementType: "geometry", stylers: [{ color: "#17263c" }] }
            ];

            const mapOptions = {
                zoom: 15,
                center: { lat: 5.6037, lng: -0.1870 },
                styles: darkMapStyle,
                disableDefaultUI: false
            };

            const map = new google.maps.Map(document.getElementById("map"), mapOptions);

            if (venueLat && venueLng) {
                const pos = { lat: venueLat, lng: venueLng };
                map.setCenter(pos);
                new google.maps.Marker({ position: pos, map: map, title: venueName, icon: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png' });
            } else if (venueAddr && venueAddr !== 'Accra, Ghana') {
                const geocoder = new google.maps.Geocoder();
                const fullAddress = venueName + ", " + venueAddr; 
                geocoder.geocode({ 'address': fullAddress }, function(results, status) {
                    if (status === 'OK') {
                        map.setCenter(results[0].geometry.location);
                        new google.maps.Marker({ map: map, position: results[0].geometry.location, title: venueName });
                    }
                });
            }
        }

        window.addEventListener('load', function() {
            if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') initMap();
        });
    </script>
</body>
</html>