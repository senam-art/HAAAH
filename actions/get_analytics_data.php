<?php
// 1. Bootstrap Core
require_once __DIR__ . '/../settings/core.php';

// 2. Import Controller
require_once PROJECT_ROOT . '/controllers/analytics_controller.php';

// 3. Security Check
check_login();
$user_id = get_user_id();

// 4. Fetch Data using Controllers
$payment_history = get_payment_history_ctr($user_id);
$organizer_stats = get_organizer_stats_ctr($user_id);

// 5. Variables are now ready for the View
// $payment_history: Array of transactions
// $organizer_stats: Associative array ['events_hosted', 'players_hosted', 'revenue_generated']
?>