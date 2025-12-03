// ../js/sign_up.js

document.addEventListener('DOMContentLoaded', () => {
    console.log("✅ sign_up.js loaded.");

    const form = document.getElementById('signupForm');
    const msgBox = document.getElementById('formMessage');
    const btn = document.getElementById('submitBtn');
    const roleInputs = document.querySelectorAll('input[name="role"]');
    const playerSection = document.getElementById('playerAttributesSection');

    // --- 1. HANDLE UI TOGGLING (Event Listeners) ---
    function togglePlayerFields() {
        // Find the currently checked role
        const checkedRole = document.querySelector('input[name="role"]:checked');
        
        if (checkedRole && checkedRole.value === '0') {
            // Player Selected
            playerSection.classList.remove('hidden');
            playerSection.classList.add('block'); // Ensure it displays
        } else {
            // Manager Selected
            playerSection.classList.add('hidden');
            playerSection.classList.remove('block');
        }
    }

    // Attach change listeners to radio buttons
    roleInputs.forEach(input => {
        input.addEventListener('change', togglePlayerFields);
    });

    // Run once on load to set initial state
    togglePlayerFields();

    // --- 2. HANDLE FORM SUBMISSION ---
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        // Clear previous messages
        msgBox.classList.add('hidden');

        // Client Validation
        const p1 = document.getElementById('password').value;
        const p2 = document.getElementById('confirmPassword').value;

        if (p1 !== p2) {
            showMessage("Passwords do not match.", "error");
            return;
        }

        if (p1.length < 6) {
            showMessage("Password must be at least 6 characters.", "error");
            return;
        }

        // Prepare Submission
        const formData = new FormData(form);
        const originalText = btn.innerHTML;
        
        // Loading State
        btn.disabled = true;
        btn.innerHTML = `<svg class="animate-spin h-5 w-5 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Creating Account...`;

        try {
            const response = await fetch('../actions/register_user_action.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                showMessage("Success! Redirecting...", "success");
                
                // Determine Redirect Destination
                const activeRole = document.querySelector('input[name="role"]:checked').value;
                let targetPage = 'homepage.php'; // Default (Player)
                
                if (activeRole === '1') targetPage = 'manage_venues.php';
                else if (activeRole === '2') targetPage = 'admin_dashboard.php';

                setTimeout(() => {
                    window.location.href = targetPage;
                }, 1000);

            } else {
                showMessage(result.message || "Registration failed.", "error");
                resetBtn(btn, originalText);
            }
        } catch (error) {
            console.error("AJAX Error:", error);
            showMessage("Server connection error. Check console.", "error");
            resetBtn(btn, originalText);
        }
    });

    // Helper: Reset Button
    function resetBtn(button, text) {
        button.disabled = false;
        button.innerHTML = text;
    }

    // Helper: Show Message
    function showMessage(msg, type) {
        msgBox.textContent = msg;
        // Reset classes to base state
        msgBox.className = "text-center text-sm p-3 rounded-xl mb-4 font-bold border";
        
        if (type === 'error') {
            msgBox.classList.add('bg-red-500/20', 'text-red-400', 'border-red-500/30');
        } else {
            msgBox.classList.add('bg-green-500/20', 'text-green-400', 'border-green-500/30');
        }
        msgBox.classList.remove('hidden');
    }
});