/* =========================================
   create-event.js
   Unified Logic for Maps, Venue Picking, 
   Calculator, and Form Submission + Paystack
   ========================================= */

console.log("✅ create-event.js loaded. Starting sequence...");

const UPLOADS_URL = "<?= UPLOADS_URL ?>"; // e.g., /~senam.dzomeku/uploads
// --- GLOBAL STATE ---
const STATE = {
    map: null,
    marker: null,
    allVenues: [],
    currentVenueIndex: 0,
    venuesLoaded: false,
    selectedVenueId: null,
    
    // Payment State
    pendingEventId: null,
    pendingAmount: 0.00,
    pendingTitle: ""
};

// --- DOM ELEMENTS ---
const DOM = {
    venueContainer: 'venue-card-container',
    venueMap: 'venue-map',
    inputs: {
        venueId: 'selected_venue_id',
        venueName: 'selected_venue_name',
        venueCost: 'selected_venue_cost',
        date: 'event_date',
        time: 'event_time',
        duration: 'duration',
        costPerPlayer: 'cost_per_player',
        minPlayers: 'min_players',
        commitmentFee: 'hidden_commitment_fee',
        title: 'input[name="title"]', // Helper selector for querySelector
        email: 'user_email_hidden'    // Helper selector for getElementById
    },
    display: {
        badge: 'selected-venue-display',
        venueCost: 'display_venue_cost',
        platformFee: 'display_platform_fee',
        totalNeeded: 'display_total_needed',
        commitmentFee: 'display_commitment_fee',
        profit: 'commission_display',
        durationLabel: 'calc_duration_label',
        loading: 'time-loading'
    },
    btn: {
        submit: 'submitBtn',
        paystack: 'paystack-btn'
    },
    form: 'eventForm',
    modal: {
        container: 'payment-modal',
        backdrop: 'modal-backdrop',
        content: 'modal-content',
        title: 'modal_event_title',
        amount: 'modal_amount_display'
    }
};

// =========================================
// 1. MAP INITIALIZATION
// =========================================

window.initVenueMap = function() {
    console.log("📍 Google Maps Callback received. Starting map render...");
    
    const mapEl = document.getElementById(DOM.venueMap);
    
    if (!mapEl) {
        console.error(`❌ Fatal: Map element #${DOM.venueMap} not found in DOM.`);
        return;
    }

    if (STATE.map) return; 

    try {
        const defaultCenter = { lat: 5.6037, lng: -0.1870 }; // Accra
        
        STATE.map = new google.maps.Map(mapEl, { 
            center: defaultCenter, 
            zoom: 13, 
            mapId: "VENUE_PICKER_MAP", 
            disableDefaultUI: true, 
            zoomControl: true, 
            gestureHandling: 'greedy' 
        });
        
        console.log("🗺️ Map successfully rendered.");

        if(STATE.allVenues.length > 0) {
            updateMapMarker(STATE.allVenues[STATE.currentVenueIndex]);
        }
    } catch (e) {
        console.error("❌ Error creating Google Map instance:", e);
        mapEl.innerHTML = '<div class="h-full w-full flex items-center justify-center bg-gray-800 text-red-400 text-xs">Map Failed to Load</div>';
    }
};

function loadGoogleMapsAPI() {
    if (window.google && window.google.maps) {
        console.log("ℹ️ Google Maps API already loaded. Initializing directly.");
        window.initVenueMap();
        return;
    }

    if (document.getElementById('gmaps-script')) {
        console.log("ℹ️ Script already injected, waiting for callback...");
        return;
    }

    console.log("🔄 Injecting Google Maps script tag...");
    const script = document.createElement('script');
    script.id = 'gmaps-script';
    script.src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyDgP6xqZcN4y50x2kq8cbytyD-k4OY1Sis&callback=initVenueMap&libraries=marker&v=beta";
    script.async = true;
    script.defer = true;
    
    script.onerror = () => {
        console.error("❌ Network Error: Failed to load Google Maps script.");
        const mapEl = document.getElementById(DOM.venueMap);
        if(mapEl) mapEl.innerHTML = '<div class="h-full w-full flex items-center justify-center bg-gray-800 text-gray-400 text-xs">Connection Error</div>';
    };

    document.head.appendChild(script);
}

// =========================================
// 2. APP STARTUP
// =========================================

document.addEventListener('DOMContentLoaded', () => {
    console.log("🚀 DOM Content Loaded. Initializing App...");
    
    const mapEl = document.getElementById(DOM.venueMap);
    if(mapEl) {
        mapEl.innerHTML = '<div class="h-full w-full flex items-center justify-center bg-gray-800 text-brand-accent animate-pulse text-xs">Loading Map...</div>';
    }

    initListeners();
    fetchVenues(); 
    loadGoogleMapsAPI();
    
    const payBtn = document.getElementById(DOM.btn.paystack);
    if(payBtn) {
        payBtn.addEventListener('click', payWithPaystack);
    }
});


// =========================================
// 3. VENUE LOGIC (Fetch, Render, Select)
// =========================================

function fetchVenues() {
    if (STATE.venuesLoaded) return;
    STATE.venuesLoaded = true;

    console.log("📡 Fetching venues from server...");
    
    const actionUrl = '../actions/get_venues_action.php?action=all';

    fetch(actionUrl)
        .then(async res => {
            if (!res.ok) throw new Error(`HTTP Error: ${res.status}`);
            const text = await res.text();
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error("❌ JSON Parse Failed. Raw Response:", text);
                throw new Error("Invalid JSON response from server");
            }
        })
        .then(res => {
            const rawData = res?.data?.data || [];
            console.log(`✅ Venues Loaded: ${rawData.length}`);

            STATE.allVenues = rawData.map(v => {
                if (!v.venue_id && v.id) v.venue_id = v.id;
                v.latitude = parseFloat(v.latitude);
                v.longitude = parseFloat(v.longitude);
                return v;
            });

            if (STATE.allVenues.length === 0) {
                showVenueError('No venues available.');
                return;
            }
            
            renderVenueCard();
        })
        .catch(err => {
            console.error("❌ Fetch Error:", err);
            showVenueError(`Failed to load venues.<br><span class="text-[10px] text-gray-400">${err.message}</span>`);
        });
}

function renderVenueCard() {
    const container = document.getElementById(DOM.venueContainer);
    if (!container || !STATE.allVenues.length) return;

    const venue = STATE.allVenues[STATE.currentVenueIndex];
    
    // Update marker (if map is ready)
    updateMapMarker(venue);

    const isSelected = (String(STATE.selectedVenueId) === String(venue.venue_id));

    const btnText = isSelected ? 'Venue Selected ✓' : 'Select This Venue';
    const btnIcon = isSelected ? 'check' : 'mouse-pointer-2';
    const btnClass = isSelected 
        ? 'flex-1 py-3 bg-green-600 text-white font-bold rounded-xl shadow-lg shadow-green-900/20 transform scale-[1.02] transition-all flex items-center justify-center gap-2' 
        : 'flex-1 py-3 bg-brand-accent text-black font-bold rounded-xl hover:bg-[#2fe080] transition-colors flex items-center justify-center gap-2'; 

    let imagesHtml = '';
    let dotsHtml = '';
    if (venue.image_urls) {
    let imgs = Array.isArray(venue.image_urls) ? venue.image_urls : [venue.image_urls];
    if (typeof venue.image_urls === 'string' && venue.image_urls.startsWith('[')) {
         try { imgs = JSON.parse(venue.image_urls); } catch(e) {}
    }

    if (imgs.length > 0) {
            imagesHtml = imgs.map(url => {
                // Fix URL dynamically: remove leading /uploads and prepend UPLOADS_URL
                const safeUrl = UPLOADS_URL + url.replace(/^\/uploads/, '');
                return `
                    <div class="flex-none w-full h-full snap-center relative">
                        <img src="${safeUrl}" class="w-full h-full object-cover" loading="lazy" />
                        <div class="absolute inset-0 bg-gradient-to-t from-brand-card via-transparent to-transparent"></div>
                    </div>
                `;
            }).join('');

            if (imgs.length > 1) {
                dotsHtml = `<div class="absolute bottom-4 left-0 right-0 flex justify-center gap-1.5 z-10">
                    ${imgs.map((_, i) => `<div class="w-1.5 h-1.5 rounded-full bg-white/50 backdrop-blur-sm ${i===0?'bg-brand-accent w-3':''}"></div>`).join('')}
                </div>`;
            }
        }
    }

    if (!imagesHtml) imagesHtml = `<div class="flex-none w-full h-full snap-center bg-gray-800 flex items-center justify-center"><span class="text-gray-500 text-xs">No photos</span></div>`;

    let amenitiesHtml = '<span class="text-xs text-gray-600 italic">No amenities listed</span>';
    if (venue.amenities) {
        let list = Array.isArray(venue.amenities) ? venue.amenities : [];
        if (typeof venue.amenities === 'string' && venue.amenities.startsWith('[')) {
             try { list = JSON.parse(venue.amenities); } catch(e) {}
        }
        if (list.length > 0) {
            amenitiesHtml = list.map(a => `<span class="text-[10px] font-bold px-2.5 py-1.5 rounded-lg bg-white/5 text-gray-300 border border-white/5 whitespace-nowrap">${a}</span>`).join(' ');
        }
    }

    // UPDATED HTML TO INCLUDE THE BUTTON
    container.innerHTML = `
        <div class="h-[500px] w-full bg-brand-card rounded-2xl border border-white/10 overflow-hidden shadow-2xl flex flex-col relative animate-fade-in group">
            <div class="relative h-[220px] w-full bg-gray-900 shrink-0">
                <div class="flex overflow-x-auto snap-x snap-mandatory scrollbar-hide h-full w-full" style="scroll-behavior: smooth;">${imagesHtml}</div>
                ${dotsHtml}
                
                <!-- [NEW] View Profile Button -->
                <a href="venue-profile.php?id=${venue.venue_id}" target="_blank" class="absolute top-4 right-4 z-20 p-2 bg-black/60 hover:bg-brand-accent hover:text-black text-white rounded-lg backdrop-blur-md border border-white/10 transition-all shadow-lg" title="View Full Venue Profile">
                    <i data-lucide="external-link" size="18"></i>
                </a>

            </div>
            
            <div class="flex-1 p-5 flex flex-col justify-between overflow-hidden">
                <div>
                    <h3 class="text-xl font-black text-white leading-tight line-clamp-1 mb-1">${venue.name}</h3>
                    <p class="text-sm text-gray-400 flex items-center gap-1.5 line-clamp-1"><i data-lucide="map-pin" size="14" class="text-brand-accent shrink-0"></i> ${venue.address}</p>
                </div>
                <div class="py-2"><div class="flex gap-2 overflow-x-auto scrollbar-hide w-full">${amenitiesHtml}</div></div>
                <div class="bg-white/5 rounded-xl p-3 flex justify-between items-center border border-white/5">
                    <div class="flex flex-col"><span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Rate</span><span class="text-brand-accent font-black text-xl">GHS ${Number(venue.cost_per_hour).toFixed(2)}</span></div>
                    <span class="text-xs text-gray-500 bg-black/20 px-2 py-1 rounded">per hour</span>
                </div>
            </div>

            <div class="p-4 bg-black/20 border-t border-white/5 flex gap-3 items-center shrink-0">
                <button type="button" id="prevVenue" class="p-3 rounded-xl bg-white/5 hover:bg-white/10 text-white transition-colors border border-white/5"><i data-lucide="chevron-left" size="20"></i></button>
                <button type="button" id="pickVenueBtn" class="${btnClass}"><i data-lucide="${btnIcon}" size="18"></i> ${btnText}</button>
                <button type="button" id="nextVenue" class="p-3 rounded-xl bg-white/5 hover:bg-white/10 text-white transition-colors border border-white/5"><i data-lucide="chevron-right" size="20"></i></button>
            </div>
            
            <div class="absolute top-4 left-4 bg-black/60 backdrop-blur-md px-2 py-1 rounded-md border border-white/10 text-[10px] font-mono text-gray-300">${STATE.currentVenueIndex + 1} / ${STATE.allVenues.length}</div>
        </div>
    `;
    
    if (window.lucide) lucide.createIcons();
    
    document.getElementById('prevVenue').onclick = () => { 
        STATE.currentVenueIndex = (STATE.currentVenueIndex - 1 + STATE.allVenues.length) % STATE.allVenues.length; 
        renderVenueCard(); 
    };
    document.getElementById('nextVenue').onclick = () => { 
        STATE.currentVenueIndex = (STATE.currentVenueIndex + 1) % STATE.allVenues.length; 
        renderVenueCard(); 
    };
    document.getElementById('pickVenueBtn').onclick = (e) => { 
        e.preventDefault();
        toggleVenueSelection(venue);
    };
}

function updateMapMarker(venue) {
    if (!STATE.map) return;
    
    if (isNaN(venue.latitude) || isNaN(venue.longitude)) {
        console.warn("Invalid coordinates for venue:", venue.name);
        return;
    }

    const pos = { lat: venue.latitude, lng: venue.longitude };
    
    if (STATE.marker) STATE.marker.map = null;
    
    try {
        if (google.maps.marker && google.maps.marker.AdvancedMarkerElement) {
            STATE.marker = new google.maps.marker.AdvancedMarkerElement({ map: STATE.map, position: pos, title: venue.name });
        } else {
            STATE.marker = new google.maps.Marker({ map: STATE.map, position: pos });
        }
        STATE.map.panTo(pos); 
    } catch(e) {
        console.error("Error updating marker:", e);
    }
}

function showVenueError(msg) { 
    const container = document.getElementById(DOM.venueContainer);
    if(container) container.innerHTML = `<div class="h-[500px] flex items-center justify-center p-6 text-center text-gray-500 bg-brand-card rounded-2xl border border-white/10 text-sm">${msg}<br><button onclick="fetchVenues()" class="text-brand-accent hover:underline mt-2">Retry</button></div>`;
}


// =========================================
// 4. SELECTION & CALCULATOR LOGIC
// =========================================

function toggleVenueSelection(venue) {
    if (String(STATE.selectedVenueId) === String(venue.venue_id)) {
        // Deselect
        STATE.selectedVenueId = null;
        setValue(DOM.inputs.venueId, '');
        setValue(DOM.inputs.venueName, '');
        setValue(DOM.inputs.venueCost, '');
        document.getElementById(DOM.display.badge).classList.add('hidden');
    } else {
        // Select
        STATE.selectedVenueId = venue.venue_id;
        setValue(DOM.inputs.venueId, venue.venue_id);
        setValue(DOM.inputs.venueName, venue.name);
        setValue(DOM.inputs.venueCost, venue.cost_per_hour);
        
        const displayBox = document.getElementById(DOM.display.badge);
        displayBox.classList.remove('hidden');
        displayBox.innerHTML = `
            <div class="flex items-center justify-between">
                <span class="font-bold text-white">${venue.name}</span>
                <span class="text-brand-accent">GHS ${parseFloat(venue.cost_per_hour).toFixed(2)}/hr</span>
            </div>`;
    }

    renderVenueCard(); 
    calculateFinancials(); 
    checkAvailability(); 
}

function calculateFinancials() {
    const costPerPlayer = parseFloat(document.getElementById(DOM.inputs.costPerPlayer)?.value) || 0;
    const minPlayers = parseInt(document.getElementById(DOM.inputs.minPlayers)?.value) || 10;
    const venueRate = parseFloat(document.getElementById(DOM.inputs.venueCost)?.value) || 0;
    const duration = parseInt(document.getElementById(DOM.inputs.duration)?.value) || 1;

    const totalVenueCost = venueRate * duration; 
    const platformFee = totalVenueCost * 0.10; 
    const totalNeeded = totalVenueCost + platformFee;
    const commitmentFee = totalVenueCost * 0.20; 
    
    const totalRevenue = costPerPlayer * minPlayers;
    const commission = totalRevenue - totalNeeded;

    setText(DOM.display.durationLabel, `${duration}h`);
    setText(DOM.display.venueCost, `GHS ${totalVenueCost.toFixed(2)}`);
    setText(DOM.display.platformFee, `GHS ${platformFee.toFixed(2)}`);
    setText(DOM.display.totalNeeded, `GHS ${totalNeeded.toFixed(2)}`);
    
    setText(DOM.display.commitmentFee, `GHS ${commitmentFee.toFixed(2)}`);
    setValue(DOM.inputs.commitmentFee, commitmentFee.toFixed(2));

    const submitBtn = document.getElementById(DOM.btn.submit);
    if(submitBtn) {
        if(venueRate > 0) {
            submitBtn.disabled = false;
            submitBtn.className = "w-full mt-6 py-4 bg-brand-accent hover:bg-[#2fe080] text-black font-bold rounded-xl transition-all shadow-lg shadow-brand-accent/20 flex items-center justify-center gap-2";
            submitBtn.innerHTML = `Pay GHS ${commitmentFee.toFixed(2)} & Publish`;
        } else {
            submitBtn.disabled = true;
            submitBtn.className = "w-full mt-6 py-4 bg-gray-600 text-gray-300 font-bold rounded-xl cursor-not-allowed transition-all flex items-center justify-center gap-2";
            submitBtn.innerHTML = `Select a Venue First`;
        }
    }
    
    const commDisplay = document.getElementById(DOM.display.profit);
    if(commDisplay && venueRate > 0) {
        if (commission >= 0) commDisplay.innerHTML = `Success! Profit: <span class="text-brand-accent font-bold">GHS ${commission.toFixed(2)}</span>`;
        else commDisplay.innerHTML = `Warning: <span class="text-red-400 font-bold">GHS ${Math.abs(commission).toFixed(2)} loss</span>`;
    }
}


// =========================================
// 5. AVAILABILITY & LISTENERS
// =========================================

async function checkAvailability() {
    const venueId = document.getElementById(DOM.inputs.venueId)?.value;
    const date = document.getElementById(DOM.inputs.date)?.value;
    const timeSelect = document.getElementById(DOM.inputs.time);
    const loading = document.getElementById(DOM.display.loading);

    if (!timeSelect) return;

    if (!venueId || !date) {
        timeSelect.innerHTML = '<option value="">Select Venue & Date</option>';
        timeSelect.disabled = true;
        return;
    }

    timeSelect.disabled = true;
    if(loading) loading.classList.remove('hidden');
    timeSelect.innerHTML = '<option>Loading slots...</option>';

    try {
        const response = await fetch(`../actions/check_availability_action.php?venue_id=${venueId}&date=${date}`);
        const result = await response.json();

        if (result.success) {
            const slots = result.data.available;
            
            if (slots.length > 0) {
                timeSelect.innerHTML = '<option value="">Select Start Time</option>';
                slots.forEach(time => {
                    const option = document.createElement('option');
                    option.value = time;
                    option.textContent = time;
                    timeSelect.appendChild(option);
                });
                timeSelect.disabled = false;
            } else {
                timeSelect.innerHTML = '<option value="">No slots available</option>';
            }
        } else {
            timeSelect.innerHTML = '<option value="">Error checking slots</option>';
        }
    } catch (error) {
        console.error('Network error:', error);
        timeSelect.innerHTML = '<option value="">Connection failed</option>';
    } finally {
        if(loading) loading.classList.add('hidden');
    }
}

function initListeners() {
    // Date Constraint (Min Today)
    const dateInput = document.getElementById(DOM.inputs.date);
    if (dateInput) {
        dateInput.setAttribute('min', new Date().toISOString().split('T')[0]);
        dateInput.addEventListener('change', checkAvailability);
    }

    // Calculator Triggers
    [DOM.inputs.minPlayers, DOM.inputs.costPerPlayer, DOM.inputs.duration].forEach(id => {
        const el = document.getElementById(id);
        if(el) el.addEventListener('input', calculateFinancials);
    });

    // Form Submission
    const form = document.getElementById(DOM.form);
    if (form) {
        form.addEventListener('submit', handleFormSubmit);
    }
}

// =========================================
// 6. MODAL & PAYSTACK LOGIC (NEW)
// =========================================

// Open Modal
function showPaymentModal(eventId, amount, title) {
    const modal = document.getElementById(DOM.modal.container);
    const backdrop = document.getElementById(DOM.modal.backdrop);
    const content = document.getElementById(DOM.modal.content);

    // Populate Data
    STATE.pendingEventId = eventId;
    STATE.pendingAmount = parseFloat(amount);
    STATE.pendingTitle = title;

    document.getElementById(DOM.modal.title).textContent = title;
    document.getElementById(DOM.modal.amount).textContent = `GHS ${STATE.pendingAmount.toFixed(2)}`;

    // Show Animation
    modal.classList.remove('hidden');
    setTimeout(() => {
        backdrop.classList.remove('opacity-0');
        content.classList.remove('opacity-0', 'scale-95');
        content.classList.add('scale-100');
    }, 10);
}

// Close Modal (Exposed globally)
window.closePaymentModal = function() {
    const modal = document.getElementById(DOM.modal.container);
    const backdrop = document.getElementById(DOM.modal.backdrop);
    const content = document.getElementById(DOM.modal.content);

    backdrop.classList.add('opacity-0');
    content.classList.add('opacity-0', 'scale-95');
    content.classList.remove('scale-100');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        // Reset Button State if needed
        const submitBtn = document.getElementById(DOM.btn.submit);
        submitBtn.disabled = false;
        submitBtn.innerHTML = submitBtn.getAttribute('data-original-text') || "Submit";
    }, 300);
}

// Trigger Paystack
function payWithPaystack() {
    const email = document.getElementById(DOM.inputs.email)?.value;
    if(!email) { alert("User email missing. Please login again."); return; }

    const payBtn = document.getElementById(DOM.btn.paystack);
    const originalText = payBtn.innerHTML;
    payBtn.disabled = true;
    payBtn.innerHTML = "Initializing...";

    const handler = PaystackPop.setup({
        key: 'pk_test_62bcb1bc82f3445af1255aa8a8f0f1e7446f7936', 
        email: email,
        amount: STATE.pendingAmount * 100, // Convert to Kobo
        currency: 'GHS',
        ref: 'HAAAH-' + Math.floor((Math.random() * 1000000000) + 1), 
        metadata: {
            custom_fields: [
                { display_name: "Event ID", variable_name: "event_id", value: STATE.pendingEventId },
                { display_name: "Event Title", variable_name: "event_title", value: STATE.pendingTitle }
            ]
        },
        callback: function(response) {
            window.location.href = `../actions/verify_payment.php?reference=${response.reference}&event_id=${STATE.pendingEventId}&type=organizer_fee`;
        },
        onClose: function() {
            alert('Transaction was not completed, window closed.');
            payBtn.disabled = false;
            payBtn.innerHTML = originalText;
        }
    });
    handler.openIframe();
}


// =========================================
// 7. FORM SUBMISSION HANDLER
// =========================================

function handleFormSubmit(e) {
    e.preventDefault();
    const form = e.target;
    if (!form.checkValidity()) { form.reportValidity(); return; }
    if (!document.getElementById(DOM.inputs.venueId).value) { alert("⚠️ Please select a venue."); return; }

    const submitBtn = document.getElementById(DOM.btn.submit);
    submitBtn.setAttribute('data-original-text', submitBtn.innerHTML); 
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i data-lucide="loader" class="animate-spin"></i> Creating Event...';
    if(window.lucide) lucide.createIcons();

    const formData = new FormData(form);
    
    const titleVal = form.querySelector('input[name="title"]').value;
    const feeVal = document.getElementById(DOM.inputs.commitmentFee).value;

    fetch(form.action, {
        method: 'POST', body: formData, headers: { 'Accept': 'application/json' }
    })
    .then(response => response.text())
    .then(text => {
        let data;
        try { data = JSON.parse(text.trim()); } catch (e) { data = { success: false, message: 'Server error' }; }

        if (data.success && data.event_id) {
            showPaymentModal(data.event_id, feeVal, titleVal);
        } else {
            alert("Error: " + (data.message || "Failed"));
            submitBtn.disabled = false;
            submitBtn.innerHTML = submitBtn.getAttribute('data-original-text');
        }
    })
    .catch(() => { 
        submitBtn.disabled = false; 
        alert("Network error."); 
    });
}

// --- HELPERS ---
function setValue(id, val) { const el = document.getElementById(id); if(el) el.value = val; }
function setText(id, text) { const el = document.getElementById(id); if(el) el.textContent = text; }