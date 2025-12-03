<?php
require_once(__DIR__ . '/../settings/db_class.php');

class Guest extends db_connection {

    public function __construct() {
        parent::db_connect();
    }

    // --- HOMEPAGE METHODS ---
public function getActiveEvents($search = '', $lat = null, $lng = null) {
        
        // Base Query
        $sql = "SELECT e.*, v.name AS venue_name, v.address AS venue_address, v.image_urls
                FROM events e 
                JOIN venues v ON e.venue_id = v.venue_id 
                WHERE e.is_approved = 1 
                AND e.status IN ('open', 'upcoming', 'confirmed') 
                AND e.event_date >= CURDATE()";

        // Add Search Filter if provided
        if (!empty($search)) {
            $safe_search = $this->db->real_escape_string($search);
            $sql .= " AND (e.title LIKE '%$safe_search%' 
                       OR v.name LIKE '%$safe_search%' 
                       OR v.address LIKE '%$safe_search%' 
                       OR e.sport LIKE '%$safe_search%')";
        }

        // Order by Date (Soonest first)
        $sql .= " ORDER BY e.event_date ASC, e.event_time ASC";

        return $this->db_fetch_all($sql);
    }

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

    function searchEvents($term) {
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

    function getEventPlayers($id) {
        $id = intval($id);
        $sql = "SELECT u.id, u.user_name
                FROM bookings b
                JOIN users u ON b.user_id = u.id
                WHERE b.event_id = $id";
        return $this->db_fetch_all($sql);
    }

    // --- ORGANIZER MANAGEMENT METHODS ---

    function manageOrganizerParticipation($event_id, $user_id, $action) {
        if (!$this->db_connect()) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }
        $sql_check = "SELECT organizer_id FROM events WHERE event_id = '$event_id'";
        $event = $this->db_fetch_one($sql_check);

        if (!$event || $event['organizer_id'] != $user_id) {
            return ['success' => false, 'message' => 'Unauthorized action'];
        }

        mysqli_begin_transaction($this->db);
        try {
            if ($action === 'join') {
                $sql_exist = "SELECT id FROM bookings WHERE user_id = '$user_id' AND event_id = '$event_id'";
                $result = mysqli_query($this->db, $sql_exist);
                $existing = mysqli_fetch_assoc($result);

                if (!$existing) {
                    $sql_book = "INSERT INTO bookings (user_id, event_id, booked_at) VALUES ('$user_id', '$event_id', NOW())";
                    if (!mysqli_query($this->db, $sql_book)) throw new Exception("Booking failed: " . mysqli_error($this->db));
                    
                    $sql_update = "UPDATE events SET current_players = current_players + 1 WHERE event_id = '$event_id'";
                    if (!mysqli_query($this->db, $sql_update)) throw new Exception("Count update failed: " . mysqli_error($this->db));
                }
                $msg = "host_joined";

            } elseif ($action === 'leave') {
                $sql_delete = "DELETE FROM bookings WHERE user_id = '$user_id' AND event_id = '$event_id'";
                mysqli_query($this->db, $sql_delete);
                if (mysqli_affected_rows($this->db) > 0) {
                    $sql_update = "UPDATE events SET current_players = GREATEST(0, current_players - 1) WHERE event_id = '$event_id'";
                    if (!mysqli_query($this->db, $sql_update)) throw new Exception("Count update failed: " . mysqli_error($this->db));
                }
                $msg = "host_left";
            } else {
                throw new Exception("Invalid action");
            }
            mysqli_commit($this->db);
            return ['success' => true, 'msg' => $msg];
        } catch (Exception $e) {
            mysqli_rollback($this->db);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // --- NEW METHODS FOR PROFILE PAGE ---

    /**
     * Get basic profile info
     */
    function getUserProfileData($user_id) {
        $safe_id = mysqli_real_escape_string($this->db_conn(), $user_id);
        // Note: Using 'id' as PK based on joins in getEvent
        $sql = "SELECT id as user_id, first_name, last_name, user_name, email, phone_number, created_at 
                FROM users 
                WHERE id = '$safe_id'";
        return $this->db_fetch_one($sql);
    }

    /**
     * Get events I organized
     */
    function getOrganizedEvents($user_id) {
        $safe_id = mysqli_real_escape_string($this->db_conn(), $user_id);
        $sql = "SELECT e.*, v.name AS venue_name 
                FROM events e 
                JOIN venues v ON e.venue_id = v.venue_id
                WHERE e.organizer_id = '$safe_id' 
                ORDER BY e.created_at DESC";
        return $this->db_fetch_all($sql);
    }

    /**
     * Get events I booked (joined)
     */
    function getBookedEvents($user_id) {
        $safe_id = mysqli_real_escape_string($this->db_conn(), $user_id);
        // Using bookings table as the source of truth for participation
        $sql = "SELECT e.*, b.booked_at, 'confirmed' as status
                FROM events e 
                JOIN bookings b ON e.event_id = b.event_id 
                WHERE b.user_id = '$safe_id' 
                ORDER BY b.booked_at DESC";
        return $this->db_fetch_all($sql);
    }
}
?>