<?php
require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/classes/organizer_class.php';

class OrganizerController {

    public function create_event_ctr($data, $organizer_id) {
        $organizer = new Organizer();

        // Inputs
        $title = trim($data['title']);
        $sport = trim($data['sport'] ?? 'Football');
        $format = trim($data['format'] ?? '5-a-side');
        $venue_id = intval($data['selected_venue_id']);
        $event_date = $data['event_date'];
        $event_time = $data['event_time'];
        $cost_per_player = floatval($data['cost_per_player']);
        $min_players = intval($data['min_players']);
        $duration = intval($data['duration'] ?? 1); // Default to 1 hour

        if (empty($title) || empty($venue_id) || empty($event_date) || empty($event_time)) {
            return ['success' => false, 'message' => 'Missing required fields.'];
        }

        // Create
        $new_event_id = $organizer->create_event(
            $organizer_id, $title, $sport, $format, $venue_id, 
            $event_date, $event_time, $cost_per_player, $min_players, 
            $duration // Pass Duration
        );

        if ($new_event_id) {
            return ['success' => true, 'event_id' => $new_event_id];
        } else {
            return ['success' => false, 'message' => 'Database insertion failed.'];
        }
    }
}
?>