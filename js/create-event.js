/* =========================================
   create-event.js
   ========================================= */
console.log("✅ create-event.js loaded and ready.");

document.addEventListener('DOMContentLoaded', () => {
    initDateValidation();
    initTimeValidation();
    initCalculatorListeners();
    initFormSubmission();
    
    // Run initial calc just in case values are pre-filled
    if(window.updateCalculator) window.updateCalculator();
});

function initFormSubmission() {
    const form = document.getElementById('eventForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // 1. Basic HTML Validation
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // 2. Custom Venue Validation
        const venueId = document.getElementById('selected_venue_id').value;
        if (!venueId) {
            alert("⚠️ Please select a venue from the map.");
            document.getElementById('venue-map').scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        // 3. UI Feedback
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-black inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Creating...`;

        // 4. Send Data
        const formData = new FormData(this);

        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.text())
        .then(text => {
            let data;
            try {
                data = JSON.parse(text.trim());
            } catch (e) {
                console.error("JSON Parse Error:", e, text);
                data = { success: false, message: 'Server error. Check console.' };
            }

            if (data.success) {
                // REDIRECT LOGIC FOR PAYSTACK
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    // Fallback success
                    window.location.href = '../view/index.php?msg=event_created'; 
                }
            } else {
                alert("Error: " + (data.message || "Failed to create event"));
                resetButton(submitBtn, originalText);
            }
        })
        .catch(error => {
            console.error('AJAX error:', error);
            resetButton(submitBtn, originalText);
            alert("Network error. Please try again.");
        });
    });
}

function resetButton(btn, originalHtml) {
    btn.disabled = false;
    btn.innerHTML = originalHtml;
}

window.applySelectedVenue = function() {
    const v = window.selectedVenue;
    if (!v) return;

    const currentFormVenueId = document.getElementById('selected_venue_id').value;
    const pickBtn = document.getElementById('pickVenueBtn');
    
    if (currentFormVenueId == v.venue_id) {
        // Unselect Logic
        setValue('selected_venue_id', '');
        setValue('selected_venue_name', '');
        setValue('selected_venue_cost', '');
        document.getElementById('selected-venue-display').classList.add('hidden');
        if (pickBtn) {
            pickBtn.textContent = 'Select This Venue';
            pickBtn.className = 'w-full py-3 bg-brand-accent text-black font-bold rounded-lg';
        }
    } else {
        // Select Logic
        setValue('selected_venue_id', v.venue_id);
        setValue('selected_venue_name', v.name);
        setValue('selected_venue_cost', v.cost_per_hour);
        setValue('selected_venue_address', v.address);
        setValue('selected_venue_lat', v.latitude);
        setValue('selected_venue_lng', v.longitude);

        const displayBox = document.getElementById('selected-venue-display');
        if (displayBox) {
            displayBox.classList.remove('hidden');
            displayBox.innerHTML = `<div class="flex items-center justify-between gap-3 animate-fade-in"><div class="flex items-center gap-3"><div class="bg-green-500/20 p-2 rounded-full text-green-500 border border-green-500/30"><i data-lucide="check" size="20"></i></div><div><div class="text-sm font-bold text-white">${v.name}</div><div class="text-xs text-gray-400">${v.address}</div></div></div><div class="text-right"><div class="text-sm font-bold text-brand-accent">GHS ${parseFloat(v.cost_per_hour).toFixed(2)}</div><div class="text-[10px] text-gray-500">per hour</div></div></div>`;
            if (window.lucide) lucide.createIcons();
        }
    }
    // Update the calculator whenever venue changes
    window.updateCalculator();
};

window.updateCalculator = function() {
    const form = document.getElementById('eventForm');
    
    // Read fees from form attributes (Variable Logic)
    const platformFeePercent = parseFloat(form.getAttribute('data-platform-fee')) || 0.10;
    const commitmentFeePercent = parseFloat(form.getAttribute('data-commitment-fee')) || 0.20;

    const venueCostInput = document.getElementById('selected_venue_cost');
    const costPerPlayerInput = document.getElementById('cost_per_player');
    const minPlayersInput = document.getElementById('min_players');

    const costPerPlayer = parseFloat(costPerPlayerInput?.value) || 0;
    const minPlayers = parseInt(minPlayersInput?.value) || 10;
    let venueCost = 0;
    if (venueCostInput && venueCostInput.value) venueCost = parseFloat(venueCostInput.value);

    // Calculations
    const platformFee = venueCost * platformFeePercent;
    const commitmentFee = venueCost * commitmentFeePercent;
    const totalNeeded = venueCost + platformFee;
    const totalRevenue = costPerPlayer * minPlayers;
    const commission = totalRevenue - totalNeeded;

    // UI Updates
    setText('display_venue_cost', `GHS ${venueCost.toFixed(2)}`);
    setText('display_platform_fee', `GHS ${platformFee.toFixed(2)}`);
    setText('display_total_needed', `GHS ${totalNeeded.toFixed(2)}`);
    
    // New Commitment Fee Updates
    setText('display_commitment_fee', `GHS ${commitmentFee.toFixed(2)}`);
    setValue('hidden_commitment_fee', commitmentFee.toFixed(2));

    // Button Text Update
    const submitBtn = document.getElementById('submitBtn');
    if(submitBtn) {
        submitBtn.innerHTML = `Pay GHS ${commitmentFee.toFixed(2)} & Publish <i data-lucide="arrow-right" size="18" class="inline"></i>`;
        if (window.lucide) lucide.createIcons();
    }
    
    // Commission Text
    const commDisplay = document.getElementById('commission_display');
    if(commDisplay) {
        if (venueCost === 0) commDisplay.innerHTML = '<span class="text-gray-500">Select a venue to see breakdown</span>';
        else if (commission >= 0) commDisplay.innerHTML = `Success! If game happens: You get <span class="text-white font-bold">GHS ${commitmentFee.toFixed(2)}</span> back + <span class="text-brand-accent font-bold">GHS ${commission.toFixed(2)}</span> profit.`;
        else commDisplay.innerHTML = `Warning: With ${minPlayers} players, you're <span class="text-red-400 font-bold">GHS ${Math.abs(commission).toFixed(2)} short</span> of covering costs.`;
    }
};

function initDateValidation() {
    const dateInput = document.getElementById('event_date');
    if (!dateInput) return;
    const today = new Date().toISOString().split('T')[0];
    dateInput.setAttribute('min', today);
}
function initTimeValidation() {} // Placeholder
function initCalculatorListeners() {
    const min = document.getElementById('min_players');
    const cost = document.getElementById('cost_per_player');
    if(min) min.addEventListener('input', function(){ document.getElementById('min_players_display').textContent = this.value; window.updateCalculator(); });
    if(cost) cost.addEventListener('input', window.updateCalculator);
}
function setValue(id, val) { const el = document.getElementById(id); if(el) el.value = val; }
function setText(id, text) { const el = document.getElementById(id); if(el) el.textContent = text; }