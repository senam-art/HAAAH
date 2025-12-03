<?php
session_start();
require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/controllers/user_controller.php';

// Force JSON response
header('Content-Type: application/json');

// 1. Capture Data
$username = $_POST['username'] ?? $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
// Capture the redirect URL if passed from the frontend (e.g., hidden input)
$redirect_to = $_POST['redirect_to'] ?? ''; 

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Please enter both username/email and password.']);
    exit();
}

// 2. Call Controller
$controller = new UserController();
$result = $controller->login_user_ctr($username, $password);

// 3. Role-Based Redirect Logic
if ($result['success']) {
    
    // CHECK 1: Deep Linking / Return URL
    // If the user was trying to go somewhere specific (e.g. checkout), send them there.
    if (!empty($redirect_to)) {
        $result['redirect'] = $redirect_to;
    } 
    // CHECK 2: Default Role-Based Routing
    else {
        // We assume the controller has successfully set $_SESSION['user_role'] upon login.
        // Default to 0 (Regular User) if not set.
        $role = isset($_SESSION['user_role']) ? intval($_SESSION['user_role']) : 0;
        
        switch ($role) {
            case 1: // Venue Manager
                $result['redirect'] = '../view/manage_venues.php';
                break;
                
            case 2: // Admin
                // We will build this page next
                $result['redirect'] = '../view/admin_dashboard.php'; 
                break;
                
            default: // Regular User (0) or Guest
                $result['redirect'] = '../view/homepage.php';
                break;
        }
    }
}

// 4. Return Result
echo json_encode($result);
exit();
?>