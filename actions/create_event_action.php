<?php
// actions/create_event_action.php

require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/controllers/organizer_controller.php';
require_once PROJECT_ROOT . '/controllers/user_controller.php'; // To get email for Paystack

// 1. Output JSON (Required for the JS fetch)
header('Content-Type: application/json');

// 2. Security Check
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to create an event.']);
    exit();
}

// 3. Capture POST Data
$data = $_POST;
$user_id = $_SESSION['user_id'];

// 4. Create Event (Pending State)
$organizerController = new OrganizerController();
$result = $organizerController->create_event_ctr($data, $user_id);

if (!$result['success']) {
    echo json_encode($result);
    exit();
}

$event_id = $result['event_id'];
$commitment_fee = floatval($data['hidden_commitment_fee'] ?? 0);

// 5. Initialize Paystack Transaction
// If fee is 0, just redirect to dashboard (e.g. for free games)
if ($commitment_fee <= 0) {
    echo json_encode(['success' => true, 'redirect' => '../view/index.php?msg=event_created']);
    exit();
}

// --- PAYSTACK LOGIC ---
$userController = new UserController();
$user = $userController->get_user_by_id_ctr($user_id);
$email = $user['email'];

$url = "https://api.paystack.co/transaction/initialize";
$fields = [
    'email' => $email,
    'amount' => $commitment_fee * 100, // Convert to pesewas/cents
    'callback_url' => "http://localhost/HAAAH/actions/verify_event_payment.php", // Make sure this path is correct
    'metadata' => [
        'custom_fields' => [
            ['display_name' => "Payment Type", 'variable_name' => "type", 'value' => "event_creation"],
            ['display_name' => "Event ID", 'variable_name' => "event_id", 'value' => $event_id],
            ['display_name' => "User ID", 'variable_name' => "user_id", 'value' => $user_id]
        ]
    ]
];

$fields_string = http_build_query($fields);

// Open Connection
$ch = curl_init();
// Use your actual Secret Key here
$secret_key = "sk_test_319a4bce5fb94ebdf86fdb8a81a216683008e1d7"; 

curl_setopt_array($ch, array(
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CustomREQUEST => "POST",
  CURLOPT_POSTFIELDS => $fields_string,
  CURLOPT_HTTPHEADER => array(
    "Authorization: Bearer " . $secret_key,
    "Cache-Control: no-cache",
  ),
));

$response = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

if ($err) {
    echo json_encode(['success' => false, 'message' => "Paystack Error: " . $err]);
} else {
    $paystack_data = json_decode($response, true);
    if ($paystack_data['status']) {
        // Return Authorization URL to Frontend
        echo json_encode(['success' => true, 'redirect' => $paystack_data['data']['authorization_url']]);
    } else {
        echo json_encode(['success' => false, 'message' => "Payment Init Failed: " . $paystack_data['message']]);
    }
}
exit();
?>