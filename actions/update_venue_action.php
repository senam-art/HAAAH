<?php

// 1. Force Error Display
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../settings/core.php';

// 1. Check Login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../view/login.php");
    exit();
}

// 2. Load Controller (Safety Check)
$controller_path = __DIR__ . '/../controllers/venue_controller.php';
if (file_exists($controller_path)) {
    require_once $controller_path;
} else {
    require_once PROJECT_ROOT . '/controllers/venue_controller.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_venue'])) {
    
    // 3. Verify Function Exists
    if (!function_exists('update_venue_ctr')) {
        die("Error: Function 'update_venue_ctr' not found. Please ensure controllers/venue_controller.php is updated.");
    }

    // 4. Process Update
    $result = update_venue_ctr($_POST, $_FILES);

    if ($result) {
        header("Location: ../view/manage_venues.php?msg=updated_successfully");
    } else {
        header("Location: ../view/edit_venue.php?id=" . $_POST['venue_id'] . "&error=update_failed");
    }
    exit();
} else {
    header("Location: ../view/manage_venues.php");
    exit();
}
?>