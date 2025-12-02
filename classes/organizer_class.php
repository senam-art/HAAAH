<?php
// classes/organizer_class.php
require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/settings/db_class.php';

class Organizer extends db_connection
{
    public function __construct() { parent::db_connect(); }

    public function create_event($organizer_id, $title, $sport, $format, $venue_id, $date, $time, $cost, $min_players, $duration)
    {
        $current_players = 0;
        $status = 'pending';
        $is_approved = 0;

        // Added 'duration' to query
        $sql = "INSERT INTO events 
                (organizer_id, title, sport, format, venue_id, event_date, event_time, cost_per_player, min_players, duration, created_at, current_players, status, is_approved) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?)";

        if (!$this->db) $this->db_connect();

        $stmt = $this->db->prepare($sql);
        
        // Added 'i' for duration (integer)
        $stmt->bind_param("isssissdiiisi", 
            $organizer_id, $title, $sport, $format, $venue_id, $date, $time, 
            $cost, $min_players, $duration, // New duration param
            $current_players, $status, $is_approved
        );

        if ($stmt->execute()) return $this->db->insert_id;
        return false;
    }
}
?>