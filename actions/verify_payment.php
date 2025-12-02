<?php
session_start();
require_once __DIR__ . '/../settings/db_class.php'; 
require_once __DIR__ . '/../controllers/payment_controller.php'; 

// --- CRITICAL CONFIGURATION ---
// IMPORTANT: Replace this with your actual Paystack SECRET Key
define('PAYSTACK_SECRET_KEY', 'sk_test_319a4bce5fb94ebdf86fdb8a81a216683008e1d7'); 
// ------------------------------

if (!isset($_GET['reference']) || !isset($_GET['event_id']) || !isset($_GET['type'])) {
    header("Location: ../view/payment_failed.php?msg=Missing payment details");
    exit();
}

$tx_ref = $_GET['reference'];
$event_id = intval($_GET['event_id']);
$payment_type = $_GET['type'];
$user_id = $_SESSION['user_id'] ?? 0;

if ($user_id === 0) {
    // Fallback if session lost during payment
    header("Location: ../view/login.php?error=session_expired");
    exit();
}

// 1. Fetch Transaction Details from Paystack API
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($tx_ref),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer " . PAYSTACK_SECRET_KEY,
        "Cache-Control: no-cache"
    ),
));
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    header("Location: ../view/payment_failed.php?event_id=$event_id&msg=Paystack Connection Error");
    exit();
}

$result = json_decode($response, true);

// 2. Validate Payment
if (!isset($result['data']) || $result['data']['status'] !== 'success') {
    $gateway_msg = isset($result['message']) ? $result['message'] : 'Transaction not successful';
    header("Location: ../view/payment_failed.php?event_id=$event_id&msg=" . urlencode($gateway_msg));
    exit();
}

$tx_amount_kobo = $result['data']['amount'];
$tx_currency = $result['data']['currency'];

// 3. Process Success & Record in Database
$db_amount = $tx_amount_kobo / 100; // Convert back to GHS
$booking_id = null; 

try {
    // Start Transaction
    $conn = new mysqli(SERVER, USERNAME, PASSWD, DATABASE);
    if ($conn->connect_error) {
        throw new Exception("Database Connection Failed: " . $conn->connect_error);
    }
    $conn->begin_transaction();

    // A. Record Player Booking (if applicable)
    if ($payment_type !== 'organizer_fee') {
        $booking_sql = "INSERT INTO bookings (user_id, event_id, booked_at) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($booking_sql);
        $stmt->bind_param("ii", $user_id, $event_id);
        $stmt->execute();
        $booking_id = $conn->insert_id;
    }

    // B. Record the Payment
    // Note: booking_id can be null for organizer fees
    $payment_sql = "INSERT INTO payments (user_id, event_id, booking_id, amount, reference, status, currency) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($payment_sql);
    $tx_status = 'success';
    
    // FIX: Changed types from "iidssis" to "iiidsss"
    $stmt->bind_param("iiidsss", $user_id, $event_id, $booking_id, $db_amount, $tx_ref, $tx_status, $tx_currency);
    
    if (!$stmt->execute()) {
        throw new Exception("Payment Insert Failed: " . $stmt->error);
    }
    $payment_id = $conn->insert_id; // Capture the ID for the invoice

    // C. Update Event State
    $msg = "";
    if ($payment_type === 'organizer_fee') {
        // Publish the event
        $event_status_sql = "UPDATE events SET status = 'pending', is_approved = 0 WHERE event_id = ?";
        $stmt = $conn->prepare($event_status_sql);
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $msg = "published";
    } else {
        // Update Player Count
        $update_count_sql = "UPDATE events SET current_players = current_players + 1 WHERE event_id = ?";
        $stmt = $conn->prepare($update_count_sql);
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $msg = "joined";
    }

    $conn->commit();
    $conn->close();

    // 4. Redirect to Invoice Page
    header("Location: ../view/payment_success.php?payment_id=$payment_id");
    exit();

} catch (Exception $e) {
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->rollback();
        $conn->close();
    }
    error_log("Payment verification failed: " . $e->getMessage());
    // Redirect to the new failed page with a generic error or specific debug message
    header("Location: ../view/payment_failed.php?event_id=$event_id&msg=Internal System Error");
    exit();
}
?>