<?php
require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/classes/guest_class.php';

function get_active_events_ctr() {
    $guest = new Guest();
    return $guest->getActiveEvents();
}

function get_location_events_ctr($lat, $lng) {
    $guest = new Guest();
    return $guest->getLocationBasedEvents($lat, $lng);
}

function search_events_ctr($term) {
    $guest = new Guest();
    return $guest->searchEvents($term);
}

function get_event_details_ctr($id) {
    $guest = new Guest();
    return $guest->getEvent($id);
}

function get_event_players_ctr($id) {
    $guest = new Guest();
    return $guest->getEventPlayers($id);
}
?>