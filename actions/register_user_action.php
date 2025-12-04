<?php
// --- DEBUGGING: RETURN ERRORS AS JSON ---
// We turn OFF raw error display because it corrupts the JSON response (causing JS SyntaxErrors).
// Instead, we catch errors and return them as a clean JSON "message".
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

// 1. Catch Fatal Errors (e.g. missing files, syntax errors in includes)
register_shutdown_function(function() {
    $error = error_get_last();
    // If a fatal error occurred, send it as a JSON response
    if ($error && ($error['type'] === E_ERROR || $error['type'] === E_PARSE || $error['type'] === E_COMPILE_ERROR || $error['type'] === E_CORE_ERROR)) {
        // Clean any garbage output buffer so we send valid JSON
        if (ob_get_length()) ob_clean();
        echo json_encode([
            'success' => false, 
            'message' => "Server Fatal Error: {$error['message']} in {$error['file']} on line {$error['line']}"
        ]);
        exit();
    }
});

// Start Output Buffering to prevent whitespace/warnings from breaking JSON
ob_start();

try {
    session_start();
    
    // Check Paths Manually to throw clean Exceptions
    if (!file_exists(__DIR__ . '/../settings/core.php')) {
        throw new Exception("core.php not found in ../settings/");
    }
    require_once __DIR__ . '/../settings/core.php';

    // Verify Project Root and Controller Path
    if (!defined('PROJECT_ROOT')) {
        // Fallback if PROJECT_ROOT isn't defined
        define('PROJECT_ROOT', dirname(__DIR__, 2)); // Go up two levels from 'actions'
    }

    $controllerPath = PROJECT_ROOT . '/controllers/user_controller.php';
    if (!file_exists($controllerPath)) {
        throw new Exception("user_controller.php not found at: " . $controllerPath);
    }
    require_once $controllerPath;


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // 1. Capture Redirect URL if passed
        $redirect_to = $_POST['redirect_to'] ?? ''; 

        // 2. Instantiate Controller
        if (!class_exists('UserController')) {
            throw new Exception("Class 'UserController' not found.");
        }
        $controller = new UserController();
        
        // 3. Call Register Method
        $result = $controller->register_user_ctr($_POST);

        // 4. Handle Response & Routing
        if ($result['success']) {
            
            // CHECK 1: Deep Linking
            if (!empty($redirect_to)) {
                $result['redirect'] = $redirect_to;
            } 
            // CHECK 2: Default Role-Based Routing
            else {
                $role = isset($_POST['role']) ? intval($_POST['role']) : 0;
                
                switch ($role) {
                    case 1: $result['redirect'] = '../view/manage_venues.php'; break;
                    case 2: $result['redirect'] = '../view/admin_dashboard.php'; break;
                    default: $result['redirect'] = '../view/homepage.php'; break;
                }
            }
            
            ob_clean(); // Ensure buffer is clean before sending JSON
            echo json_encode($result);
            
        } else {
            ob_clean();
            echo json_encode(['success' => false, 'message' => $result['message']]);
        }
        exit();
    } else {
        throw new Exception("Invalid Request Method. Use POST.");
    }

} catch (Exception $e) {
    // 2. Catch Standard Exceptions
    if (ob_get_length()) ob_clean();
    echo json_encode([
        'success' => false,
        'message' => "Server Error: " . $e->getMessage()
    ]);
    exit();
}
?>