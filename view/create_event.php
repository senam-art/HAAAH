<?php
// view/create_event.php

// 1. Bootstrap & Security
require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/controllers/user_controller.php';

check_login();

// Fetch user for avatar in header
$user_id = get_user_id();
$userController = new UserController();
$current_user = $userController->get_user_by_id_ctr($user_id);
$initials = strtoupper(substr($current_user['user_name'], 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event - Haaah Sports</title>
    
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
    
    <!-- Link JS Files EARLY so functions are defined before Google Maps calls them -->
    <script src="../js/venue-picker.js"></script>
    <script src="../js/create-event.js"></script>
</head>
<body class="selection:bg-brand-accent selection:text-black pb-20">

    <!-- Navbar -->
    <nav class="border-b border-white/5 bg-brand-dark px-6 py-4 sticky top-0 z-50 backdrop-blur-md">
        <div class="max-w-5xl mx-auto flex justify-between items-center">
            <a href="homepage.php" class="flex items-center gap-2 text-gray-400 hover:text-white transition-colors">
                <i data-lucide="arrow-left" size="20"></i> Cancel
            </a>
            <h1 class="font-black text-lg tracking-tight">Create Match</h1>
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-brand-accent to-brand-purple flex items-center justify-center font-bold text-sm text-black">
                <?php echo $initials; ?>
            </div>
        </div>
    </nav>

    <div class="max-w-5xl mx-auto px-4 py-8">
        
        <form id="eventForm" action="../actions/create_event_action.php" method="POST" data-platform-fee="0.10" data-commitment-fee="0.20">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- LEFT COLUMN: Details & Venue -->
                <div class="lg:col-span-2 space-y-8">
                    
                    <!-- 1. Basic Details -->
                    <div class="bg-brand-card p-6 rounded-2xl border border-white/5">
                        <h3 class="text-xl font-bold mb-6 flex items-center gap-2">
                            <span class="bg-white/10 w-6 h-6 rounded-full flex items-center justify-center text-xs">1</span> Match Details
                        </h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Event Title</label>
                                <input type="text" name="title" placeholder="e.g. Sunday Morning 5-a-side" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 text-white focus:border-brand-accent focus:outline-none transition-colors" required>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Sport</label>
                                    <select name="sport" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 text-white focus:border-brand-accent focus:outline-none appearance-none">
                                        <option value="Football">Football</option>
                                        <option value="Basketball">Basketball</option>
                                        <option value="Tennis">Tennis</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Format</label>
                                    <select name="format" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 text-white focus:border-brand-accent focus:outline-none appearance-none">
                                        <option value="5-a-side">5-a-side</option>
                                        <option value="7-a-side">7-a-side</option>
                                        <option value="11-a-side">11-a-side</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Date</label>
                                    <input type="date" id="event_date" name="event_date" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 text-white focus:border-brand-accent focus:outline-none" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Time</label>
                                    <input type="time" id="event_time" name="event_time" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 text-white focus:border-brand-accent focus:outline-none" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Duration</label>
                                    <select id="duration" name="duration" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 text-white focus:border-brand-accent focus:outline-none appearance-none">
                                        <option value="1">1 Hour</option>
                                        <option value="2">2 Hours</option>
                                        <option value="3">3 Hours</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Venue Picker -->
                    <div class="bg-brand-card p-6 rounded-2xl border border-white/5">
                        <h3 class="text-xl font-bold mb-6 flex items-center gap-2">
                            <span class="bg-white/10 w-6 h-6 rounded-full flex items-center justify-center text-xs">2</span> Select Venue
                        </h3>

                        <!-- Interactive Map Container -->
                        <div id="venue-map" class="w-full h-64 rounded-xl bg-gray-800 mb-4 border border-white/10"></div>
                        
                        <!-- Venue Info Card (Populated by JS) -->
                        <div id="venue-card-container" class="bg-brand-dark p-4 rounded-xl border border-white/5 min-h-[150px] flex items-center justify-center">
                            <span class="text-gray-500 text-sm animate-pulse">Loading venues...</span>
                        </div>

                        <!-- Hidden Inputs to store selection for PHP -->
                        <input type="hidden" name="selected_venue_id" id="selected_venue_id" required>
                        <input type="hidden" name="selected_venue_name" id="selected_venue_name">
                        <input type="hidden" id="selected_venue_cost"> <!-- Used by JS Calc -->
                    </div>

                </div>

                <!-- RIGHT COLUMN: Calculator & Checkout -->
                <div class="space-y-6">
                    
                    <!-- 3. Cost & Players -->
                    <div class="bg-brand-card p-6 rounded-2xl border border-white/5 sticky top-24">
                        <h3 class="text-xl font-bold mb-6 flex items-center gap-2">
                            <span class="bg-white/10 w-6 h-6 rounded-full flex items-center justify-center text-xs">3</span> Financials
                        </h3>

                        <!-- Selected Venue Display (Initially Hidden) -->
                        <div id="selected-venue-display" class="hidden mb-6 p-3 bg-brand-accent/10 border border-brand-accent/20 rounded-lg">
                            <!-- Populated by JS -->
                        </div>

                        <div class="space-y-4 mb-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Entry Fee per Player (GHS)</label>
                                <input type="number" id="cost_per_player" name="cost_per_player" min="0" step="0.50" value="30.00" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 text-white font-mono font-bold focus:border-brand-accent focus:outline-none transition-colors" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Minimum Players</label>
                                <input type="number" id="min_players" name="min_players" min="2" max="50" value="10" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 text-white font-mono font-bold focus:border-brand-accent focus:outline-none transition-colors" required>
                                <p class="text-[10px] text-gray-500 mt-1">Green Light triggers at <span id="min_players_display">10</span> players.</p>
                            </div>
                        </div>

                        <!-- Real-time Breakdown -->
                        <div class="space-y-2 text-sm text-gray-400 pt-4 border-t border-white/5">
                            <div class="flex justify-between">
                                <span>Venue Cost (<span id="calc_duration_label">1h</span>)</span>
                                <span id="display_venue_cost" class="font-mono">--</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Platform Fee (10%)</span>
                                <span id="display_platform_fee" class="font-mono">--</span>
                            </div>
                            <div class="flex justify-between font-bold text-white pt-2">
                                <span>Total Needed</span>
                                <span id="display_total_needed" class="font-mono">--</span>
                            </div>
                        </div>

                        <!-- Commission / Profit Display -->
                        <div id="commission_display" class="mt-4 text-xs text-center p-2 rounded bg-white/5 border border-white/5">
                            Select a venue to see breakdown
                        </div>

                        <!-- Commitment Fee Section -->
                        <div class="mt-6 p-4 bg-brand-purple/10 border border-brand-purple/30 rounded-xl">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs font-bold text-brand-purple uppercase">Commitment Fee (20%)</span>
                                <span id="display_commitment_fee" class="font-black text-lg text-white">--</span>
                            </div>
                            <p class="text-[10px] text-gray-400 leading-tight">
                                Pay this now to reserve the slot. Refunded if the game doesn't hit the green light threshold.
                            </p>
                            <!-- Hidden input for backend -->
                            <input type="hidden" name="hidden_commitment_fee" id="hidden_commitment_fee" value="0">
                        </div>

                        <button type="submit" id="submitBtn" class="w-full mt-6 py-4 bg-gray-600 text-gray-300 font-bold rounded-xl cursor-not-allowed transition-all" disabled>
                            Select a Venue First
                        </button>

                    </div>
                </div>

            </div>
        </form>
    </div>

    <!-- Google Maps API -->
    <!-- KEY INSERTED HERE -->
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDgP6xqZcN4y50x2kq8cbytyD-k4OY1Sis&callback=initVenuePicker"></script>
    
    <script>lucide.createIcons();</script>

</body>
</html>