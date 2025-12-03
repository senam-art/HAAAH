<?php
// actions/get_venue_details_action.php

// 1. Bootstrap
require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/controllers/venue_controller.php';

// 2. Set Headers
header('Content-Type: application/json');

// 3. Validation
if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Venue ID is required']);
    exit();
}

$venue_id = intval($_GET['id']);

// 4. Call Controller
$controller = new VenueController();
$venue = $controller->get_venue_details_ctr($venue_id);

// 5. Return Response
if ($venue) {
    echo json_encode(['success' => true, 'data' => $venue]);
} else {
    echo json_encode(['success' => false, 'message' => 'Venue not found']);
}
exit();
?>