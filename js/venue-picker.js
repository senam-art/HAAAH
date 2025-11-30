// Global state
let map;
let allVenues = [];
let currentVenueIndex = 0;
let selectedVenue = null;
let venueMarker = null;
let userMarker = null;

// Initialize map and venue picker
function initVenuePicker() {
    if (typeof google === 'undefined' || !google.maps) {
        console.error('Google Maps API not loaded.');
        document.getElementById('venue-card-container').innerHTML =
            '<p class="text-red-400 text-center py-8">Google Maps failed to load. Please check your API key.</p>';
        return;
    }

    const defaultLatLng = { lat: 5.6037, lng: -0.1870 }; // Accra
    map = new google.maps.Map(document.getElementById('venue-map'), {
        center: defaultLatLng,
        zoom: 13,
        disableDefaultUI: true,
        zoomControl: true,
        gestureHandling: 'greedy'
    });

    // Show user's current location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            pos => {
                const userPos = { lat: pos.coords.latitude, lng: pos.coords.longitude };
                userMarker = new google.maps.Marker({
                    position: userPos,
                    map,
                    title: 'Your Location',
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 8,
                        fillColor: '#4285F4',
                        fillOpacity: 1,
                        strokeWeight: 2,
                        strokeColor: 'white'
                    }
                });
            },
            err => console.warn('Geolocation failed:', err)
        );
    }

    fetchAllVenues();
}

// Fetch all venues
function fetchAllVenues() {
    fetch('../actions/get_venues_action.php?action=all')
        .then(res => res.json())
        .then(data => {
            const venues = data?.data?.data; // Handle nested structure
            if (!Array.isArray(venues) || !venues.length) {
                throw new Error(data?.data?.message || 'No venues returned');
            }
            allVenues = venues;
            currentVenueIndex = 0;
            displayVenue();
        })
        .catch(err => {
            console.error('Fetch error:', err);
            document.getElementById('venue-card-container').innerHTML =
                '<p class="text-red-400 text-center py-8">Failed to load venues. Please try again.</p>';
        });
}

// Display current venue card
function displayVenue() {
    if (!allVenues.length) {
        document.getElementById('venue-card-container').innerHTML =
            '<p class="text-gray-400 text-center py-8">No venues available.</p>';
        return;
    }

    const venue = allVenues[currentVenueIndex];
    selectedVenue = venue;

    const imageUrl = venue.image_urls?.[0] ?? 'https://via.placeholder.com/400x250?text=No+Image';
    const amenitiesHtml = venue.amenities?.length
        ? venue.amenities.map(a => `<span class="text-xs bg-brand-accent/20 text-brand-accent px-2 py-1 rounded">${a}</span>`).join(' ')
        : '<span class="text-xs text-gray-500">No amenities listed</span>';

    const html = `
        <div class="space-y-4">
            <div class="relative rounded-xl overflow-hidden h-48 bg-brand-dark border border-white/10">
                <img src="${imageUrl}" alt="${venue.name}" class="w-full h-full object-cover">
                <div class="absolute top-3 right-3 bg-black/70 px-3 py-1 rounded-full flex items-center gap-1 text-xs font-bold">
                    <i data-lucide="star" size="12" class="text-yellow-400"></i>
                    <span>${venue.rating || 'N/A'}</span>
                    <span class="text-gray-500">(${venue.total_reviews || 0})</span>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-bold mb-1">${venue.name}</h3>
                <p class="text-xs text-gray-400 mb-2">
                    <i data-lucide="map-pin" size="12"></i>
                    ${venue.address}
                </p>
                <p class="text-sm text-gray-300 mb-3">${venue.description || 'No description available.'}</p>
                <div class="flex gap-2 mb-4 flex-wrap">${amenitiesHtml}</div>
                <div class="flex justify-between items-center bg-brand-accent/10 border border-brand-accent/20 rounded-lg p-3 mb-4">
                    <span class="text-sm font-bold">Cost per hour:</span>
                    <span class="text-xl font-black text-brand-accent">GHS ${parseFloat(venue.cost_per_hour).toFixed(2)}</span>
                </div>
            </div>

            <div class="flex justify-between items-center mb-4">
                <button onclick="previousVenue()" class="px-4 py-2 bg-white/5 border border-white/10 rounded-lg">‹</button>
                <span class="text-xs text-gray-400">${currentVenueIndex + 1} / ${allVenues.length}</span>
                <button onclick="nextVenue()" class="px-4 py-2 bg-white/5 border border-white/10 rounded-lg">›</button>
            </div>

            <button onclick="selectVenue()" class="w-full py-3 bg-brand-accent text-black font-bold rounded-lg">
                Select This Venue
            </button>
        </div>
    `;

    document.getElementById('venue-card-container').innerHTML = html;
    lucide.createIcons();

    updateMapPin(venue);
}

// Map marker update
function updateMapPin(venue) {
    if (!venue.latitude || !venue.longitude) return;

    const pos = { lat: parseFloat(venue.latitude), lng: parseFloat(venue.longitude) };

    if (venueMarker) {
        venueMarker.setPosition(pos);
    } else {
        venueMarker = new google.maps.Marker({ map, position: pos, title: venue.name });
    }

    map.setCenter(pos);
    map.setZoom(15);
}

// Navigation
function nextVenue() {
    if (!allVenues.length) return;
    currentVenueIndex = (currentVenueIndex + 1) % allVenues.length;
    displayVenue();
}

function previousVenue() {
    if (!allVenues.length) return;
    currentVenueIndex = (currentVenueIndex - 1 + allVenues.length) % allVenues.length;
    displayVenue();
}

// Select venue
function selectVenue() {
    if (!selectedVenue) return;

    ['id', 'name', 'cost', 'address', 'lat', 'lng'].forEach(field => {
        const el = document.getElementById(`selected_venue_${field}`);
        if (el) el.value = selectedVenue[`venue_${field}`] ?? selectedVenue[field];
    });

    const displayEl = document.getElementById('selected-venue-display');
    if (displayEl) {
        displayEl.innerHTML = `<p class="text-sm"><strong>${selectedVenue.name}</strong> - GHS ${parseFloat(selectedVenue.cost_per_hour).toFixed(2)}/hr</p>`;
    }

    alert(`Selected: ${selectedVenue.name}`);
}

// Init on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('venue-map')) initVenuePicker();
});
