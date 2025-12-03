<?php
// classes/event_class.php

// 1. Bootstrap Core
require_once __DIR__ . '/../settings/core.php';

// 2. Import Database Class
require_once PROJECT_ROOT . '/settings/db_class.php';

class Event extends db_connection
{
    public function __construct()
    {
        parent::db_connect();
    }

    /**
     * Create a new event
     * Columns: event_id, organizer_id, title, sport, format, event_date, 
     * event_time, venue_id, cost_per_player, min_players, created_at, 
     * current_players, status, is_approved, duration
     */
    public function createEvent($data)
    {
        // Default values for a new event
        $current_players = 0;
        $status = 'pending'; // Pending payment
        $is_approved = 0;    // Not approved until paid

        $sql = "INSERT INTO events (
                    organizer_id, title, sport, format, event_date, event_time, 
                    venue_id, cost_per_player, min_players, duration, 
                    current_players, status, is_approved, created_at
                ) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        if (!$this->db) {
            $this->db_connect();
        }

        $stmt = $this->db->prepare($sql);
        
        // Bind Parameters:
        // i (organizer_id), s (title), s (sport), s (format), s (date), s (time)
        // i (venue_id), d (cost - double), i (min_players), i (duration)
        // i (current_players), s (status), i (is_approved)
        $stmt->bind_param(
            "isssssidiiisi", 
            $data['organizer_id'], 
            $data['title'], 
            $data['sport'], 
            $data['format'], 
            $data['event_date'], 
            $data['event_time'], 
            $data['venue_id'], 
            $data['cost_per_player'], 
            $data['min_players'], 
            $data['duration'],
            $current_players,
            $status,
            $is_approved
        );

        if ($stmt->execute()) {
            // Return success array to match Controller/Action expectations
            return [
                'success' => true,
                'event_id' => $this->db->insert_id
            ];
        }

        return [
            'success' => false,
            'message' => $stmt->error
        ];
    }

    /**
     * Get event details by ID
     */
    public function getEventById($event_id)
    {
        $sql = "SELECT * FROM events WHERE event_id = ? LIMIT 1";
        
        if (!$this->db) {
            $this->db_connect();
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Update Event Status (e.g., after payment)
     */
    public function updateEventStatus($event_id, $status, $is_approved)
    {
        $sql = "UPDATE events SET status = ?, is_approved = ? WHERE event_id = ?";
        
        if (!$this->db) {
            $this->db_connect();
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sii", $status, $is_approved, $event_id);
        
        return $stmt->execute();
    }
}
?>