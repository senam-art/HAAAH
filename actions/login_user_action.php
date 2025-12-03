<?php
session_start();
require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/controllers/user_controller.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Capture Inputs
    // Check both 'username' (from formData) and 'email_or_username' just in case
    $input = $_POST['username'] ?? $_POST['email'] ?? $_POST['email_or_username'] ?? ''; 
    $pass = $_POST['password'] ?? '';
    
    if (empty($input) || empty($pass)) {
        echo json_encode(['success' => false, 'message' => 'Please enter all fields.']);
        exit();
    }

    // 2. Call Controller
    $controller = new UserController();
    $result = $controller->login_user_ctr($input, $pass);

    // 3. Handle Result
    if ($result['success']) {
        
        // Return success AND the role so JS can handle the redirect
        echo json_encode([
            'success' => true,
            'role' => intval($_SESSION['role']), // Controller sets this in session
            'message' => 'Login successful'
        ]);
        
    } else {
        echo json_encode([
            'success' => false, 
            'message' => $result['message']
        ]);
    }
    exit();
}
?>