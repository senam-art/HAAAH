// js/find_game.js

// We wrap the code in an IIFE (Immediately Invoked Function Expression)
// instead of DOMContentLoaded, because this script is loaded dynamically
// and the DOM is likely already ready.
(function() {
    console.log("🎮 find_game.js loaded and running...");

    const container = document.getElementById('games-container');
    const searchInput = document.getElementById('search-input');
    const searchBtn = document.getElementById('search-btn');

    // Safety Check
    if (!container) {
        console.error("❌ Critical: #games-container not found in DOM.");
        return;
    }

    // 1. Initial Fetch - Check for data passed from Landing Page
    // We access the global variables attached to 'window' in homepage.php
    let initialQuery = '';
    
    // Check window.INITIAL_SEARCH explicitly
    if (typeof window.INITIAL_SEARCH !== 'undefined' && window.INITIAL_SEARCH) {
        initialQuery = window.INITIAL_SEARCH;
        if(searchInput) searchInput.value = window.INITIAL_SEARCH; // Pre-fill search bar
    }
    
    const lat = (typeof window.INITIAL_LAT !== 'undefined') ? window.INITIAL_LAT : '';
    const lng = (typeof window.INITIAL_LNG !== 'undefined') ? window.INITIAL_LNG : '';

    // Trigger Initial Fetch
    fetchGames(initialQuery, lat, lng);

    // 2. Search Listeners
    if (searchBtn) {
        searchBtn.addEventListener('click', () => {
            fetchGames(searchInput.value);
        });
    }
    
    if (searchInput) {
        searchInput.addEventListener('keyup', (e) => {
            if (e.key === 'Enter') fetchGames(searchInput.value);
        });
    }

    // 3. Fetch Function
    function fetchGames(query = '', lat = '', lng = '') {
        console.log(`🔍 Fetching games: Query='${query}', Lat=${lat}, Lng=${lng}`);

        // Show Loading State
        container.innerHTML = `
            <div class="col-span-full flex justify-center py-20">
                <svg class="animate-spin h-8 w-8 text-brand-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>`;

        // Build URL relative to view/homepage.php
        let url = `../actions/fetch_games_action.php?search=${encodeURIComponent(query)}`;
        if (lat && lng) {
            url += `&lat=${lat}&lng=${lng}`;
        }

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text(); // Get text first to debug if needed
            })
            .then(text => {
                try {
                    return JSON.parse(text); // Try parsing JSON
                } catch (e) {
                    console.error("❌ Invalid JSON response:", text);
                    throw new Error("Server returned invalid JSON");
                }
            })
            .then(json => {
                if (json.success && Array.isArray(json.data) && json.data.length > 0) {
                    renderGames(json.data);
                } else {
                    // Empty State
                    container.innerHTML = `
                        <div class="col-span-full text-center py-20 text-gray-500">
                            <div class="flex justify-center mb-4 opacity-50"><i data-lucide="ghost" width="48" height="48"></i></div>
                            <p class="mb-2">No active games found matching "${query}".</p>
                            <a href="create_event.php" class="text-brand-accent font-bold hover:underline">Host one yourself!</a>
                        </div>`;
                    if(window.lucide) lucide.createIcons();
                }
            })
            .catch(err => {
                console.error('❌ Error fetching games:', err);
                container.innerHTML = `
                    <div class="col-span-full text-center py-12">
                        <p class="text-red-500 font-bold">Unable to load games</p>
                        <p class="text-xs text-gray-500 mt-1">${err.message}</p>
                        <button onclick="location.reload()" class="mt-4 px-4 py-2 bg-white/5 hover:bg-white/10 rounded-lg text-xs font-bold transition-colors">Reload Page</button>
                    </div>`;
            });
    }

    // 4. Render Function
    function renderGames(events) {
        container.innerHTML = events.map(event => {
            // Data Sanitization
            const currentPlayers = parseInt(event.current_players) || 0;
            const minPlayers = parseInt(event.min_players) || 10;
            const cost = parseFloat(event.cost_per_player) || 0;
            
            // UI Logic: Check DB Status OR if Players meet Minimum
            const isConfirmed = event.status === 'confirmed' || (currentPlayers >= minPlayers);
            
            const statusColor = isConfirmed ? 'text-brand-accent' : 'text-yellow-500';
            const statusIcon = isConfirmed ? 'check-circle' : 'clock';
            const statusText = isConfirmed ? 'Confirmed' : `Needs ${Math.max(0, minPlayers - currentPlayers)} more`;
            const icon = (event.sport || 'Football') === 'Football' ? 'trophy' : 'activity';
            
            // Calculate Progress Bar Width
            let progressPercent = 0;
            if (minPlayers > 0) {
                progressPercent = Math.min(100, (currentPlayers / minPlayers) * 100);
            }

            const venueDisplay = event.venue_name || 'TBA';

            return `
                <div class="group relative p-5 bg-[#16161c] border border-white/5 rounded-2xl hover:border-brand-accent/30 transition-all flex flex-col animate-fade-in">
                    <div class="absolute top-4 right-4 text-center">
                        <div class="text-[10px] text-gray-500 font-bold mb-1 uppercase tracking-wider">Entry Fee</div>
                        <div class="text-brand-accent font-black text-lg">GHS ${cost.toFixed(2)}</div>
                    </div>

                    <div class="flex items-start gap-4 mb-4">
                        <div class="w-12 h-12 bg-white/5 rounded-xl flex items-center justify-center text-gray-400 group-hover:text-white transition-colors">
                            <i data-lucide="${icon}" width="24" height="24"></i>
                        </div>
                        <div class="flex-1 pr-12">
                            <h4 class="font-bold text-lg text-white group-hover:text-brand-accent transition-colors line-clamp-1">
                                ${event.title}
                            </h4>
                            <div class="flex items-center gap-2 text-sm text-gray-400 mt-1 truncate">
                                <i data-lucide="map-pin" width="12" height="12"></i> ${venueDisplay}
                            </div>
                            <div class="flex items-center gap-3 text-sm text-gray-400 mt-1">
                                <span class="flex items-center gap-1"><i data-lucide="calendar" width="12" height="12"></i> ${event.event_date}</span>
                                <span class="flex items-center gap-1"><i data-lucide="clock" width="12" height="12"></i> ${event.event_time}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="bg-black/20 rounded-lg p-3 border border-white/5 mb-4 mt-auto">
                        <div class="flex justify-between text-xs mb-2">
                            <span class="${statusColor} font-bold flex items-center gap-1">
                                <i data-lucide="${statusIcon}" width="12" height="12"></i> ${statusText}
                            </span>
                            <span class="text-gray-400">${currentPlayers} / ${minPlayers}</span>
                        </div>
                        <div class="w-full bg-white/10 rounded-full h-1.5">
                            <div class="${isConfirmed ? 'bg-brand-accent' : 'bg-yellow-500'} h-1.5 rounded-full transition-all duration-500" style="width: ${progressPercent}%"></div>
                        </div>
                    </div>

                    <a href="event-profile.php?id=${event.event_id}" class="w-full py-3 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl text-center text-sm font-bold transition-colors text-white hover:text-brand-accent">
                        View Details & Join
                    </a>
                </div>
            `;
        }).join('');
        
        // Re-initialize icons for new elements
        if(window.lucide) lucide.createIcons();
    }
})();