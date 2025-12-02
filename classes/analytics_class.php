<?php
// 1. Bootstrap Core
require_once __DIR__ . '/../settings/core.php';

// 2. Import Database Class
require_once PROJECT_ROOT . '/settings/db_class.php';

class Analytics extends db_connection {
    
    public function __construct() {
        parent::db_connect();
    }

    /**
     * Get Detailed Order/Payment History
     */
    public function get_payment_history($user_id) {
        // Updated to use 'paid_at' and 'currency'
        // Ordered by payment_id DESC to ensure newest attempts show top, even if unpaid
        $sql = "SELECT p.payment_id, p.amount, p.currency, p.reference, p.status, p.paid_at, 
                       e.title as event_title, e.event_date
                FROM payments p
                JOIN events e ON p.event_id = e.event_id
                WHERE p.user_id = ?
                ORDER BY p.payment_id DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get Organizer Analytics (Aggregated)
     */
    public function get_organizer_stats($user_id) {
        // 1. Total Events Created
        $sql_events = "SELECT COUNT(*) as total_events FROM events WHERE organizer_id = ?";
        $stmt = $this->db->prepare($sql_events);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $total_events = $stmt->get_result()->fetch_assoc()['total_events'];

        // 2. Total Players Hosted
        $sql_players = "SELECT SUM(current_players) as total_hosted FROM events WHERE organizer_id = ?";
        $stmt = $this->db->prepare($sql_players);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $total_hosted = $stmt->get_result()->fetch_assoc()['total_hosted'] ?? 0;

        // 3. Total Revenue Generated
        $sql_rev = "SELECT SUM(cost_per_player * current_players) as est_revenue 
                    FROM events 
                    WHERE organizer_id = ? AND is_approved = 1";
        $stmt = $this->db->prepare($sql_rev);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $est_revenue = $stmt->get_result()->fetch_assoc()['est_revenue'] ?? 0;

        return [
            'events_hosted' => $total_events,
            'players_hosted' => $total_hosted,
            'revenue_generated' => $est_revenue
        ];
    }
}
?>