<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/controllers/user_controller.php';

$response = ['success' => false, 'message' => 'Invalid request'];

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode($response);
    exit;
}

// Accept form-data or JSON
$input = $_POST;
if (empty($input)) {
    $raw = file_get_contents('php://input');
    if ($raw) {
        $json = json_decode($raw, true);
        if (is_array($json)) {
            $input = $json;
        }
    }
}

// Basic sanitization: trim all string inputs
if ($input && is_array($input)) {
    $input = array_map(function ($v) {
        return is_string($v) ? trim($v) : $v;
    }, $input);
}

// Ensure required fields exist
$required = ['username','first_name','last_name','email', 'password', 'confirm_password','location'];
foreach ($required as $field) {
    if (empty($input[$field])) {
        echo json_encode([
            'success' => false,
            'message' => ucfirst($field) . ' is required.'
        ]);
        exit;
    }
}

$controller = new UserController();
$result = $controller->register_user($input);

// Return JSON response
echo json_encode($result);
exit;
