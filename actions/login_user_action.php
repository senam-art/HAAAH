<?php

require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/controllers/user_controller.php';

// Force JSON response
header('Content-Type: application/json');

// 1. Capture Data
$username = $_POST['username'] ?? $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Missing inputs']);
    exit();
}

// 2. Call Controller
$controller = new UserController();
$result = $controller->login_user_ctr($username, $password);

// 3. Return Result
echo json_encode($result);
exit();
?>