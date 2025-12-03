<?php
session_start();
require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/controllers/venue_controller.php';

// 1. Check Login & Authorization
if (!isset($_SESSION['user_id'])) {
    header("Location: ../view/login.php?msg=login_to_list_venue");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../view/venue_portal.php");
    exit();
}

// 2. Call Controller
// Pass $_POST (data) and $_FILES (images) directly to the controller.
// The controller handles ALL file processing and saves to ../uploads
$result = add_venue_ctr($_POST, $_FILES);

// 3. Handle Result
if ($result) {
    // Success: Redirect to dashboard with message
    header("Location: ../view/manage_venues.php?msg=venue_created");
} else {
    // Failure: Redirect back to form with error
    header("Location: ../view/create_venue.php?error=creation_failed");
}
exit();
?>