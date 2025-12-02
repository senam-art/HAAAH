document.addEventListener('DOMContentLoaded', () => {
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});

function handlePayment(e) {
    e.preventDefault();
    
    // 1. Get Data from Hidden Inputs
    const form = document.getElementById('paymentForm');
    const formData = new FormData(form);
    
    const email = formData.get('email');
    const amount = formData.get('amount');
    const ref = formData.get('tx_ref');
    const eventId = formData.get('event_id');
    const type = formData.get('payment_type');
    
    // 2. Disable Button to prevent double-clicks
    const btn = document.getElementById('payBtn');
    const originalText = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = `Processing...`;

    // 3. Initialize Paystack Popup
    const handler = PaystackPop.setup({
        key: 'pk_test_62bcb1bc82f3445af1255aa8a8f0f1e7446f7936', // REPLACE WITH YOUR PUBLIC KEY
        email: email,
        amount: amount * 100, // Paystack expects amount in kobo/pesewas
        currency: 'GHS',
        ref: ref,
        metadata: {
            custom_fields: [
                { display_name: "Event ID", variable_name: "event_id", value: eventId },
                { display_name: "Payment Type", variable_name: "payment_type", value: type }
            ]
        },
        callback: function(response) {
            // 4. Success Handler: Redirect to verification backend
            btn.innerHTML = "Verifying Transaction...";
            
            // Redirect to backend action to verify and save to DB
            // We pass the reference returned by Paystack + our original params
            const verifyUrl = `../actions/verify_payment.php?reference=${response.reference}&event_id=${eventId}&type=${type}`;
            
            window.location.href = verifyUrl;
        },
        onClose: function() {
            // 5. Close Handler
            alert('Transaction cancelled.');
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

    handler.openIframe();
}