<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event - Haaah Sports</title>
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
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap');
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #0f0f13; 
            color: white; 
        }
        #venue-map {
            height: 400px;
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
        }
    </style>
</head>
<body class="selection:bg-brand-accent selection:text-black pb-20">

    <!-- Simple Header -->
    <nav class="border-b border-white/5 bg-brand-card px-6 py-4 flex justify-between items-center sticky top-0 z-50">
        <a href="index.php" class="flex items-center gap-2 text-gray-400 hover:text-white">
            <i data-lucide="arrow-left" size="20"></i> Back to Dashboard
        </a>
        <h1 class="font-bold">Create New Event</h1>
        <div class="w-8"></div> <!-- Spacer -->
    </nav>

    <div class="max-w-6xl mx-auto px-6 py-8">
        
        <!-- Progress Bar -->
        <div class="flex items-center justify-between mb-8 text-sm font-medium text-gray-500">
            <span class="text-brand-accent">1. Details</span>
            <span>2. Venue</span>
            <span>3. Rules</span>
            <span>4. Review</span>
        </div>
        <div class="h-1 bg-white/10 rounded-full mb-12 overflow-hidden">
            <div class="h-full w-1/4 bg-brand-accent"></div>
        </div>

        <form action="../actions/create_event.php" method="POST" class="space-y-8">
            
            <!-- Section 1: Basic Info -->
            <div class="bg-brand-card p-6 rounded-2xl border border-white/5">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <i data-lucide="trophy" class="text-brand-accent"></i> Match Basics
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">Event Title</label>
                        <input type="text" name="title" placeholder="e.g. Tuesday Night 5-a-side" required class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 focus:border-brand-accent focus:outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Sport</label>
                            <select name="sport" required class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 focus:border-brand-accent focus:outline-none">
                                <option value="Football">Football (Soccer)</option>
                                <option disabled>Basketball - Coming Soon</option>
                                <option disabled>Volleyball - Coming soon</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Format</label>
                            <select name="format" required class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 focus:border-brand-accent focus:outline-none">
                                <option value="5v5">5 vs 5</option>
                                <option value="7v7">7 vs 7</option>
                                <option value="11v11">11 vs 11</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Date</label>
                            <input type="date" name="event_date" id="event_date" required
                                class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 focus:border-brand-accent focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Time</label>
                            <input type="time" name="event_time" id="event_time" required
                                class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 focus:border-brand-accent focus:outline-none">
                        </div>
                    </div>

                </div>
            </div>

            <!-- Section 2: Venue Selection with Map -->
            <div class="bg-brand-card p-6 rounded-2xl border border-white/5">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <i data-lucide="map-pin" class="text-brand-accent"></i> Select Venue
                </h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Venue Card Browser -->
                    <div>
                        <div id="venue-card-container" class="min-h-[400px] flex items-center justify-center">
                            <p class="text-gray-400">Loading venues...</p>
                        </div>
                        <br>
                        <p class="text-xs text-gray-500 text-center">
                            <i data-lucide="info" size="12" class="inline"></i> 
                            Browse venues using the arrows. Map shows location.
                        </p>
                    </div>
                    <!-- Map View -->
                    <div>
                        <div id="venue-map" class="border border-white/10 mb-4"></div>
    
                    </div>
                </div>

                <!-- Hidden form fields for selected venue -->
                <input type="hidden" id="selected_venue_id" name="venue_id" required>
                <input type="hidden" id="selected_venue_name" name="venue_name">
                <input type="hidden" id="selected_venue_cost" name="venue_cost">
                <input type="hidden" id="selected_venue_address" name="venue_address">
                <input type="hidden" id="selected_venue_lat" name="venue_lat">
                <input type="hidden" id="selected_venue_lng" name="venue_lng">

                <!-- Selected Venue Display -->
                <div id="selected-venue-display" class="mt-4 p-4 bg-brand-accent/10 border border-brand-accent/20 rounded-xl hidden">
                    <p class="text-sm text-gray-400">Selected venue will appear here</p>
                </div>
            </div>

            <!-- Section 3: The "Economics" (Threshold Logic) -->
            <div class="bg-brand-card p-6 rounded-2xl border border-white/5">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <i data-lucide="wallet" class="text-brand-accent"></i> Cost & Thresholds
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Cost per Player (GHS)</label>
                            <input type="number" name="cost_per_player" id="cost_per_player" value="30" min="1" step="0.01" required class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 focus:border-brand-accent font-mono text-xl">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Min. Players to "Green Light"</label>
                            <div class="flex items-center gap-4">
                                <input type="range" name="min_players" id="min_players" min="8" max="22" value="10" required class="flex-1 accent-brand-accent h-2 bg-brand-dark rounded-lg appearance-none cursor-pointer">
                                <span id="min_players_display" class="font-mono text-xl font-bold">10</span>
                            </div>
                        </div>
                    </div>

                    <!-- Live Calculator -->
                    <div class="bg-black/40 rounded-xl p-4 border border-white/5 flex flex-col justify-center">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-400">Venue Cost</span>
                            <span class="font-mono" id="display_venue_cost">GHS 0.00</span>
                        </div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-400">Haaah Fee (10%)</span>
                            <span class="font-mono" id="display_platform_fee">GHS 0.00</span>
                        </div>
                        <div class="h-px bg-white/10 my-2"></div>
                        <div class="flex justify-between items-center text-brand-accent">
                            <span class="font-bold">Total needed</span>
                            <span class="font-mono font-bold text-xl" id="display_total_needed">GHS 0.00</span>
                        </div>
                        <div class="mt-4 text-xs text-center text-gray-500 bg-white/5 py-2 rounded">
                            <i data-lucide="info" size="12" class="inline mb-0.5"></i> 
                            <span id="commission_display">Select a venue to see commission calculation</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="button" class="flex-1 py-4 rounded-xl font-bold bg-white/5 hover:bg-white/10 transition-colors">Save Draft</button>
                <button type="submit" class="flex-[2] py-4 rounded-xl font-bold bg-brand-accent text-black hover:bg-[#2fe080] transition-colors shadow-lg shadow-brand-accent/20">
                    Publish Event
                </button>
            </div>

        </form>
    </div>

    <script>
        lucide.createIcons();

        // Update min players display
        document.getElementById('min_players').addEventListener('input', function() {
            document.getElementById('min_players_display').textContent = this.value;
            updateCalculator();
        });

        // Update calculator when cost per player changes
        document.getElementById('cost_per_player').addEventListener('input', updateCalculator);

        function updateCalculator() {
            const venueCostInput = document.getElementById('selected_venue_cost');
            const costPerPlayer = parseFloat(document.getElementById('cost_per_player').value) || 0;
            const minPlayers = parseInt(document.getElementById('min_players').value) || 10;

            if (venueCostInput && venueCostInput.value) {
                const venueCost = parseFloat(venueCostInput.value);
                const platformFee = venueCost * 0.10;
                const totalNeeded = venueCost + platformFee;
                const totalRevenue = costPerPlayer * minPlayers;
                const commission = totalRevenue - totalNeeded;

                document.getElementById('display_venue_cost').textContent = `GHS ${venueCost.toFixed(2)}`;
                document.getElementById('display_platform_fee').textContent = `GHS ${platformFee.toFixed(2)}`;
                document.getElementById('display_total_needed').textContent = `GHS ${totalNeeded.toFixed(2)}`;
                
                const commissionText = commission >= 0 
                    ? `With ${minPlayers} players paying GHS ${costPerPlayer.toFixed(2)}, you earn <span class="text-white font-bold">GHS ${commission.toFixed(2)}</span> commission.`
                    : `With ${minPlayers} players paying GHS ${costPerPlayer.toFixed(2)}, you're <span class="text-red-400 font-bold">GHS ${Math.abs(commission).toFixed(2)} short</span>.`;
                
                document.getElementById('commission_display').innerHTML = commissionText;
            }
        }
    </script>

    <!-- Venue Picker JavaScript -->
    <script src="../js/venue-picker.js"></script>
</body>
</html>