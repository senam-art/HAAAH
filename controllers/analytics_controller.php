<?php
// 1. Bootstrap Core
require_once __DIR__ . '/../settings/core.php';

// 2. Import Class
require_once PROJECT_ROOT . '/classes/analytics_class.php';

/**
 * Controller to fetch Payment History
 */
function get_payment_history_ctr($user_id) {
    $analytics = new Analytics();
    return $analytics->get_payment_history($user_id);
}

/**
 * Controller to fetch Organizer Stats
 */
function get_organizer_stats_ctr($user_id) {
    $analytics = new Analytics();
    return $analytics->get_organizer_stats($user_id);
}
?>