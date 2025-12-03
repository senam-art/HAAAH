<?php
require_once __DIR__ . '/../classes/admin_class.php';

function get_admin_stats_ctr() {
    $admin = new Admin();
    return $admin->get_dashboard_stats();
}

// --- Events ---
function get_pending_events_ctr() {
    $admin = new Admin();
    return $admin->get_pending_events();
}

function approve_event_ctr($event_id) {
    $admin = new Admin();
    return $admin->approve_event($event_id);
}

function reject_event_ctr($event_id) {
    $admin = new Admin();
    return $admin->reject_event($event_id);
}

// --- Venues ---
function get_all_venues_admin_ctr() {
    $admin = new Admin();
    $venues = $admin->get_all_venues_admin();
    // Helper to get cover image for UI
    if ($venues) {
        foreach ($venues as &$v) {
            $v['image_urls'] = isset($v['image_urls']) ? json_decode($v['image_urls'], true) : [];
            $v['cover_image'] = !empty($v['image_urls']) ? $v['image_urls'][0] : 'https://images.unsplash.com/photo-1522770179533-24471fcdba45?auto=format&fit=crop&q=80';
        }
    }
    return $venues;
}

function get_deleted_venues_ctr() {
    $admin = new Admin();
    return $admin->get_deleted_venues();
}

function toggle_venue_status_ctr($venue_id, $status) {
    $admin = new Admin();
    return $admin->toggle_venue_status($venue_id, $status);
}

function restore_venue_ctr($venue_id) {
    $admin = new Admin();
    return $admin->restore_venue($venue_id);
}
?>