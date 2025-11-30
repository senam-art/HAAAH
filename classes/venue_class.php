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
     * Get all unique cities
     */
    public function getAllCities()
    {
        $stmt = $this->db->prepare("SELECT DISTINCT city FROM venues WHERE available = 1 ORDER BY city ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        $cities = [];
        while ($row = $result->fetch_assoc()) {
            $cities[] = $row['city'];
        }
        return $cities;
    }

    /**
     * Get venues by city
     */
    public function getVenuesByCity($city)
    {
        $stmt = $this->db->prepare(
            "SELECT venue_id, name, description, price_per_hour, image_url, capacity, rating, address, phone 
             FROM venues 
             WHERE city = ? AND available = 1 
             ORDER BY rating DESC, name ASC"
        );
        $stmt->bind_param("s", $city);
        $stmt->execute();
        $result = $stmt->get_result();
        $venues = [];
        while ($row = $result->fetch_assoc()) {
            $venues[] = $row;
        }
        return $venues;
    }

    /**
     * Get venue by ID
     */
    public function getVenueById($venue_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM venues WHERE venue_id = ? AND available = 1 LIMIT 1");
        $stmt->bind_param("i", $venue_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Get all venues
     */
    public function getAllVenues()
    {
        $stmt = $this->db->prepare("SELECT * FROM venues WHERE available = 1 ORDER BY city, name");
        $stmt->execute();
        $result = $stmt->get_result();
        $venues = [];
        while ($row = $result->fetch_assoc()) {
            $venues[] = $row;
        }
        return $venues;
    }
}
