<?php
session_start();
require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/controllers/user_controller.php';

// Ensure we are sending JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Capture Inputs (Your HTML uses name="email")
    $email = $_POST['email'] ?? ''; 
    $pass = $_POST['password'] ?? '';
    
    if (empty($email) || empty($pass)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all fields.']);
        exit();
    }

    // 2. Call Controller
    $controller = new UserController();
    $result = $controller->login_user_ctr($email, $pass);

    // 3. Handle Result safely
    if ($result && isset($result['success']) && $result['success'] === true) {
        
        echo json_encode([
            'success' => true,
            'role' => isset($_SESSION['role']) ? intval($_SESSION['role']) : 0,
            'message' => 'Login successful'
        ]);
        
    } else {
        // FAILSAFE: Ensure a message string exists even if controller doesn't return one
        $errorMsg = $result['message'] ?? 'Incorrect email or password.';
        
        echo json_encode([
            'success' => false, 
            'message' => $errorMsg
        ]);
    }
    exit();
}
?>