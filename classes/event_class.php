<?php
// Adjust this path if your db_class is in a different folder
require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/settings/db_class.php';


class Event extends db_connection {

    public function createEvent($data) {
        // 1. Connect
        if (!$this->db_connect()) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }

        // 2. Prepare SQL (Normalized)
        // We removed venue_name, venue_cost, venue_address, venue_lat, venue_lng
        // We removed created_at (Database handles it)
        $sql = "INSERT INTO events (
                    title, sport, format, event_date, event_time, 
                    venue_id, cost_per_player, min_players
                ) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return ['success' => false, 'message' => 'SQL Error: ' . $this->db->error];
        }

        // 3. Bind Parameters
        // Types: s=string, i=int, d=double
        // String: title, sport, format, date, time (5)
        // Int: venue_id, min_players (2)
        // Double: cost_per_player (1)
        // Pattern: "sssssidi"
        
        $stmt->bind_param("sssssidi", 
            $data['title'],
            $data['sport'],
            $data['format'],
            $data['event_date'],
            $data['event_time'],
            $data['venue_id'],       // Just the ID reference
            $data['cost_per_player'],
            $data['min_players']
        );

        // 4. Execute
        if ($stmt->execute()) {
            return ['success' => true, 'event_id' => $this->db->insert_id];
        } else {
            return ['success' => false, 'message' => 'Execution Error: ' . $stmt->error];
        }
    }
}