<?php
// --- DEBUGGING: ENABLE ERROR DISPLAY ---
// These lines force PHP to show errors instead of a white screen/empty response.
// Remove these 3 lines when moving to production!
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ---------------------------------------

session_start();
require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/controllers/user_controller.php';

// Note: If a PHP error occurs before this line, the header might not set, 
// but the error text will still be sent to your JS console.
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Capture Redirect URL if passed (Deep Linking)
    $redirect_to = $_POST['redirect_to'] ?? ''; 

    // 2. Instantiate Controller
    $controller = new UserController();
    
    // 3. Call Register Method
    // The Controller handles the Database Insert AND sets the $_SESSION
    $result = $controller->register_user_ctr($_POST);

    // 4. Handle Response & Routing
    if ($result['success']) {
        
        // CHECK 1: Deep Linking / Return URL
        if (!empty($redirect_to)) {
            $result['redirect'] = $redirect_to;
        } 
        // CHECK 2: Default Role-Based Routing
        else {
            $role = isset($_POST['role']) ? intval($_POST['role']) : 0;
            
            switch ($role) {
                case 1: // Venue Manager
                    $result['redirect'] = '../view/manage_venues.php';
                    break;
                    
                case 2: // Admin
                    $result['redirect'] = '../view/admin_dashboard.php'; 
                    break;
                    
                default: // Regular User (0)
                    $result['redirect'] = '../view/homepage.php';
                    break;
            }
        }
        
        echo json_encode($result);
        
    } else {
        echo json_encode(['success' => false, 'message' => $result['message']]);
    }
    exit();
}
?>