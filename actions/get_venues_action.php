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
    $controller = new VenueController();
    $venues = $controller->get_all_venues_ctr();

    if ($venues) {
        // Parse JSON columns if necessary (like image_urls or amenities stored as strings)
        // This ensures the JS receives actual arrays, not stringified JSON "[...]"
        foreach ($venues as &$v) {
            if (isset($v['image_urls']) && is_string($v['image_urls'])) {
                $decoded = json_decode($v['image_urls']);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $v['image_urls'] = $decoded;
                }
            }
            if (isset($v['amenities']) && is_string($v['amenities'])) {
                $decoded = json_decode($v['amenities']);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $v['amenities'] = $decoded;
                }
            }
        }
        // Return wrapped in 'data' key to match JS expectation: res?.data?.data
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