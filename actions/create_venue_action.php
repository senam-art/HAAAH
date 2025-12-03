<?php
// actions/create_venue_action.php
session_start();

// 1. Settings & Core
require_once __DIR__ . '/../settings/core.php';

// 2. Load the Functional Controller
// Use relative path for safety, fallback to PROJECT_ROOT
$controller_path = __DIR__ . '/../controllers/venue_controller.php';
if (file_exists($controller_path)) {
    require_once $controller_path;
} else {
    // If relative fails, try PROJECT_ROOT
    require_once PROJECT_ROOT . '/controllers/venue_controller.php';
}

// 3. Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../view/login.php?msg=login_required");
    exit();
}

// 4. Check Request Method
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_venue'])) {
    
    // Check if the required function exists (Debugging safety)
    if (!function_exists('add_venue_ctr')) {
        die("Error: Function 'add_venue_ctr' not found. Please ensure controllers/venue_controller.php is updated.");
    }

    // 5. Call Controller
    // Pass $_POST for text data and $_FILES for images
    $result = add_venue_ctr($_POST, $_FILES);

    if ($result) {
        // Success: Redirect to the newly created venue profile
        // $result contains the new venue_id
        header("Location: ../view/venue-profile.php?id=" . $result . "&msg=venue_created");
    } else {
        // Failure: Redirect back to the creation form with an error message
        header("Location: ../view/create_venue.php?msg=creation_failed");
    }
    exit();

} else {
    // Invalid access (Direct GET access disallowed)
    header("Location: ../view/venue-portal.php");
    exit();
}
?>