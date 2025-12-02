<?php
session_start();
require_once __DIR__ . '/../controllers/guest_controller.php';

// 1. Security Check: Login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../view/login.php");
    exit();
}

// 2. Validate Inputs
if (!isset($_POST['event_id']) || !isset($_POST['action'])) {
    header("Location: ../view/index.php?error=missing_data");
    exit();
}

$event_id = intval($_POST['event_id']);
$user_id = $_SESSION['user_id'];
$action = $_POST['action']; // 'join' or 'leave'

// 3. Call Controller (MVC Pattern)
// We delegate the logic to the controller, which uses the Class > DB Connection
$result = toggle_organizer_player_ctr($event_id, $user_id, $action);

// 4. Handle Result
if ($result['success']) {
    header("Location: ../view/event-profile.php?id=$event_id&msg=" . $result['msg']);
} else {
    $error_message = urlencode($result['message']);
    header("Location: ../view/event-profile.php?id=$event_id&error=$error_message");
}
exit();
?>