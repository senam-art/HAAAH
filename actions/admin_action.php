<?php
session_start();
require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/controllers/admin_controller.php';

// 1. Security Check (Admin Role = 2)
// We assume your login script maps DB 'role' to Session 'user_role'
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] != 2) {
    header("Location: ../view/login.php");
    exit();
}

// 2. Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $action = $_POST['action'] ?? '';
    $id = intval($_POST['id'] ?? 0);

    if ($id > 0) {
        switch ($action) {
            case 'approve_event':
                approve_event_ctr($id);
                $msg = 'event_approved';
                break;

            case 'reject_event':
                reject_event_ctr($id);
                $msg = 'event_rejected';
                break;

            case 'activate_venue':
                toggle_venue_status_ctr($id, 1);
                $msg = 'venue_activated';
                break;

            case 'deactivate_venue':
                toggle_venue_status_ctr($id, 0);
                $msg = 'venue_deactivated';
                break;

            case 'restore_venue':
                restore_venue_ctr($id);
                $msg = 'venue_restored';
                break;

            default:
                $msg = 'error';
        }
        
        // Return to dashboard
        header("Location: ../view/admin_dashboard.php?msg=$msg");
        exit();
    }
}

header("Location: ../view/admin_dashboard.php");
exit();
?>