<?php

require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/classes/venue_class.php';

class VenueController
{
    private $venueModel;

    public function __construct()
    {
        $this->venueModel = new Venue();
    }

    /**
     * Get all cities
     */
    public function get_all_cities()
    {
        $cities = $this->venueModel->getAllCities();
        return [
            'success' => true,
            'data' => $cities,
            'message' => $cities ? 'Cities retrieved successfully.' : 'No cities found.'
        ];
    }

    /**
     * Get venues by city
     */
    public function get_venues_by_city($city)
    {
        if (!$city) {
            return ['success' => false, 'data' => [], 'message' => 'City is required.'];
        }

        $venues = $this->venueModel->getVenuesByCity($city);
        return [
            'success' => true,
            'data' => $venues,
            'message' => $venues ? "Venues for $city retrieved." : "No venues found in $city."
        ];
    }

    /**
     * Get single venue by ID
     */
    public function get_venue($venue_id)
    {
        if (!$venue_id) {
            return ['success' => false, 'data' => null, 'message' => 'Venue ID is required.'];
        }

        $venue = $this->venueModel->getVenueById($venue_id);
        if (!$venue) {
            return ['success' => false, 'data' => null, 'message' => 'Venue not found.'];
        }

        return ['success' => true, 'data' => $venue, 'message' => 'Venue retrieved successfully.'];
    }

    /**
     * Get all venues
     */
    public function get_all_venues()
    {
        $venues = $this->venueModel->getAllVenues();
        return [
            'success' => true,
            'data' => $venues,
            'message' => $venues ? 'All venues retrieved.' : 'No venues found.'
        ];
    }
}
