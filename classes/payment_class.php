<?php
// classes/payment_class.php

// 1. Bootstrap Core
require_once __DIR__ . '/../settings/core.php';

// 2. Import Database Class
require_once PROJECT_ROOT . '/settings/db_class.php';

class Payment extends db_connection
{
    public function __construct()
    {
        // Call the parent's connection method to establish $this->db
        $this->db_connect();
    }

    /**
     * Add a new booking record (for player joining)
     * @param int $user_id
     * @param int $event_id
     * @return int|false The new booking ID or false on failure.
     */
    public function add_booking($user_id, $event_id) {
        $sql = "INSERT INTO bookings (user_id, event_id, booked_at) VALUES (?, ?, NOW())";
        
        // Use proper prepared statement from $this->db
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param("ii", $user_id, $event_id);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    /**
     * Record the actual payment transaction
     * UPDATED: Includes $payment_type to support the controller logic.
     * @param int $user_id
     * @param int $event_id
     * @param int|null $booking_id
     * @param float $amount
     * @param string $ref
     * @param string $status
     * @param string $currency
     * @param string $payment_type  <-- Required by Controller
     * @return int|false Payment ID on success, false on failure.
     */
    public function recordPayment($user_id, $event_id, $booking_id, $amount, $ref, $status, $currency, $payment_type)
    {
        // 1. Enforce Manual Verification Workflow (Pending until Admin confirms)
        // if ($status === 'success') {
        //     $status = 'pending';
        // }

        // 2. Prepare SQL (Added payment_type column)
        $sql = "INSERT INTO payments (user_id, event_id, booking_id, amount, reference, status, currency, payment_type, paid_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) return false;
        
        // Bind parameters: iiidssss (8 params matching the SQL)
        $stmt->bind_param("iiidssss", $user_id, $event_id, $booking_id, $amount, $ref, $status, $currency, $payment_type);

        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    /**
     * Update event specifically for player joining (increment count)
     */
    public function update_event_player_join($event_id) {
        $sql = "UPDATE events SET current_players = current_players + 1 WHERE event_id = ?";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param("i", $event_id);
        
        return $stmt->execute();
    }

    /**
     * Update event specifically for organizer fee
     * UPDATED: Sets status to 'pending' as requested (not 'upcoming')
     */
    public function update_event_organizer_publish($event_id) {
        // As requested: Pending until admin approves
        $status = 'pending';
        $is_approved = 0;
        $refund_status = 'none';
        
        $sql = "UPDATE events SET status = ?, is_approved = ?, refund_status = ? WHERE event_id = ?";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param("sisi", $status, $is_approved, $refund_status, $event_id);
        
        return $stmt->execute();
    }

    /**
     * Returns the raw mysqli connection for external transaction control.
     * This is crucial for the controller to manage COMMIT/ROLLBACK.
     */
    public function get_connection() {
        return $this->db;
    }
}