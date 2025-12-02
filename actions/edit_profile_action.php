<?php
// actions/edit_profile_action.php

require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/controllers/user_controller.php';

// Security Check
check_login();

// Check Method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../view/edit_profile.php");
    exit();
}

// Collect Data
$user_id = get_user_id();
$data = $_POST; // Contains first_name, last_name, user_name, location, positions[], traits[]

// Call Controller
$controller = new UserController();
$result = $controller->update_user_profile_ctr($user_id, $data);

if ($result['success']) {
    // Redirect to Profile with Success
    header("Location: ../view/profile.php?msg=profile_updated");
} else {
    // Redirect back to Edit with Error
    header("Location: ../view/edit_profile.php?error=" . urlencode($result['message']));
}
exit();
?>