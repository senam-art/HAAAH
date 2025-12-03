console.log("✅ sign_up.js loaded.");

// 1. Toggle UI based on selection
function togglePlayerFields() {
    const role = document.querySelector('input[name="role"]:checked').value;
    const section = document.getElementById('playerAttributesSection');
    
    // Role 0 = Player, Role 1 = Venue Manager
    if (role === '0') { 
        section.classList.remove('hidden');
    } else { 
        section.classList.add('hidden');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    
    // Run toggle once on load to set initial state
    togglePlayerFields();

    const form = document.getElementById('signupForm');
    const msgBox = document.getElementById('formMessage');
    const btn = document.getElementById('submitBtn');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        // --- CLIENT VALIDATION ---
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

        // --- SUBMISSION ---
        const formData = new FormData(form);
        
        // Loading State
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<svg class="animate-spin h-5 w-5 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Creating Account...`;

        try {
            const response = await fetch('../actions/register_action.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                showMessage("Success! Redirecting...", "success");
                
                // --- REDIRECTION LOGIC (Frontend Based) ---
                // 1. Get the value of the actively checked radio button
                const activeRole = document.querySelector('input[name="role"]:checked').value;
                
                // 2. Determine destination
                let targetPage = 'homepage.php'; // Default (Player)
                
                if (activeRole === '1') {
                    targetPage = 'manage_venues.php'; // Venue Manager
                } else if (activeRole === '2') {
                    targetPage = 'admin_dashboard.php'; // Admin (if applicable)
                }

                // 3. Redirect
                setTimeout(() => {
                    window.location.href = targetPage;
                }, 1000);

            } else {
                showMessage(result.message || "Registration failed.", "error");
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        } catch (error) {
            console.error(error);
            showMessage("Server connection error.", "error");
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

    function showMessage(msg, type) {
        msgBox.textContent = msg;
        msgBox.classList.remove('hidden', 'bg-red-500/20', 'text-red-400', 'bg-green-500/20', 'text-green-400', 'border-red-500/30', 'border-green-500/30');
        
        if (type === 'error') {
            msgBox.classList.add('bg-red-500/20', 'text-red-400', 'border', 'border-red-500/30');
        } else {
            msgBox.classList.add('bg-green-500/20', 'text-green-400', 'border', 'border-green-500/30');
        }
        msgBox.classList.remove('hidden');
    }
});