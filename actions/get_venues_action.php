<?php
require_once '../settings/core.php';
require_once PROJECT_ROOT . '/controllers/venue_controller.php';

header('Content-Type: application/json');

// Get request parameters
$action   = $_GET['action'] ?? $_POST['action'] ?? '';
$city     = $_GET['city'] ?? $_POST['city'] ?? '';
$venue_id = $_GET['venue_id'] ?? $_POST['venue_id'] ?? '';

$controller = new VenueController();
$response = ['success' => false, 'data' => [], 'message' => 'Invalid action.'];

// Handle actions
switch ($action) {
    case 'all':
        $response = $controller->get_all_venues();
        break;

    case 'venue':
        if ($venue_id) {
            $response = $controller->get_venue($venue_id);
        } else {
            $response['message'] = 'Venue ID is required';
        }
        break;

    case 'venues_by_city':
        if ($city) {
            $response = $controller->get_venues_by_city($city);
        } else {
            $response['message'] = 'City is required';
        }
        break;

    case 'cities':
        $response = $controller->get_all_cities();
        break;

    default:
        $response['message'] = 'Unknown action';
        break;
}

echo json_encode($response);
exit;
