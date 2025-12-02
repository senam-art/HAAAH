<?php
require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/settings/db_class.php';

class Venue extends db_connection
{
    public function __construct() { parent::db_connect(); }

    public function get_all_venues()
    {
        $sql = "SELECT * FROM venues WHERE is_active = 1"; 
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return $this->db_fetch_all($sql);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get Booked Slots (Smart Duration Handling)
     */
    public function get_booked_slots($venue_id, $date)
    {
        // Select Start Time AND Duration
        $sql = "SELECT event_time, duration 
                FROM events 
                WHERE venue_id = ? 
                AND event_date = ? 
                AND status != 'cancelled'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("is", $venue_id, $date);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $blocked_hours = [];
        
        while ($row = $result->fetch_assoc()) {
            $start_hour = intval(substr($row['event_time'], 0, 2)); // Extract HH
            $duration = intval($row['duration']);
            
            // Loop through the duration to block subsequent hours
            for ($i = 0; $i < $duration; $i++) {
                $blocked_hour = $start_hour + $i;
                $time_str = sprintf("%02d:00", $blocked_hour);
                if (!in_array($time_str, $blocked_hours)) {
                    $blocked_hours[] = $time_str;
                }
            }
        }
        
        return $blocked_hours;
    }
}
?>