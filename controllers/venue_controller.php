<?php
require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/classes/venue_class.php';

class VenueController {

    public function get_all_venues_ctr() {
        $venue = new Venue();
        return $venue->get_all_venues();
    }
}
?>