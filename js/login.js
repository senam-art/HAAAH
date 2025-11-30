document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('loginForm');
    if (!form) return;

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            // Extra safety: stop propagation
            e.stopPropagation();

            const formData = new FormData(form);
            const username = formData.get('username') || formData.get('email');
            const password = formData.get('password');

            // Simple validation
            if (!username || !password) {
                showMessage('Please enter your username/email and password.', false);
                return false;
            }

            // Disable button
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

                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;

                showMessage(data.message, data.success);

                if (data.success) {
                    setTimeout(() => {
                        window.location.href = '../view/homepage.php';
                    }, 1200);
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
            msgDiv.className = 'mb-4 text-sm text-center';
            form.prepend(msgDiv);
        }
        msgDiv.className = 'mb-4 text-sm text-center ' + (success ? 'text-green-400' : 'text-red-400');
        msgDiv.textContent = msg;
    }
});