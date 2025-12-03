<?php
session_start();

// Define PROJECT_ROOT if it's missing to prevent crashes
if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', dirname(__DIR__));
}

require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/controllers/admin_controller.php';

// 1. Security Check (Admin Role = 2)
// Using loose comparison (!=) allows string "2" or int 2
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 2) {
    // Debug: If you are getting stuck here, uncomment the line below to see why
    // die("Access Denied. Role is: " . ($_SESSION['role'] ?? 'Not Set'));
    header("Location: ../view/login.php");
    exit();
}

// 2. Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $action = $_POST['action'] ?? '';
    $id = intval($_POST['id'] ?? 0);

    // If ID is missing, we can't do anything
    if ($id > 0) {
        switch ($action) {
            case 'approve_event':
                $result = approve_event_ctr($id);
                set_flash_message($result, "Event successfully published.", "Failed to approve event.");
                break;

            case 'reject_event':
                $result = reject_event_ctr($id);
                set_flash_message($result, "Event rejected.", "Failed to reject event.", "error");
                break;

            case 'activate_venue':
                $result = toggle_venue_status_ctr($id, 1);
                set_flash_message($result, "Venue activated.", "Failed to activate venue.");
                break;

            case 'deactivate_venue':
                $result = toggle_venue_status_ctr($id, 0);
                set_flash_message($result, "Venue deactivated.", "Failed to deactivate venue.", "warning");
                break;

            case 'restore_venue':
                $result = restore_venue_ctr($id);
                set_flash_message($result, "Venue restored.", "Failed to restore venue.");
                break;

            default:
                $_SESSION['flash'] = ['type' => 'error', 'title' => 'Error', 'message' => 'Invalid action.'];
        }
    } else {
        $_SESSION['flash'] = ['type' => 'error', 'title' => 'Error', 'message' => 'ID missing for action.'];
    }
    
    // Always return to dashboard after POST
    header("Location: ../view/admin_dashboard.php");
    exit();
}

// Helper function
function set_flash_message($success, $success_msg, $fail_msg, $success_type = 'success') {
    if ($success) {
        $_SESSION['flash'] = [
            'type' => $success_type,
            'title' => ucfirst($success_type),
            'message' => $success_msg
        ];
    } else {
        $_SESSION['flash'] = [
            'type' => 'error',
            'title' => 'Database Error',
            'message' => $fail_msg
        ];
    }
}

// Fallback redirect if accessed directly without POST
header("Location: ../view/admin_dashboard.php");
exit();
?>