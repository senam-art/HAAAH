<?php

require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/controllers/venue_controller.php';

header('Content-Type: application/json');

try {
    $controller = new VenueController();
    $venues = $controller->get_all_venues();

    echo json_encode([
        'success' => true,
        'data' => $venues,
        'message' => 'All venues retrieved.'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch venues: ' . $e->getMessage()
    ]);
}
exit;
