<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event - Haaah Sports</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDgP6xqZcN4y50x2kq8cbytyD-k4OY1Sis&libraries=places"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { brand: { dark: '#0f0f13', card: '#1a1a23', accent: '#3dff92' } },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #0f0f13; color: white; }
        #venue-map { width: 100%; border-radius: 12px; overflow: hidden; height: 300px; }
        @media (min-width: 1024px) { #venue-map { height: 500px; } }
    </style>
</head>
<body class="selection:bg-brand-accent selection:text-black pb-20">

    <nav class="border-b border-white/5 bg-brand-card px-4 md:px-6 py-4 flex justify-between items-center sticky top-0 z-50">
        <a href="index.php" class="flex items-center gap-2 text-gray-400 hover:text-white text-sm md:text-base">
            <i data-lucide="arrow-left" size="20"></i> <span class="hidden sm:inline">Back to Dashboard</span><span class="sm:hidden">Back</span>
        </a>
        <h1 class="font-bold text-sm md:text-base">Create New Event</h1>
        <div class="w-8"></div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 md:px-6 py-8">
        <div class="flex items-center justify-between mb-8 text-xs md:text-sm font-medium text-gray-500">
            <span class="text-brand-accent">1. Details</span>
            <span>2. Venue</span>
            <span>3. Rules</span>
            <span>4. Review</span>
        </div>
        <div class="h-1 bg-white/10 rounded-full mb-12 overflow-hidden">
            <div class="h-full w-1/4 bg-brand-accent"></div>
        </div>

        <form id="eventForm" action="../actions/create_event_action.php" method="POST" class="space-y-8" novalidate>
            
            <div class="bg-brand-card p-4 md:p-6 rounded-2xl border border-white/5">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <i data-lucide="trophy" class="text-brand-accent"></i> Match Basics
                </h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">Event Title</label>
                        <input type="text" name="title" required placeholder="e.g. Tuesday Night 5-a-side" class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 focus:border-brand-accent focus:outline-none">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Sport</label>
                            <select name="sport" required class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 focus:border-brand-accent focus:outline-none">
                                <option value="Football">Football (Soccer)</option>
                                <option disabled>Basketball - Coming Soon</option>
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
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Date</label>
                            <input type="date" name="event_date" id="event_date" required class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 focus:border-brand-accent focus:outline-none [color-scheme:dark]">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Time</label>
                            <input type="time" name="event_time" id="event_time" required class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 focus:border-brand-accent focus:outline-none [color-scheme:dark]">
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-brand-card p-4 md:p-6 rounded-2xl border border-white/5">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2"><i data-lucide="map-pin" class="text-brand-accent"></i> Select Venue</h2>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="flex flex-col h-full">
                        <div id="venue-card-container" class="flex-1 min-h-[300px] flex flex-col items-center justify-center border border-white/5 rounded-xl bg-brand-dark/50 p-4">
                            <p class="text-gray-400">Loading venues...</p>
                        </div>
                        <p class="text-xs text-gray-500 text-center mt-3"><i data-lucide="info" size="12" class="inline"></i> Browse venues using the arrows.</p>
                    </div>
                    <div><div id="venue-map" class="border border-white/10 shadow-xl"></div></div>
                </div>

                <input type="hidden" id="selected_venue_id" name="venue_id" required>
                <input type="hidden" id="selected_venue_name" name="venue_name">
                <input type="hidden" id="selected_venue_cost" name="venue_cost">
                <input type="hidden" id="selected_venue_address" name="venue_address">
                <input type="hidden" id="selected_venue_lat" name="venue_lat">
                <input type="hidden" id="selected_venue_lng" name="venue_lng">

                <div id="selected-venue-display" class="mt-4 p-4 bg-brand-accent/10 border border-brand-accent/20 rounded-xl hidden"></div>
            </div>

            <div class="bg-brand-card p-4 md:p-6 rounded-2xl border border-white/5">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2"><i data-lucide="wallet" class="text-brand-accent"></i> Cost & Thresholds</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Cost per Player (GHS)</label>
                            <input type="number" name="cost_per_player" id="cost_per_player" value="30" min="1" step="0.01" required class="w-full bg-brand-dark border border-white/10 rounded-xl p-3 focus:border-brand-accent font-mono text-xl">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Min. Players</label>
                            <div class="flex items-center gap-4">
                                <input type="range" name="min_players" id="min_players" min="8" max="22" value="10" class="flex-1 accent-brand-accent h-2 bg-brand-dark rounded-lg cursor-pointer">
                                <span id="min_players_display" class="font-mono text-xl font-bold">10</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-black/40 rounded-xl p-4 border border-white/5 flex flex-col justify-center">
                        <div class="flex justify-between items-center mb-2"><span class="text-sm text-gray-400">Venue Cost</span><span class="font-mono" id="display_venue_cost">GHS 0.00</span></div>
                        <div class="flex justify-between items-center mb-2"><span class="text-sm text-gray-400">Haaah Fee (10%)</span><span class="font-mono" id="display_platform_fee">GHS 0.00</span></div>
                        <div class="h-px bg-white/10 my-2"></div>
                        <div class="flex justify-between items-center text-brand-accent"><span class="font-bold">Total needed</span><span class="font-mono font-bold text-xl" id="display_total_needed">GHS 0.00</span></div>
                        <div class="mt-4 text-xs text-center text-gray-500 bg-white/5 py-2 rounded"><span id="commission_display">Select a venue to see commission calculation</span></div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 pt-4">
                <button type="button" class="w-full sm:flex-1 py-4 rounded-xl font-bold bg-white/5 hover:bg-white/10 transition-colors">Save Draft</button>
                <button type="submit" class="w-full sm:flex-[2] py-4 rounded-xl font-bold bg-brand-accent text-black hover:bg-[#2fe080] transition-colors shadow-lg shadow-brand-accent/20">Publish Event</button>
            </div>
        </form>
    </div>

    <script>lucide.createIcons();</script>
    <script src="../js/create-event.js"></script> 
    <script src="../js/venue-picker.js"></script>
</body>
</html>