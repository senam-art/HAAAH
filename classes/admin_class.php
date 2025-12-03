<?php
require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/settings/db_class.php';

class Admin extends db_connection
{
    public function __construct() { parent::db_connect(); }

    // --- DASHBOARD STATS ---
    public function get_dashboard_stats() {
        $stats = [];
        
        // Pending Events (Not yet approved)
        $sql = "SELECT COUNT(*) as count FROM events WHERE is_approved = 0 AND status != 'cancelled'";
        $stats['pending_events'] = $this->db_fetch_one($sql)['count'] ?? 0;

        // Total Venues (Visible)
        $sql = "SELECT COUNT(*) as count FROM venues WHERE is_deleted = 0";
        $stats['total_venues'] = $this->db_fetch_one($sql)['count'] ?? 0;

        // Deleted Venues
        $sql = "SELECT COUNT(*) as count FROM venues WHERE is_deleted = 1";
        $stats['deleted_venues'] = $this->db_fetch_one($sql)['count'] ?? 0;

        // Total Regular Users (FIX: Using correct 'role' column from your schema)
        $sql = "SELECT COUNT(*) as count FROM users WHERE role = 0";
        $stats['total_users'] = $this->db_fetch_one($sql)['count'] ?? 0;

        return $stats;
    }

      // Fetch Active/Approved Events
    public function get_active_events() {
        $sql = "SELECT e.*, v.name as venue_name, u.user_name as organizer_name 
                FROM events e 
                JOIN venues v ON e.venue_id = v.venue_id 
                JOIN users u ON e.organizer_id = u.id 
                WHERE e.is_approved = 1 AND e.status != 'cancelled' 
                ORDER BY e.event_date ASC, e.event_time ASC";
        return $this->db_fetch_all($sql);
    }


    // --- EVENTS MANAGEMENT ---
    public function get_pending_events() {
        $sql = "SELECT e.*, v.name as venue_name, u.user_name as organizer_name, u.email as organizer_email 
                FROM events e 
                JOIN venues v ON e.venue_id = v.venue_id 
                JOIN users u ON e.organizer_id = u.id 
                WHERE e.is_approved = 0 AND e.status != 'cancelled' 
                ORDER BY e.created_at ASC";
        return $this->db_fetch_all($sql);
    }

    public function approve_event($event_id) {
        // Publish event: Visible ('open') and Approved (1)
        $sql = "UPDATE events SET status = 'open', is_approved = 1 WHERE event_id = '$event_id'";
        return $this->db_query($sql);
    }

    public function reject_event($event_id) {
        $sql = "UPDATE events SET status = 'cancelled' WHERE event_id = '$event_id'";
        return $this->db_query($sql);
    }

    // --- VENUE MANAGEMENT ---
    public function get_all_venues_admin() {
        // Fetch ALL non-deleted venues (Active and Inactive)
        $sql = "SELECT v.*, u.user_name as owner_name 
                FROM venues v 
                JOIN users u ON v.owner_id = u.id 
                WHERE v.is_deleted = 0 
                ORDER BY v.created_at DESC";
        return $this->db_fetch_all($sql);
    }

    public function get_deleted_venues() {
        // Fetch only Soft Deleted venues
        $sql = "SELECT v.*, u.user_name as owner_name 
                FROM venues v 
                JOIN users u ON v.owner_id = u.id 
                WHERE v.is_deleted = 1 
                ORDER BY v.updated_at DESC";
        return $this->db_fetch_all($sql);
    }

    public function toggle_venue_status($venue_id, $new_status) {
        // Controls 'is_active' (1 = Live, 0 = Pending/Hidden)
        $status = intval($new_status);
        $sql = "UPDATE venues SET is_active = '$status' WHERE venue_id = '$venue_id'";
        return $this->db_query($sql);
    }

    public function restore_venue($venue_id) {
        // Restore from deletion: is_deleted -> 0. Keep is_active -> 0 (Safety)
        $sql = "UPDATE venues SET is_deleted = 0, is_active = 0 WHERE venue_id = '$venue_id'";
        return $this->db_query($sql);
    }
}
?>