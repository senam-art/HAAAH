<?php
// actions/create_event_action.php

// 1. Bootstrap & Settings
require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/controllers/event_controller.php';

// Enable JSON Output
header('Content-Type: application/json');

// 2. Session Check
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Session expired. Please log in.']);
    exit();
}

// 3. Capture Data
$user_id = $_SESSION['user_id'];
$venue_id = isset($_POST['selected_venue_id']) ? intval($_POST['selected_venue_id']) : 0;
$title = sanitize_input($_POST['title']);
$format = sanitize_input($_POST['format']);
$date = sanitize_input($_POST['event_date']);
$time = sanitize_input($_POST['event_time']);
$duration = intval($_POST['duration']);
$cost_per_player = floatval($_POST['cost_per_player']);
$min_players = intval($_POST['min_players']);

// Basic Validation
if ($venue_id === 0 || empty($title) || empty($date) || empty($time)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit();
}

// 4. Construct Data Array
// Mapping POST fields to your 'events' table schema
$eventData = [
    'organizer_id'    => $user_id,
    'title'           => $title,
    'sport'           => 'Football', // Default
    'format'          => $format,
    'event_date'      => $date,
    'event_time'      => $time,
    'venue_id'        => $venue_id,
    'cost_per_player' => $cost_per_player,
    'min_players'     => $min_players,
    'duration'        => $duration,
];

// 5. Create Event via Functional Controller
// We call the function directly instead of instantiating a controller class.
// Ensure your event_controller.php defines: function create_event_ctr($data) { ... }
$result = create_event_ctr($eventData);

if ($result['success']) {
    // 6. SUCCESS: Return Event ID to JS for the Payment Modal
    echo json_encode([
        'success'  => true, 
        'event_id' => $result['event_id'], 
        'message'  => 'Event created. Proceeding to payment...'
    ]);
} else {
    // FAILURE
    echo json_encode([
        'success' => false, 
        'message' => 'Database Error: ' . $result['message']
    ]);
}

// Helper Function
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}
?>