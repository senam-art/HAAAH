<?php
// 1. ENABLE DEBUGGING
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


header('Content-Type: application/json');

// 3. Include Files
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/event_controller.php';

try {
    // 4. INSTANTIATE CONTROLLER (WITHOUT $db)
    // FIX: We removed ($db) because your Class connects internally now.
    $controller = new EventController(); 

    // 5. Run Create Method
    $result = $controller->create($_POST);

    if ($result['success']) {
        echo json_encode(['success' => true, 'message' => 'Event created!']);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => $result['message'] ?? 'Failed to create event.'
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Server Error: ' . $e->getMessage()
    ]);
}
exit;