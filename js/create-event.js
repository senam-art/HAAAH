/* =========================================
   create-event.js (Updated for Duration)
   ========================================= */
console.log("✅ create-event.js loaded.");

document.addEventListener('DOMContentLoaded', () => {
    initDateValidation();
    initCalculatorListeners(); // Listen for Duration change
    initFormSubmission();
    
    if(window.updateCalculator) window.updateCalculator();
});

// ... (initFormSubmission & resetButton remain unchanged) ...
function initFormSubmission() {
    const form = document.getElementById('eventForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        if (!form.checkValidity()) { form.reportValidity(); return; }

        const venueId = document.getElementById('selected_venue_id').value;
        if (!venueId) {
            alert("⚠️ Please select a venue.");
            return;
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Creating...';

        const formData = new FormData(this);

        fetch(this.action, {
            method: 'POST', body: formData, headers: { 'Accept': 'application/json' }
        })
        .then(response => response.text())
        .then(text => {
            let data;
            try { data = JSON.parse(text.trim()); } catch (e) { data = { success: false, message: 'Server error.' }; }

            if (data.success) {
                if (data.redirect) window.location.href = data.redirect;
                else window.location.href = '../view/index.php?msg=event_created'; 
            } else {
                alert("Error: " + (data.message || "Failed"));
                resetButton(submitBtn, originalText);
            }
        })
        .catch(() => { resetButton(submitBtn, originalText); alert("Network error."); });
    });
}

function resetButton(btn, txt) { btn.disabled = false; btn.innerHTML = txt; }

// --- CALCULATOR LOGIC ---

window.applySelectedVenue = function() {
    const v = window.selectedVenue;
    if (!v) return;

    const currentFormVenueId = document.getElementById('selected_venue_id').value;
    
    if (currentFormVenueId == v.venue_id) {
        // Unselect
        setValue('selected_venue_id', '');
        setValue('selected_venue_cost', '');
        document.getElementById('selected-venue-display').classList.add('hidden');
    } else {
        // Select
        setValue('selected_venue_id', v.venue_id);
        setValue('selected_venue_name', v.name);
        setValue('selected_venue_cost', v.cost_per_hour);
        
        const displayBox = document.getElementById('selected-venue-display');
        displayBox.classList.remove('hidden');
        displayBox.innerHTML = `
            <div class="flex items-center justify-between">
                <span class="font-bold text-white">${v.name}</span>
                <span class="text-brand-accent">GHS ${parseFloat(v.cost_per_hour).toFixed(2)}/hr</span>
            </div>`;
    }
    window.updateCalculator();
};

window.updateCalculator = function() {
    const form = document.getElementById('eventForm');
    
    // Inputs
    const costPerPlayer = parseFloat(document.getElementById('cost_per_player')?.value) || 0;
    const minPlayers = parseInt(document.getElementById('min_players')?.value) || 10;
    const venueRate = parseFloat(document.getElementById('selected_venue_cost')?.value) || 0;
    const duration = parseInt(document.getElementById('duration')?.value) || 1;

    // Calculations
    const totalVenueCost = venueRate * duration; // Rate * Hours
    const platformFee = totalVenueCost * 0.10;
    const totalNeeded = totalVenueCost + platformFee;
    const commitmentFee = totalVenueCost * 0.20;
    const totalRevenue = costPerPlayer * minPlayers;
    const commission = totalRevenue - totalNeeded;

    // UI Updates
    setText('calc_duration_label', `${duration}h`);
    setText('display_venue_cost', `GHS ${totalVenueCost.toFixed(2)}`);
    setText('display_platform_fee', `GHS ${platformFee.toFixed(2)}`);
    setText('display_total_needed', `GHS ${totalNeeded.toFixed(2)}`);
    
    setText('display_commitment_fee', `GHS ${commitmentFee.toFixed(2)}`);
    setValue('hidden_commitment_fee', commitmentFee.toFixed(2));

    const submitBtn = document.getElementById('submitBtn');
    if(submitBtn) {
        if(venueRate > 0) {
            submitBtn.disabled = false;
            submitBtn.className = "w-full mt-6 py-4 bg-brand-accent hover:bg-[#2fe080] text-black font-bold rounded-xl transition-all shadow-lg shadow-brand-accent/20";
            submitBtn.innerHTML = `Pay GHS ${commitmentFee.toFixed(2)} & Publish`;
        } else {
            submitBtn.disabled = true;
            submitBtn.className = "w-full mt-6 py-4 bg-gray-600 text-gray-300 font-bold rounded-xl cursor-not-allowed transition-all";
            submitBtn.innerHTML = `Select a Venue First`;
        }
    }
    
    const commDisplay = document.getElementById('commission_display');
    if(commDisplay && venueRate > 0) {
        if (commission >= 0) commDisplay.innerHTML = `Success! Profit: <span class="text-brand-accent font-bold">GHS ${commission.toFixed(2)}</span>`;
        else commDisplay.innerHTML = `Warning: <span class="text-red-400 font-bold">GHS ${Math.abs(commission).toFixed(2)} loss</span>`;
    }
};

function initDateValidation() {
    const dateInput = document.getElementById('event_date');
    if (dateInput) {
        dateInput.setAttribute('min', new Date().toISOString().split('T')[0]);
    }
}

function initCalculatorListeners() {
    ['min_players', 'cost_per_player', 'duration'].forEach(id => {
        const el = document.getElementById(id);
        if(el) el.addEventListener('input', window.updateCalculator);
    });
}

function setValue(id, val) { const el = document.getElementById(id); if(el) el.value = val; }
function setText(id, text) { const el = document.getElementById(id); if(el) el.textContent = text; }