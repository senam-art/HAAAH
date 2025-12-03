<?php
// classes/venue_class.php
require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/settings/db_class.php';

class Venue extends db_connection
{
    public function __construct() { parent::db_connect(); }

    // --- Fetch All Venues ---
    public function get_all_venues()
    {
        $sql = "SELECT * FROM venues WHERE is_active = 1 AND is_deleted = 0 ORDER BY created_at DESC"; 
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return false;
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // --- NEW: Fetch Owner's Venues ---
    public function get_venues_by_owner($owner_id)
    {
        $sql = "SELECT * FROM venues WHERE owner_id = ? AND is_deleted = 0 ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return [];
        
        $stmt->bind_param("i", $owner_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // --- Get Single Venue ---
    public function get_venue_by_id($venue_id)
    {
        $sql = "SELECT * FROM venues WHERE venue_id = ? AND is_deleted = 0";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return false;
        
        $stmt->bind_param("i", $venue_id);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_assoc();
    }

    // --- NEW: Create Venue ---
    public function add_venue($owner_id, $name, $address, $lat, $lng, $cost, $desc, $capacity, $amenities, $images, $phone, $email)
    {
       // Default: is_active = 1 (Pending/Active), is_deleted = 0
        $sql = "INSERT INTO venues (owner_id, name, address, latitude, longitude, cost_per_hour, description, capacity, amenities, image_urls, phone, email, is_active, is_deleted, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0, NOW())";
        
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param("issdddsissss", $owner_id, $name, $address, $lat, $lng, $cost, $desc, $capacity, $amenities, $images, $phone, $email);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }
       

    // --- NEW: Update Venue ---
    public function update_venue($venue_id, $owner_id, $name, $address, $lat, $lng, $cost, $desc, $capacity, $amenities, $images, $phone, $email)
    {
        $sql = "UPDATE venues SET name=?, address=?, latitude=?, longitude=?, cost_per_hour=?, description=?, capacity=?, amenities=?, image_urls=?, phone=?, email=?, updated_at=NOW() 
                WHERE venue_id=? AND owner_id=?";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return false;

        // params: s s d d d s i s s s s i i (13 params)
        $stmt->bind_param("ssdddsissssii", $name, $address, $lat, $lng, $cost, $desc, $capacity, $amenities, $images, $phone, $email, $venue_id, $owner_id);
        
        return $stmt->execute();
    }

    // --- UPDATED: Soft Delete (Using is_deleted) ---
    public function delete_venue($venue_id, $owner_id)
    {
        // Set is_deleted to 1 (True). preserve is_active for history.
        $sql = "UPDATE venues SET is_deleted = 1 WHERE venue_id = ? AND owner_id = ?";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param("ii", $venue_id, $owner_id);
        
        return $stmt->execute();
    }

    // --- NEW: Fetch Popular Venues (Top 3 Rated) ---
    public function get_popular_venues()
    {
        // Only fetch venues that are Active, Not Deleted, and have a Rating
        $sql = "SELECT venue_id, name, address, rating FROM venues 
                WHERE is_active = 1 AND is_deleted = 0 AND rating IS NOT NULL 
                ORDER BY rating DESC LIMIT 3";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return [];
        
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function get_booked_slots($venue_id, $date)
    {
        // Check if duration exists (Legacy support)
        $check = $this->db->query("SHOW COLUMNS FROM events LIKE 'duration'");
        $has_duration = ($check && $check->num_rows > 0);

        if ($has_duration) {
            $sql = "SELECT event_time, duration FROM events WHERE venue_id = ? AND event_date = ? AND status != 'cancelled'";
        } else {
            $sql = "SELECT event_time, 1 as duration FROM events WHERE venue_id = ? AND event_date = ? AND status != 'cancelled'";
        }
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return []; 
        
        $stmt->bind_param("is", $venue_id, $date);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $blocked_hours = [];
        
        while ($row = $result->fetch_assoc()) {
            $start_hour = intval(substr($row['event_time'], 0, 2)); 
            $duration = intval($row['duration']);
            for ($i = 0; $i < $duration; $i++) {
                $blocked_hours[] = sprintf("%02d:00", $start_hour + $i);
            }
        }
        return $blocked_hours;
    }

    


    // --- AVAILABILITY ---

    /**
     * Get all confirmed bookings for a venue on a specific date.
     * Returns start times and durations so the controller can calculate blocked slots.
     */
    public function check_availability($venue_id, $date) {
        // Use a Prepared Statement for security
        $sql = "SELECT event_time, duration 
                FROM events 
                WHERE venue_id = ? 
                AND event_date = ? 
                AND status != 'cancelled'
                ORDER BY event_time ASC";
        
        // Prepare
        $stmt = $this->db->prepare($sql);
        
        // Bind: 'i' for integer (venue_id), 's' for string (date)
        $stmt->bind_param("is", $venue_id, $date);
        
        // Execute & Fetch
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // --- GENERAL VENUE METHODS ---
    
    public function get_venue_details($venue_id) {
        $sql = "SELECT * FROM venues WHERE venue_id = ? AND is_deleted = 0 LIMIT 1";
        
        // Prepare
        $stmt = $this->db->prepare($sql);
        
        // Bind: 'i' for integer (venue_id)
        $stmt->bind_param("i", $venue_id);
        
        // Execute & Fetch
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
}