<?php
// actions/delete_venue_action.php
session_start();
require_once __DIR__ . '/../settings/core.php';

// 1. Check Login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../view/login.php");
    exit();
}

// 2. Load Controller
$controller_path = __DIR__ . '/../controllers/venue_controller.php';
if (file_exists($controller_path)) {
    require_once $controller_path;
} else {
    require_once PROJECT_ROOT . '/controllers/venue_controller.php';
}

// 3. Process Delete Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['venue_id'])) {
    
    if (!function_exists('delete_venue_ctr')) {
        die("Error: Function 'delete_venue_ctr' not found.");
    }

    $venue_id = intval($_POST['venue_id']);
    
    // Call Controller
    $result = delete_venue_ctr($venue_id);

    if ($result) {
        header("Location: ../view/manage_venues.php?msg=deleted");
    } else {
        header("Location: ../view/manage_venues.php?error=delete_failed");
    }
    exit();
} else {
    // Invalid Access
    header("Location: ../view/manage_venues.php");
    exit();
}
?>