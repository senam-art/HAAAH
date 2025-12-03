<?php
// 1. Bootstrap Core (Standardizes paths & DB connection)
require_once __DIR__ . '/../settings/core.php'; 
require_once __DIR__ . '/guest_controller.php';
require_once __DIR__ . '/../classes/payment_class.php';

// Safe Definition: Prevents "Constant already defined" warning
if (!defined('PAYSTACK_SECRET_KEY')) {
    // ⚠️ REPLACE WITH YOUR ACTUAL KEY IF NOT IN CORE.PHP
    define('PAYSTACK_SECRET_KEY', 'sk_test_319a4bce5fb94ebdf86fdb8a81a216683008e1d7');
}

/**
 * Calculates payment details based on the transaction type
 * --- RESTORED FUNCTION TO FIX CHECKOUT.PHP ERROR ---
 */
function get_checkout_details_ctr($event_id, $type) {
    // Ensure this function exists in guest_controller.php
    if (!function_exists('get_event_details_ctr')) {
        return false;
    }

    $event = get_event_details_ctr($event_id);
    if (!$event) return false;

    $data = [
        'event' => $event,
        'tx_ref' => "HAAAH-" . uniqid() . "-" . time(),
        'page_title' => '', 'item_name' => '', 'item_desc' => '', 'amount_to_pay' => 0.00, 'button_text' => ''
    ];

    // LOGIC: Organizer Fee Calculation
    if ($type === 'organizer_fee') {
        $venue_cost = floatval($event['venue_cost'] ?? 0);
        $commitment_fee = $venue_cost * 0.20; 
        $data['page_title'] = "Publish Event";
        $data['item_name'] = "Organizer Commitment Fee";
        $data['item_desc'] = "Refundable deposit + commission after game completion.";
        $data['amount_to_pay'] = $commitment_fee;
        $data['button_text'] = "Pay GHS " . number_format($commitment_fee, 2) . " & Publish";
    } 
    // LOGIC: Player Join Calculation
    else {
        $cost_per_player = floatval($event['cost_per_player'] ?? 0);
        $service_fee = $cost_per_player * 0.10; 
        $total = $cost_per_player + $service_fee;
        $data['page_title'] = "Join Squad";
        $data['item_name'] = "Player Spot";
        $data['item_desc'] = "Reserves 1 slot for " . ($event['title'] ?? 'Game');
        $data['amount_to_pay'] = $total;
        $data['button_text'] = "Pay GHS " . number_format($total, 2) . " & Join";
    }
    return $data;
}

/**
 * 1. Verify Transaction with Paystack API
 * (Unchanged to preserve Organizer Payment logic)
 */
function verify_paystack_transaction_ctr($reference) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer " . PAYSTACK_SECRET_KEY,
            "Cache-Control: no-cache"
        ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) return ['status' => false, 'message' => 'Connection Error'];

    $result = json_decode($response, true);
    
    if (!isset($result['data']) || $result['data']['status'] !== 'success') {
        return ['status' => false, 'message' => $result['message'] ?? 'Verification failed'];
    }

    return ['status' => true, 'data' => $result['data']];
}

/**
 * 2. Process the Verified Payment (Database Logic)
 * (Unchanged to preserve Organizer Payment logic)
 */
function process_payment_record_ctr($user_id, $event_id, $payment_type, $amount_kobo, $ref, $currency) {
    $paymentModel = new Payment();
    
    // Use get_connection() from your updated class
    $conn = $paymentModel->get_connection(); 
    
    $db_amount = $amount_kobo / 100;
    $booking_id = null;
    $payment_id = null;

    try {
        $conn->begin_transaction();

        // A. Player Joining Logic
        if ($payment_type !== 'organizer_fee') {
            $booking_id = $paymentModel->add_booking($user_id, $event_id);
            if (!$booking_id) throw new Exception("Failed to create booking");
            
            if (!$paymentModel->update_event_player_join($event_id)) throw new Exception("Failed to update player count");
        }

        // B. Organizer Publishing Logic (CRITICAL: Preserved)
        if ($payment_type === 'organizer_fee') {
            if (!$paymentModel->update_event_organizer_publish($event_id)) throw new Exception("Failed to update event status");
        }

        // C. Record Payment
        // IMPORTANT: Passing 8 arguments to match your updated Payment Class
        $payment_id = $paymentModel->recordPayment(
            $user_id, 
            $event_id, 
            $booking_id, 
            $db_amount, 
            $ref, 
            'success', 
            $currency, 
            $payment_type
        );

        if (!$payment_id) {
            throw new Exception("Failed to record payment");
        }

        $conn->commit();
        return ['status' => true, 'payment_id' => $payment_id];

    } catch (Exception $e) {
        if ($conn) $conn->rollback();
        return ['status' => false, 'message' => $e->getMessage()];
    }
}
?>