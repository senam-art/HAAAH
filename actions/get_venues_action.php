<?php
// actions/get_venues_action.php

// 1. Bootstrap
require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/controllers/venue_controller.php';

// 2. JSON Headers
header('Content-Type: application/json');

// 3. Handle Request
$action = $_GET['action'] ?? '';

if ($action === 'all') {
    // FUNCTIONAL UPDATE: Call function directly, no 'new VenueController()'
    $venues = get_all_venues_ctr();

    if ($venues) {
        echo json_encode(['success' => true, 'data' => ['data' => $venues]]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No venues found', 'data' => ['data' => []]]);
    }
    exit();
}

// 4. Default Error
echo json_encode(['success' => false, 'message' => 'Invalid action']);
exit();
?>