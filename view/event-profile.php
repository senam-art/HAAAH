<?php
session_start();

// 1. Include Controller
require_once __DIR__ . '/../controllers/guest_controller.php';

// 2. Get Event ID
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}
$event_id = $_GET['id'];

// 3. Fetch Data
$event = get_event_details_ctr($event_id);
$players = get_event_players_ctr($event_id);

// 4. Check if event exists
if (!$event) {
    // Redirect or show simple error
    // header("Location: index.php"); 
    // exit();
    die("Event not found.");
}

// 5. Logic & Calculations
// Use null coalescing (??) to handle potential missing database fields safely
$min_players = intval($event['min_players'] ?? 0);
$current_players_db = intval($event['current_players'] ?? 0);

$players_list_count = (is_array($players)) ? count($players) : 0;
$current_players = $players_list_count > 0 ? $players_list_count : $current_players_db;

$spots_left = max(0, $min_players - $current_players);
$progress_percent = ($min_players > 0) ? min(100, ($current_players / $min_players) * 100) : 0;

$status = $event['status'] ?? 'pending';
$is_confirmed = ($status === 'confirmed');

$organizer_username = $event['organizer_username'] ?? 'Unknown';
$organizer_name = '@' . $organizer_username; 

// Calculate Fees
$entry_fee = floatval($event['cost_per_player'] ?? 0);
$service_fee = $entry_fee * 0.10; 
$total_cost = $entry_fee + $service_fee;

// Format Dates
$event_date_str = $event['event_date'] ?? 'now';
$event_time_str = $event['event_time'] ?? '00:00';
$formatted_date = date('D, M j', strtotime($event_date_str));
$formatted_time = date('H:i', strtotime($event_time_str));

// Location Data for Map
$venue_name = $event['venue_name'] ?? 'Unknown Venue';
$venue_address = $event['venue_address'] ?? 'Accra, Ghana';
$venue_lat = isset($event['latitude']) ? floatval($event['latitude']) : null;
$venue_lng = isset($event['longitude']) ? floatval($event['longitude']) : null;

$event_title = $event['title'] ?? 'Untitled Event';
$event_format = $event['format'] ?? 'Sport';

// Check Membership (Is current user already joined?)
$is_joined = false;
if (isset($_SESSION['user_id']) && is_array($players)) {
    foreach ($players as $p) {
        if (isset($p['id']) && $p['id'] == $_SESSION['user_id']) {
            $is_joined = true;
            break;
        }
    }
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
    
    <!-- Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDgP6xqZcN4y50x2kq8cbytyD-k4OY1Sis&libraries=places"></script>

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
    <nav class="border-b border-white/5 bg-brand-card px-6 py-4 flex justify-between items-center sticky top-0 z-50">
        <a href="index.php" class="flex items-center gap-2 text-gray-400 hover:text-white">
            <i data-lucide="arrow-left" size="20"></i> Back to Games
        </a>
        <div class="flex items-center gap-4">
            <button class="p-2 text-gray-400 hover:text-white"><i data-lucide="share-2" size="20"></i></button>
            <?php if (!$is_joined): ?>
                <a href="checkout.php?event_id=<?php echo $event_id; ?>&type=join_game" class="relative p-2 text-brand-accent hover:text-white">
                    <i data-lucide="shopping-cart" size="20"></i>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                </a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 lg:px-8 py-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- LEFT COLUMN: Match Info & Roster -->
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
                            <span class="text-gray-400"><?php echo $current_players; ?> / <?php echo $min_players; ?> Players Joined</span>
                        </div>
                        <div class="w-full bg-white/10 rounded-full h-3 mb-2 overflow-hidden">
                            <div class="bg-gradient-to-r <?php echo $is_confirmed ? 'from-green-600 to-brand-accent' : 'from-yellow-600 to-yellow-400'; ?> h-full rounded-full relative" style="width: <?php echo $progress_percent; ?>%">
                                <div class="absolute right-0 top-0 bottom-0 w-1 bg-white/50 animate-pulse"></div>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500">
                            <?php if ($is_confirmed): ?>
                                This game is ON! Venue is booked.
                            <?php else: ?>
                                <?php echo $spots_left; ?> more players needed to confirm venue booking.
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <!-- The Squad (Roster) -->
                <div>
                    <h3 class="font-bold text-xl mb-4 flex items-center gap-2">
                        <i data-lucide="users" class="text-brand-accent"></i> Squad List
                    </h3>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <!-- Host Card -->
                        <div class="bg-brand-card p-4 rounded-xl border border-brand-accent/30 relative">
                            <span class="absolute top-2 right-2 text-[10px] bg-brand-accent text-black font-bold px-1.5 py-0.5 rounded">HOST</span>
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 rounded-full bg-purple-600 flex items-center justify-center font-bold text-sm text-white uppercase">
                                    <?php echo substr($organizer_username, 0, 2); ?>
                                </div>
                                <div>
                                    <div class="font-bold text-sm truncate max-w-[80px]"><?php echo $organizer_name; ?></div>
                                    <div class="text-xs text-gray-500">Organizer</div>
                                </div>
                            </div>
                        </div>

                        <!-- Player List -->
                        <?php if (is_array($players)): ?>
                            <?php foreach($players as $player): ?>
                                <?php 
                                    $p_id = $player['id'] ?? 0;
                                    $o_id = $event['organizer_id'] ?? -1;
                                    if($p_id == $o_id) continue; 
                                    $p_username = $player['user_name'] ?? 'Player';
                                ?>
                                
                                <div class="bg-brand-card p-4 rounded-xl border border-white/5">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center font-bold text-sm text-white uppercase">
                                            <?php echo substr($p_username, 0, 2); ?>
                                        </div>
                                        <div>
                                            <div class="font-bold text-sm truncate max-w-[80px]">@<?php echo htmlspecialchars($p_username); ?></div>
                                            <div class="text-xs text-gray-500">Player</div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- Empty Slots (Visual Filler) -->
                        <?php for($i = 0; $i < max(0, 4 - (is_array($players) ? count($players) : 0) - 1); $i++): ?>
                            <a href="checkout.php?event_id=<?php echo $event_id; ?>&type=join_game" class="bg-brand-dark p-4 rounded-xl border-2 border-dashed border-white/10 hover:border-brand-accent/50 hover:bg-white/5 transition-all group flex flex-col items-center justify-center gap-2 h-full cursor-pointer">
                                <div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center group-hover:bg-brand-accent group-hover:text-black transition-colors">
                                    <i data-lucide="plus" size="16"></i>
                                </div>
                                <span class="text-xs font-bold text-gray-400 group-hover:text-white">Open Slot</span>
                            </a>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- Google Map Section -->
                <div class="bg-brand-card rounded-2xl p-6 border border-white/5">
                    <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
                        <i data-lucide="map" class="text-brand-accent"></i> Location
                    </h3>
                    
                    <div id="map" class="w-full h-[300px] md:h-[400px] rounded-xl border border-white/10 mb-4 bg-[#2a2a35] relative group overflow-hidden"></div>
                    
                    <div class="flex items-start gap-3">
                        <div class="p-2 bg-brand-dark rounded-lg border border-white/5">
                            <i data-lucide="navigation" size="18" class="text-brand-accent"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-white"><?php echo htmlspecialchars($venue_name); ?></h4>
                            <p class="text-xs text-gray-500"><?php echo htmlspecialchars($venue_address); ?></p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: Actions -->
            <div class="space-y-6">
                
                <!-- Action Card -->
                <div class="bg-brand-card rounded-2xl p-6 border border-white/5 shadow-xl sticky top-24">
                    <?php if ($is_joined): ?>
                        <div class="text-center py-4">
                            <div class="w-16 h-16 bg-green-500/20 text-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="check" size="32"></i>
                            </div>
                            <h3 class="font-bold text-xl text-white">You're on the list!</h3>
                            <p class="text-sm text-gray-400 mt-2">See you on the pitch.</p>
                        </div>
                    <?php else: ?>
                        <h3 class="font-bold text-lg mb-4">Join this Game</h3>
                        
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-400">Spot Price</span>
                                <span>GHS <?php echo number_format($entry_fee, 2); ?></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-400">Booking Fee</span>
                                <span>GHS <?php echo number_format($service_fee, 2); ?></span>
                            </div>
                            <div class="h-px bg-white/10"></div>
                            <div class="flex justify-between font-bold text-brand-accent">
                                <span>Total</span>
                                <span>GHS <?php echo number_format($total_cost, 2); ?></span>
                            </div>
                        </div>

                        <a href="checkout.php?event_id=<?php echo $event_id; ?>&type=join_game" class="block w-full py-3 bg-brand-accent hover:bg-[#2fe080] text-black font-bold text-center rounded-xl transition-transform hover:scale-105 mb-3">
                            Join Squad (GHS <?php echo number_format($total_cost, 2); ?>)
                        </a>
                        <p class="text-[10px] text-center text-gray-500 mt-3">
                            <i data-lucide="shield-check" size="10" class="inline"></i> Refunded automatically if game is cancelled.
                        </p>
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

    <script>
        lucide.createIcons();

        // --- MAP INITIALIZATION ---
        function initMap() {
            // Get PHP values (default to null if not set)
            const venueLat = <?php echo $venue_lat ? $venue_lat : 'null'; ?>;
            const venueLng = <?php echo $venue_lng ? $venue_lng : 'null'; ?>;
            const venueName = "<?php echo htmlspecialchars($venue_name); ?>";
            const venueAddr = "<?php echo htmlspecialchars($venue_address); ?>";

            // Dark Mode Styles for Map
            const darkMapStyle = [
                { elementType: "geometry", stylers: [{ color: "#242f3e" }] },
                { elementType: "labels.text.stroke", stylers: [{ color: "#242f3e" }] },
                { elementType: "labels.text.fill", stylers: [{ color: "#746855" }] },
                { featureType: "administrative.locality", elementType: "labels.text.fill", stylers: [{ color: "#d59563" }] },
                { featureType: "poi", elementType: "labels.text.fill", stylers: [{ color: "#d59563" }] },
                { featureType: "poi.park", elementType: "geometry", stylers: [{ color: "#263c3f" }] },
                { featureType: "poi.park", elementType: "labels.text.fill", stylers: [{ color: "#6b9a76" }] },
                { featureType: "road", elementType: "geometry", stylers: [{ color: "#38414e" }] },
                { featureType: "road", elementType: "geometry.stroke", stylers: [{ color: "#212a37" }] },
                { featureType: "road", elementType: "labels.text.fill", stylers: [{ color: "#9ca5b3" }] },
                { featureType: "road.highway", elementType: "geometry", stylers: [{ color: "#746855" }] },
                { featureType: "road.highway", elementType: "geometry.stroke", stylers: [{ color: "#1f2835" }] },
                { featureType: "road.highway", elementType: "labels.text.fill", stylers: [{ color: "#f3d19c" }] },
                { featureType: "water", elementType: "geometry", stylers: [{ color: "#17263c" }] },
                { featureType: "water", elementType: "labels.text.fill", stylers: [{ color: "#515c6d" }] },
                { featureType: "water", elementType: "labels.text.stroke", stylers: [{ color: "#17263c" }] }
            ];

            const mapOptions = {
                zoom: 15,
                center: { lat: 5.6037, lng: -0.1870 }, // Default Accra
                styles: darkMapStyle,
                disableDefaultUI: false,
                streetViewControl: false,
                mapTypeControl: false
            };

            const map = new google.maps.Map(document.getElementById("map"), mapOptions);

            // Logic: Prefer Lat/Lng, fallback to Address Geocoding
            if (venueLat && venueLng) {
                const pos = { lat: venueLat, lng: venueLng };
                map.setCenter(pos);
                new google.maps.Marker({
                    position: pos,
                    map: map,
                    title: venueName,
                    icon: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png' 
                });
            } else if (venueAddr && venueAddr !== 'Accra, Ghana') {
                // Fallback: Geocode
                const geocoder = new google.maps.Geocoder();
                const fullAddress = venueName + ", " + venueAddr; 
                
                geocoder.geocode({ 'address': fullAddress }, function(results, status) {
                    if (status === 'OK') {
                        map.setCenter(results[0].geometry.location);
                        new google.maps.Marker({
                            map: map,
                            position: results[0].geometry.location,
                            title: venueName
                        });
                    } else {
                        console.error('Geocode failed: ' + status);
                    }
                });
            }
        }

        // Initialize map only if API loaded
        window.addEventListener('load', function() {
            if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
                initMap();
            }
        });
    </script>
</body>
</html>