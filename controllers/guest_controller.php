<?php
/**
 * Guest Controller
 * Handles logic for profile, event listings, and general guest actions
 */

require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/classes/guest_class.php';

// --- EXISTING CONTROLLERS ---

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

function toggle_organizer_player_ctr($event_id, $user_id, $action) {
    $guest = new Guest();
    return $guest->manageOrganizerParticipation($event_id, $user_id, $action);
}

// --- NEW PROFILE CONTROLLERS (Added for Profile Page) ---

function get_user_profile_data_ctr($user_id) {
    $guest = new Guest();
    return $guest->getUserProfileData($user_id);
}

function get_organized_events_ctr($user_id) {
    $guest = new Guest();
    return $guest->getOrganizedEvents($user_id);
}

function get_booked_events_ctr($user_id) {
    $guest = new Guest();
    return $guest->getBookedEvents($user_id);
}
?>