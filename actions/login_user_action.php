<?php
require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/controllers/user_controller.php';

header('Content-Type: application/json');

// Accept POST only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Get POST data
$data = $_POST;
if (empty($data)) {
    // Try raw JSON
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
}

// Sanitize
$data = array_map('trim', $data);

// Delegate login logic to controller (will set session)
$userController = new UserController();
$result = $userController->login_user($data);

// Log unexpected result structure for debugging
if (!is_array($result) || !array_key_exists('success', $result)) {
    error_log('[login_user_action] Unexpected login result: ' . var_export($result, true));
    echo json_encode(['success' => false, 'message' => 'Unexpected server response.']);
    exit;
}

echo json_encode($result);
exit;
