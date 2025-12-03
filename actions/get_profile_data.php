<?php
// actions/get_profile_data.php
// 1. Bootstrap Core (Standard Pattern)
require_once __DIR__ . '/../settings/core.php';

// 2. Import Dependencies using PROJECT_ROOT
require_once PROJECT_ROOT . '/controllers/user_controller.php';
require_once PROJECT_ROOT . '/controllers/guest_controller.php';

// 3. Security Check (Using helper from core.php)
// Ensures only logged-in users can view profiles
check_login(); 

// 4. Determine Context (Who is viewing whom?)
$viewer_id = get_user_id(); // From core.php helper

// If ?id=X is set in URL, view that user. Otherwise, view self.
$profile_id = isset($_GET['id']) ? intval($_GET['id']) : $viewer_id;

$is_own_profile = ($viewer_id == $profile_id);

// 5. Fetch User Data (Using UserController)
$userController = new UserController();
$user_data = $userController->get_user_by_id_ctr($profile_id);

if (!$user_data) {
    die("User profile not found.");
}

// 6. Parse Personalization Tags (JSON)
$profile_tags = [
    'positions' => [],
    'traits' => [],
    'profile_image' => null
];

// Handle profile details JSON if present
if (!empty($user_data['profile_details'])) {
    $decoded = json_decode($user_data['profile_details'], true);
    if (is_array($decoded)) {
        $profile_tags = array_merge($profile_tags, $decoded);
    }
}
// Prioritize explicit profile_image column if available in data
if (isset($user_data['profile_image']) && !empty($user_data['profile_image'])) {
    $profile_tags['profile_image'] = $user_data['profile_image'];
}

// 7. Fetch Events (Using GuestController)
$organized_events = [];
if (function_exists('get_organized_events_ctr')) {
    $organized_events = get_organized_events_ctr($profile_id);
}

// 8. Fetch Booking History
// We fetch this for everyone to calculate the "Matches Played" count.
$all_bookings = [];
if (function_exists('get_booked_events_ctr')) {
    $all_bookings = get_booked_events_ctr($profile_id);
}

// Only expose the detailed list (financials/history) if it's the owner viewing
$booked_events = [];
if ($is_own_profile) {
    $booked_events = $all_bookings;
}

// 9. Prepare Display Variables
$initials = strtoupper(substr($user_data['user_name'], 0, 1));
if (!empty($user_data['last_name'])) {
    $initials .= strtoupper(substr($user_data['last_name'], 0, 1));
}

$username_display = '@' . htmlspecialchars($user_data['user_name']);
$full_name = htmlspecialchars($user_data['first_name'] . ' ' . $user_data['last_name']);
$join_date = date('M Y', strtotime($user_data['created_at']));

// 10. User Stats (HYBRID: Real Matches, Dummy Performance)
$user_stats = [
    // Real Data: Count of games they have actually booked/joined
    'matches' => count($all_bookings), 
    
    // Dummy Data: Hardcoded for visualization purposes until stats module is built
    'goals' => 12, 
    'mvps' => 3, 
    'rating' => 4.8 
];
?>