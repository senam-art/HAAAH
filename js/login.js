document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('loginForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const formData = new FormData(form);
        const username = formData.get('username') || formData.get('email');
        const password = formData.get('password');

        if (!username || !password) {
            showMessage('Please enter your username/email and password.', false);
            return false;
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Logging in...';

        fetch('../actions/login_user_action.php', {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.text())
        .then(text => {
            let data;
            try {
                data = JSON.parse(text.trim());
            } catch {
                data = { success: false, message: 'Server returned an invalid response.' };
            }

            if (data.success) {
                showMessage("Login successful! Redirecting...", true);
                
                // --- REDIRECTION LOGIC IN JS ---
                // 1. Get role from response (0=Player, 1=Manager, 2=Admin)
                const role = parseInt(data.role); 
                let targetUrl = '../view/homepage.php'; // Default

                // 2. Logic matching your PHP redirectIfLoggedIn()
                if (role === 1) {
                    // Managers go to Dashboard (manage_venues.php is the dashboard, venue-profile needs an ID)
                    targetUrl = '../view/manage_venues.php'; 
                } else if (role === 2) {
                    // Admin
                    targetUrl = '../view/admin_dashboard.php';
                } else {
                    // Regular User
                    targetUrl = '../view/homepage.php';
                }

                // 3. Execute Redirect
                setTimeout(() => {
                    window.location.href = targetUrl;
                }, 1000);

            } else {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                showMessage(data.message, false);
            }
        })
        .catch(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            showMessage('Network error. Please try again.', false);
        });
        return false;
    });

    function showMessage(msg, success) {
        let msgDiv = document.getElementById('formMessage');
        if (!msgDiv) {
            msgDiv = document.createElement('div');
            msgDiv.id = 'formMessage';
            form.prepend(msgDiv);
        }
        // Tailwind styling for message box
        msgDiv.className = `mb-4 p-3 rounded-xl text-sm font-bold text-center border ${
            success 
            ? 'bg-green-500/10 text-green-400 border-green-500/20' 
            : 'bg-red-500/10 text-red-400 border-red-500/20'
        }`;
        msgDiv.textContent = msg;
        msgDiv.classList.remove('hidden');
    }
});