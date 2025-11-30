let map;
let allVenues = [];
let currentVenueIndex = 0;
window.selectedVenue = null; 
let venueMarker = null;

document.addEventListener('DOMContentLoaded', () => { if (document.getElementById('venue-map')) initVenuePicker(); });

function initVenuePicker() {
    if (!window.google || !google.maps) { showVenueError('Google Maps failed to load.'); return; }
    const accra = { lat: 5.6037, lng: -0.1870 };
    map = new google.maps.Map(document.getElementById('venue-map'), { center: accra, zoom: 13, disableDefaultUI: true, zoomControl: true, gestureHandling: 'greedy' });
    fetchVenues();
}

function fetchVenues() {
    fetch('../actions/get_venues_action.php?action=all').then(res => res.json()).then(res => {
        allVenues = res?.data?.data || [];
        if (!allVenues.length) throw new Error('No venues');
        renderVenue();
    }).catch(() => showVenueError('Failed to load venues.'));
}

function renderVenue() {
    const container = document.getElementById('venue-card-container');
    if (!allVenues.length) return;

    window.selectedVenue = allVenues[currentVenueIndex];
    const venue = window.selectedVenue;
    
    // Button State Logic
    const selectedId = document.getElementById('selected_venue_id')?.value;
    const isSelected = (selectedId && selectedId == venue.venue_id);

    const btnText = isSelected ? 'Venue Selected ✓' : 'Select This Venue';
    const btnClass = isSelected 
        ? 'w-full py-3 bg-green-600 text-white font-bold rounded-lg shadow-lg shadow-green-900/20 transform scale-105 transition-all' 
        : 'w-full py-3 bg-brand-accent text-black font-bold rounded-lg'; 

    const image = venue.image_urls?.[0] ?? 'https://via.placeholder.com/400x250';
    const amenities = venue.amenities?.length ? venue.amenities.map(a => `<span class="text-xs px-2 py-1 rounded bg-brand-accent/20 text-brand-accent">${a}</span>`).join(' ') : '';

    container.innerHTML = `
        <div class="space-y-4">
            <img src="${image}" class="rounded-xl h-48 w-full object-cover"/>
            <div><h3 class="text-lg font-bold">${venue.name}</h3><p class="text-xs text-gray-400">${venue.address}</p></div>
            <div class="flex flex-wrap gap-2">${amenities}</div>
            <div class="flex justify-between items-center p-3 rounded-lg bg-brand-accent/10 border border-brand-accent/20"><span class="text-sm font-bold">Cost / hour</span><span class="text-brand-accent font-black text-lg">GHS ${Number(venue.cost_per_hour).toFixed(2)}</span></div>
            <div class="flex justify-between items-center"><button id="prevVenue" class="px-4 py-2 bg-white/5 rounded-lg">‹</button><span class="text-xs text-gray-400">${currentVenueIndex + 1} / ${allVenues.length}</span><button id="nextVenue" class="px-4 py-2 bg-white/5 rounded-lg">›</button></div>
            <button type="button" id="pickVenueBtn" class="${btnClass}">${btnText}</button>
        </div>
    `;
    bindVenueControls();
    updateMap(venue);
}

function bindVenueControls() {
    document.getElementById('prevVenue').onclick = () => { currentVenueIndex = (currentVenueIndex - 1 + allVenues.length) % allVenues.length; renderVenue(); };
    document.getElementById('nextVenue').onclick = () => { currentVenueIndex = (currentVenueIndex + 1) % allVenues.length; renderVenue(); };
    document.getElementById('pickVenueBtn').onclick = () => { if (window.applySelectedVenue) window.applySelectedVenue(); };
}

function updateMap(venue) {
    if (!venue.latitude) return;
    const pos = { lat: Number(venue.latitude), lng: Number(venue.longitude) };
    if (!venueMarker) venueMarker = new google.maps.Marker({ map, position: pos, title: venue.name });
    else venueMarker.setPosition(pos);
    map.panTo(pos); map.setZoom(15);
}

function showVenueError(msg) { document.getElementById('venue-card-container').innerHTML = `<p class="text-red-400 text-center py-8">${msg}</p>`; }