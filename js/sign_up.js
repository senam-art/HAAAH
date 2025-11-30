document.addEventListener('DOMContentLoaded', function () {
    // Initialize icons
    if (typeof lucide !== 'undefined' && lucide.createIcons) {
        lucide.createIcons();
    }

    const form = document.getElementById('signupForm');
    if (!form) return;

    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirmPassword');
    const passwordError = document.getElementById('passwordError');
    const confirmError = document.getElementById('confirmError');
    const username = document.getElementById('username');
    const usernameError = document.getElementById('usernameError');

    function clearFieldError(el) {
        if (!el) return;
        el.classList.remove('border-red-500');
    }

    function setFieldError(el) {
        if (!el) return;
        el.classList.add('border-red-500');
    }

    // Real-time checks
    if (password) {
        password.addEventListener('input', function () {
            if (passwordError) passwordError.classList.add('hidden');
            clearFieldError(password);
            // If confirm has value, re-check match
            if (confirmPassword && confirmPassword.value.length) {
                if (password.value !== confirmPassword.value) {
                    if (confirmError) confirmError.classList.remove('hidden');
                    setFieldError(confirmPassword);
                } else {
                    if (confirmError) confirmError.classList.add('hidden');
                    clearFieldError(confirmPassword);
                }
            }
        });
    }

    if (confirmPassword) {
        confirmPassword.addEventListener('input', function () {
            if (confirmError) confirmError.classList.add('hidden');
            clearFieldError(confirmPassword);
            if (password && password.value !== confirmPassword.value) {
                if (confirmError) confirmError.classList.remove('hidden');
                setFieldError(confirmPassword);
            } else {
                if (confirmError) confirmError.classList.add('hidden');
                clearFieldError(confirmPassword);
            }
        });
    }

    // Clear inline errors when the user types for any required field
    const requiredFields = form.querySelectorAll('[required]');
    requiredFields.forEach(field => {
        field.addEventListener('input', function () {
            // hide inline error if present
            const inline = document.getElementById(field.id + 'Error');
            if (inline) inline.classList.add('hidden');
            // clear visual error state
            clearFieldError(field);

            // specific quick re-validation: username length
            if (field === username) {
                if (username.value.length >= 4) {
                    if (usernameError) usernameError.classList.add('hidden');
                    clearFieldError(username);
                }
            }

            // password length live feedback
            if (field === password) {
                if (password.value.length >= 8) {
                    if (passwordError) passwordError.classList.add('hidden');
                    clearFieldError(password);
                }
            }

            // confirm password live match check
            if (field === confirmPassword || field === password) {
                if (password && confirmPassword && password.value === confirmPassword.value) {
                    if (confirmError) confirmError.classList.add('hidden');
                    clearFieldError(confirmPassword);
                }
            }
        });
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        // Clear previous errors
        if (passwordError) passwordError.classList.add('hidden');
        if (confirmError) confirmError.classList.add('hidden');
        [password, confirmPassword].forEach(clearFieldError);

        // Check built-in validity for required fields and show inline field errors
        const required = form.querySelectorAll('[required]');
        for (const el of required) {
            if (!el.checkValidity()) {
                // show inline error if available
                const inlineErr = document.getElementById(el.id + 'Error');
                if (inlineErr) {
                    inlineErr.textContent = el.validationMessage || 'This field is required.';
                    inlineErr.classList.remove('hidden');
                } else {
                    // fallback to native tooltip
                    el.reportValidity();
                }
                setFieldError(el);
                el.focus();
                return;
            }
        }

        // Username length
        if (username && username.value.length < 4) {
            if (usernameError) usernameError.textContent = 'Username must be at least 4 characters.';
            if (usernameError) usernameError.classList.remove('hidden');
            setFieldError(username);
            username.focus();
            return;
        }

        // Password length
        if (password && password.value.length < 8) {
            if (passwordError) passwordError.textContent = 'Password must be at least 8 characters.';
            if (passwordError) passwordError.classList.remove('hidden');
            setFieldError(password);
            password.focus();
            return;
        }

        // Password match
        if (password && confirmPassword && password.value !== confirmPassword.value) {
            if (confirmError) confirmError.textContent = 'Passwords do not match.';
            if (confirmError) confirmError.classList.remove('hidden');
            setFieldError(confirmPassword);
            confirmPassword.focus();
            return;
        }

        // All good — send via AJAX to actions endpoint
        const actionUrl = '../actions/register_user_action.php';

        // collect form data
        const formData = new FormData(form);

        // loading state
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        submitButton.disabled = true;
        submitButton.innerHTML = 'Creating account...';

        fetch(actionUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.text()) // read raw first
        .then(text => {
            console.log('Raw server response:', text); // ✅ super helpful for debugging

            let data;
            try {
                // Trim whitespace before parsing (removes stray ?> or newlines)
                const cleanedText = text.trim();
                data = JSON.parse(cleanedText);
            } catch (e) {
                data = {
                    success: false,
                    message: 'Server returned an invalid response.'
                };
            }

            submitButton.disabled = false;
            submitButton.innerHTML = originalText;

            let msg = document.getElementById('formMessage');
            if (!msg) {
                msg = document.createElement('div');
                msg.id = 'formMessage';
                msg.className = 'mb-4 text-sm text-center';
                form.prepend(msg);
            }

            if (data.success) {
                msg.className = 'mb-4 text-sm text-center text-green-400';
                msg.textContent = data.message || 'Registration successful!';

                setTimeout(() => {
                    window.location.href = '/HAAAH/view/homepage.php';
                }, 1200);
            } else {
                msg.className = 'mb-4 text-sm text-center text-red-400';
                msg.textContent = data.message || 'Registration failed.';
            }
        })
        .catch(error => {
            console.error('AJAX error:', error);

            submitButton.disabled = false;
            submitButton.innerHTML = originalText;

            let msg = document.getElementById('formMessage');
            if (!msg) {
                msg = document.createElement('div');
                msg.id = 'formMessage';
                msg.className = 'mb-4 text-sm text-center';
                form.prepend(msg);
            }

            msg.className = 'mb-4 text-sm text-center text-red-400';
            msg.textContent = 'Network error. Please try again.';
        });

    });
});
