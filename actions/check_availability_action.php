<?php
// actions/check_availability_action.php

require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/controllers/venue_controller.php';

header('Content-Type: application/json');

$venue_id = isset($_GET['venue_id']) ? intval($_GET['venue_id']) : 0;
$date = isset($_GET['date']) ? $_GET['date'] : '';

if (!$venue_id || empty($date)) {
    echo json_encode(['success' => false, 'message' => 'Missing inputs']);
    exit();
}

try {
    $controller = new VenueController();
    $result = $controller->get_venue_availability_ctr($venue_id, $date);

    if (isset($result['error'])) {
        echo json_encode(['success' => false, 'message' => $result['error']]);
    } else {
        echo json_encode(['success' => true, 'data' => $result]);
    }
} catch (Exception $e) {
    // Catch PHP errors and return JSON instead of 500 HTML page
    echo json_encode(['success' => false, 'message' => 'Server Error: ' . $e->getMessage()]);
}
exit();
?>