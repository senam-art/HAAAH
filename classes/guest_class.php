<?php
require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/settings/db_class.php'; 

class Guest extends db_connection {

    public function __construct() {
        parent::db_connect();
    }

    // --- HOMEPAGE METHODS (With 'is_approved' Filter) ---

    /**
     * Method 1: Get Active Events (Standard Fetch)
     * Filters by: Pending/Confirmed AND Approved by Admin
     */
    function getActiveEvents() {
        $sql = "SELECT e.*, v.name AS venue_name, v.address AS venue_address, v.image_urls
                FROM events e 
                JOIN venues v ON e.venue_id = v.venue_id 
                WHERE e.status IN ('pending', 'confirmed') 
                AND e.is_approved = 1 
                ORDER BY e.event_date ASC";
        
        // Use the parent class method for consistency
        return $this->db_fetch_all($sql);
    }

    /**
     * Method 2: Get Location Based Events (Nearby Fetch)
     * Filters by: Pending/Confirmed AND Approved by Admin
     */
    function getLocationBasedEvents($lat, $lng) {
        $lat = floatval($lat);
        $lng = floatval($lng);
        
        $sql = "SELECT e.*, v.name AS venue_name, v.address AS venue_address, v.image_urls,
                ( 6371 * acos( cos( radians($lat) ) * cos( radians( v.latitude ) ) * cos( radians( v.longitude ) - radians($lng) ) + sin( radians($lat) ) * sin( radians( v.latitude ) ) ) ) AS distance 
                FROM events e 
                JOIN venues v ON e.venue_id = v.venue_id 
                WHERE e.status IN ('pending', 'confirmed') 
                AND e.is_approved = 1
                ORDER BY distance ASC";

        return $this->db_fetch_all($sql);
    }

    /**
     * Method 3: Search Events (Text Search)
     * Filters by: Pending/Confirmed AND Approved by Admin
     */
    function searchEvents($term) {
        // Use connection for escaping if available, else basic sanitization
        if($this->db_connect()) {
             $safe_term = mysqli_real_escape_string($this->db, $term);
        } else {
             $safe_term = htmlspecialchars($term, ENT_QUOTES);
        }
        
        $sql = "SELECT e.*, v.name AS venue_name, v.address AS venue_address, v.image_urls
                FROM events e 
                JOIN venues v ON e.venue_id = v.venue_id 
                WHERE e.status IN ('pending', 'confirmed') 
                AND e.is_approved = 1
                AND (e.title LIKE '%$safe_term%' 
                     OR v.name LIKE '%$safe_term%' 
                     OR v.address LIKE '%$safe_term%')
                ORDER BY e.event_date ASC";

        return $this->db_fetch_all($sql);
    }

    // --- EVENT PROFILE METHODS ---

    /**
     * Method 4: Get a Single Event by ID
     * Used for the Match Lobby (event-profile.php)
     */
    function getEvent($id) {
        $id = intval($id);
        $sql = "SELECT e.*, v.name AS venue_name, v.address AS venue_address, v.image_urls,
                       u.user_name AS organizer_username
                FROM events e 
                JOIN venues v ON e.venue_id = v.venue_id 
                JOIN users u ON e.organizer_id = u.id
                WHERE e.event_id = $id";
        
        return $this->db_fetch_one($sql);
    }

    /**
     * Method 5: Get all players who booked this event
     * Used for the Squad List in Match Lobby
     */
    function getEventPlayers($id) {
        $id = intval($id);
        $sql = "SELECT u.id, u.user_name
                FROM bookings b
                JOIN users u ON b.user_id = u.id
                WHERE b.event_id = $id";
        
        return $this->db_fetch_all($sql);
    }
}
?>