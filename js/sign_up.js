console.log("✅ sign_up.js loaded.");

function togglePlayerFields() {
    const role = document.querySelector('input[name="role"]:checked').value;
    const section = document.getElementById('playerAttributesSection');
    
    if (role === '0') { // Player
        section.classList.remove('hidden');
    } else { // Manager (1)
        section.classList.add('hidden');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    
    togglePlayerFields();

    const form = document.getElementById('signupForm');
    const msgBox = document.getElementById('formMessage');
    const btn = document.getElementById('submitBtn');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const p1 = document.getElementById('password').value;
        const p2 = document.getElementById('confirmPassword').value;

        if (p1 !== p2) {
            showMessage("Passwords do not match.", "error");
            return;
        }

        const formData = new FormData(form);
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `Creating...`;

        try {
            const response = await fetch('../actions/register_action.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                showMessage("Account created! Redirecting...", "success");
                setTimeout(() => {
                    window.location.href = result.redirect || 'login.php';
                }, 1500);
            } else {
                showMessage(result.message || "Registration failed.", "error");
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        } catch (error) {
            console.error(error);
            showMessage("Server error.", "error");
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

    function showMessage(msg, type) {
        msgBox.textContent = msg;
        msgBox.classList.remove('hidden', 'bg-red-500/20', 'text-red-400', 'bg-green-500/20', 'text-green-400');
        
        if (type === 'error') {
            msgBox.classList.add('bg-red-500/20', 'text-red-400', 'border', 'border-red-500/30');
        } else {
            msgBox.classList.add('bg-green-500/20', 'text-green-400', 'border', 'border-green-500/30');
        }
        msgBox.classList.remove('hidden');
    }
});