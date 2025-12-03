document.addEventListener('DOMContentLoaded', () => {
    const paymentForm = document.getElementById('paymentForm');
    const payBtn = document.getElementById('payBtn');

    if (paymentForm) {
        paymentForm.addEventListener('submit', handlePayment);
    }

    function handlePayment(e) {
        e.preventDefault();

        // 1. Get Data from Hidden Inputs
        const amount = document.getElementById('p_amount').value;
        const email = document.getElementById('p_email').value;
        const eventId = document.getElementById('p_event_id').value;
        const type = document.getElementById('p_type').value;
        const ref = document.getElementById('p_ref').value; // Use the PHP-generated ref for consistency
        const title = document.getElementById('p_title').value;

        // 2. Validate
        if (!amount || !email || !eventId) {
            alert("Missing payment details. Please refresh the page.");
            return;
        }

        // 3. UI Feedback
        const originalText = payBtn.innerHTML;
        payBtn.disabled = true;
        payBtn.innerHTML = `Processing...`;

        // 4. Initialize Paystack
        const handler = PaystackPop.setup({
            key: window.PAYSTACK_PUBLIC_KEY, // Defined in view/checkout.php
            email: email,
            amount: parseFloat(amount) * 100, // Convert to Kobo
            currency: 'GHS',
            ref: ref, // Ensure this matches what we verify later
            metadata: {
                custom_fields: [
                    { display_name: "Event ID", variable_name: "event_id", value: eventId },
                    { display_name: "Event Title", variable_name: "event_title", value: title },
                    { display_name: "Payment Type", variable_name: "payment_type", value: type }
                ]
            },
            callback: function(response) {
                // ✅ SUCCESS: Redirect to the MVC Action
                // Passing reference, event_id, and type so verify_payment.php can do its job
                const verifyUrl = `../actions/verify_payment.php?reference=${response.reference}&event_id=${eventId}&type=${type}`;
                
                console.log("Redirecting to verification:", verifyUrl);
                window.location.href = verifyUrl;
            },
            onClose: function() {
                alert('Transaction cancelled.');
                payBtn.disabled = false;
                payBtn.innerHTML = originalText;
            }
        });

        // 5. Open Iframe
        handler.openIframe();
    }
});