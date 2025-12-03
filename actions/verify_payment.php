<?php
session_start();

// 1. Load Controller
require_once __DIR__ . '/../controllers/payment_controller.php';

// 2. Validate Incoming Data
if (!isset($_GET['reference']) || !isset($_GET['event_id']) || !isset($_GET['type'])) {
    header("Location: ../view/payment_failed.php?msg=Missing payment details");
    exit();
}

$tx_ref = $_GET['reference'];
$event_id = intval($_GET['event_id']);
$payment_type = $_GET['type'];
$user_id = $_SESSION['user_id'] ?? 0;

if ($user_id === 0) {
    header("Location: ../view/login.php?error=session_expired");
    exit();
}

// 3. STEP 1: Verify with Paystack API
// We call the specific API verification function, not the monolithic one.
$api_result = verify_paystack_transaction_ctr($tx_ref);

if ($api_result['status'] === false) {
    $msg = urlencode($api_result['message']);
    header("Location: ../view/payment_failed.php?event_id=$event_id&msg=$msg");
    exit();
}

// 4. STEP 2: Process Database Logic
$tx_data = $api_result['data'];

// We call the database processing function
$db_result = process_payment_record_ctr(
    $user_id, 
    $event_id, 
    $payment_type, 
    $tx_data['amount'], 
    $tx_ref, 
    $tx_data['currency']
);

// 5. Handle Final Outcome
if ($db_result['status'] === true) {
    $pid = $db_result['payment_id'];
    
    // Redirect with status param
    if ($payment_type === 'organizer_fee') {
        header("Location: ../view/payment_success.php?payment_id=$pid&status=pending_approval");
    } else {
        header("Location: ../view/payment_success.php?payment_id=$pid");
    }
    exit();
} else {
    $msg = urlencode($db_result['message']);
    header("Location: ../view/payment_failed.php?event_id=$event_id&msg=$msg");
    exit();
}
?>