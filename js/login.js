document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('loginForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(form);
        const email = formData.get('email'); // Your HTML uses name="email"
        const password = formData.get('password');

        // Client-side validation
        if (!email || !password) {
            showMessage('Please enter your email and password.', false);
            return;
        }

        // Button Loading State
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Logging in...';

        fetch('../actions/login_user_action.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json()) // Directly parse JSON
        .then(data => {
            if (data.success) {
                showMessage("Login successful! Redirecting...", true);
                
                const role = parseInt(data.role); 
                let targetUrl = '../view/homepage.php'; 

                // Redirect Logic
                if (role === 1) {
                    targetUrl = '../view/manage_venues.php'; 
                } else if (role === 2) {
                    targetUrl = '../view/admin_dashboard.php';
                }

                setTimeout(() => {
                    window.location.href = targetUrl;
                }, 1000);

            } else {
                // FAILSAFE: If data.message is empty, show a default error
                const errorMsg = data.message || 'Incorrect email or password.';
                showMessage(errorMsg, false);
                
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            showMessage('System error. Please try again later.', false);
        });
    });

    function showMessage(msg, success) {
        let msgDiv = document.getElementById('formMessage');
        
        // Create div if it doesn't exist
        if (!msgDiv) {
            msgDiv = document.createElement('div');
            msgDiv.id = 'formMessage';
            // Insert it before the form inputs start
            form.prepend(msgDiv); 
        }

        // Apply classes
        msgDiv.className = `mb-6 p-4 rounded-xl text-sm font-bold text-center border transition-all duration-300 ${
            success 
            ? 'bg-green-500/10 text-green-400 border-green-500/20' 
            : 'bg-red-500/10 text-red-400 border-red-500/20'
        }`;
        
        // Set text content
        msgDiv.textContent = msg;
        
        // Ensure it is visible
        msgDiv.style.display = 'block';
    }
});