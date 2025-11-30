<?php

require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/settings/db_class.php';

class Venue extends db_connection
{
    public function __construct()
    {
        parent::db_connect();
    }

    /**
     * Get all active venues
     */
    public function getAllVenues()
    {
        $stmt = $this->db->prepare("SELECT * FROM venues WHERE is_active = 1 ORDER BY name ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        $venues = [];
        while ($row = $result->fetch_assoc()) {
            $venues[] = $this->formatVenueRow($row);
        }
        return $venues;
    }

    /**
     * Get single venue by ID
     */
    public function getVenueById($venue_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM venues WHERE venue_id = ? AND is_active = 1 LIMIT 1");
        $stmt->bind_param("i", $venue_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ? $this->formatVenueRow($row) : null;
    }

    /**
     * Format a venue row to match JS expectations
     */
    private function formatVenueRow($row)
    {
        return [
            'venue_id'       => $row['venue_id'],
            'name'           => $row['name'],
            'description'    => $row['description'] ?? '',
            'cost_per_hour'  => isset($row['cost_per_hour']) ? floatval($row['cost_per_hour']) : 0,
            'address'        => $row['address'] ?? '',
            'latitude'       => isset($row['latitude']) ? floatval($row['latitude']) : null,
            'longitude'      => isset($row['longitude']) ? floatval($row['longitude']) : null,
            'image_urls'     => isset($row['image_urls']) ? json_decode($row['image_urls'], true) ?? [] : [],
            'amenities'      => isset($row['amenities']) ? json_decode($row['amenities'], true) ?? [] : [],
            'rating'         => isset($row['rating']) ? floatval($row['rating']) : 0.0,
            'total_reviews'  => isset($row['total_reviews']) ? intval($row['total_reviews']) : 0,
            'phone'          => $row['phone'] ?? '',
            'email'          => $row['email'] ?? ''
        ];
    }
}
