<?php

require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/controllers/venue_controller.php';

header('Content-Type: application/json');

// 1. Capture Inputs
$venue_id = isset($_GET['venue_id']) ? intval($_GET['venue_id']) : 0;
$date = isset($_GET['date']) ? $_GET['date'] : '';

if (!$venue_id || empty($date)) {
    echo json_encode(['success' => false, 'message' => 'Missing venue or date']);
    exit();
}

// 2. Call Controller Logic
$controller = new VenueController();
$result = $controller->get_venue_availability_ctr($venue_id, $date);

if (isset($result['error'])) {
    echo json_encode(['success' => false, 'message' => $result['error']]);
} else {
    echo json_encode(['success' => true, 'data' => $result]);
}
exit();
?>