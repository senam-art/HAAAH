document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('games-container');
    const searchInput = document.getElementById('search-input');
    const searchBtn = document.getElementById('search-btn');

    // 1. Initial Fetch - Check for data passed from Landing Page
    // These variables (INITIAL_*) are defined in view/index.php
    let initialQuery = '';
    if (typeof INITIAL_SEARCH !== 'undefined' && INITIAL_SEARCH) {
        initialQuery = INITIAL_SEARCH;
        if(searchInput) searchInput.value = INITIAL_SEARCH; // Pre-fill search bar
    }
    
    // Pass coordinates if they exist
    const lat = (typeof INITIAL_LAT !== 'undefined') ? INITIAL_LAT : '';
    const lng = (typeof INITIAL_LNG !== 'undefined') ? INITIAL_LNG : '';

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
        // Show Loading State
        container.innerHTML = `
            <div class="col-span-full flex justify-center py-20">
                <svg class="animate-spin h-8 w-8 text-brand-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>`;

        // Build URL with optional coordinates
        let url = `../actions/fetch_games_action.php?search=${encodeURIComponent(query)}`;
        if (lat && lng) {
            url += `&lat=${lat}&lng=${lng}`;
        }

        fetch(url)
            .then(response => response.json())
            .then(json => {
                if (json.success && json.data.length > 0) {
                    renderGames(json.data);
                } else {
                    container.innerHTML = `
                        <div class="col-span-full text-center py-20 text-gray-500">
                            <i data-lucide="ghost" size="48" class="mx-auto mb-4 opacity-50"></i>
                            <p>No active games found matching "${query}".</p>
                            <a href="../view/create-event.php" class="text-brand-accent font-bold hover:underline">Host one yourself!</a>
                        </div>`;
                    if(window.lucide) lucide.createIcons();
                }
            })
            .catch(err => {
                console.error('Error fetching games:', err);
                container.innerHTML = `<p class="col-span-full text-center text-red-500">Failed to load games. Try again.</p>`;
            });
    }

    // 4. Render Function
    function renderGames(events) {
        container.innerHTML = events.map(event => {
            // DATABASE NOTE: Ensure 'status' and 'current_players' exist in your DB or backend logic
            const currentPlayers = event.current_players || 0;
            const isConfirmed = event.status === 'confirmed';
            
            const statusColor = isConfirmed ? 'text-brand-accent' : 'text-yellow-500';
            const statusIcon = isConfirmed ? 'check-circle' : 'clock';
            const statusText = isConfirmed ? 'Confirmed' : `Needs ${event.spots_left || (event.min_players - currentPlayers)} more`;
            const icon = event.sport === 'Football' ? 'trophy' : 'activity';

            // Backend usually joins 'venues' table to get venue_name. 
            // If strictly using your columns, this might be missing unless backend handles it.
            const venueDisplay = event.venue_name || 'Unknown Venue';

            return `
                <div class="group relative p-5 bg-[#16161c] border border-white/5 rounded-2xl hover:border-brand-accent/30 transition-all flex flex-col animate-fade-in">
                    <div class="absolute top-4 right-4 text-center">
                        <div class="text-xs text-gray-400 font-bold mb-1 uppercase">Fee</div>
                        <div class="text-brand-accent font-black text-lg">GHS ${parseFloat(event.cost_per_player).toFixed(2)}</div>
                    </div>

                    <div class="flex items-start gap-4 mb-4">
                        <div class="w-12 h-12 bg-white/5 rounded-xl flex items-center justify-center text-gray-400">
                            <i data-lucide="${icon}" size="24"></i>
                        </div>
                        <div class="flex-1 pr-12">
                            <h4 class="font-bold text-lg text-white group-hover:text-brand-accent transition-colors line-clamp-1">
                                ${event.title}
                            </h4>
                            <div class="flex items-center gap-2 text-sm text-gray-400 mt-1 truncate">
                                <i data-lucide="map-pin" size="12"></i> ${venueDisplay}
                            </div>
                            <div class="flex items-center gap-3 text-sm text-gray-400 mt-1">
                                <span class="flex items-center gap-1"><i data-lucide="calendar" size="12"></i> ${event.formatted_date || event.event_date}</span>
                                <span class="flex items-center gap-1"><i data-lucide="clock" size="12"></i> ${event.formatted_time || event.event_time}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="bg-black/20 rounded-lg p-3 border border-white/5 mb-4 mt-auto">
                        <div class="flex justify-between text-xs mb-2">
                            <span class="${statusColor} font-bold flex items-center gap-1">
                                <i data-lucide="${statusIcon}" size="12"></i> ${statusText}
                            </span>
                            <span class="text-gray-400">${currentPlayers} / ${event.min_players}</span>
                        </div>
                        <div class="w-full bg-white/10 rounded-full h-2">
                            <div class="bg-${isConfirmed ? 'brand-accent' : 'yellow-500'} h-2 rounded-full transition-all duration-500" style="width: ${event.progress_percent || 0}%"></div>
                        </div>
                    </div>

                    <a href="event-profile.php?id=${event.event_id}" class="w-full py-3 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl text-center text-sm font-bold transition-colors">
                        View Details & Join
                    </a>
                </div>
            `;
        }).join('');
        
        // Re-initialize icons for new elements
        if(window.lucide) lucide.createIcons();
    }
});