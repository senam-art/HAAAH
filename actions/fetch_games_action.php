<?php
// 1. ENABLE DEBUGGING
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');



try {
    require_once __DIR__ . '/../settings/core.php';
    require_once PROJECT_ROOT . '/controllers/guest_controller.php';

    // 2. Capture Parameters
    $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
    $lat = isset($_GET['lat']) ? $_GET['lat'] : null;
    $lng = isset($_GET['lng']) ? $_GET['lng'] : null;

    // Log Inputs
    $debug['inputs'] = [
        'search' => $searchTerm,
        'lat' => $lat,
        'lng' => $lng
    ];

    // Logic: Ignore "Current Location" text if actual coordinates exist
    if (strcasecmp($searchTerm, 'Current Location') === 0 && $lat && $lng) {
        $searchTerm = '';
        $debug['logic'] = 'Cleared "Current Location" text to use coordinates';
    }

    // 3. Determine Which Method to Call
    $result = null;

    if (!empty($searchTerm)) {
        $debug['controller_method'] = 'search_events_ctr';
        $result = search_events_ctr($searchTerm);
    } elseif ($lat !== null && $lng !== null && is_numeric($lat)) {
        $debug['controller_method'] = 'get_location_events_ctr';
        $result = get_location_events_ctr($lat, $lng);
    } else {
        $debug['controller_method'] = 'get_active_events_ctr';
        $result = get_active_events_ctr();
    }

    // Log Raw Result Type
    $debug['raw_result_type'] = gettype($result);
    if (is_array($result)) {
        $debug['result_count'] = count($result);
    } elseif (is_object($result)) {
        $debug['result_class'] = get_class($result);
    }

    $events = [];

    // 4. Process Results
    // CASE A: Result is an Array (Standard from db_fetch_all)
    if (is_array($result)) {
        foreach ($result as $row) {
            $events[] = process_event_row($row);
        }
        
        // Success (even if empty)
        echo json_encode([
            'success' => true, 
            'data' => $events,
            'debug' => $debug
        ]);
    }
    // CASE B: Result is MySQL Object (Fallback)
    elseif (is_object($result) && (method_exists($result, 'fetch_assoc') || ($result instanceof mysqli_result))) {
        while ($row = $result->fetch_assoc()) {
            $events[] = process_event_row($row);
        }
        echo json_encode([
            'success' => true, 
            'data' => $events,
            'debug' => $debug
        ]);
    } 
    // CASE C: Error
    else {
        $debug['error'] = 'Database query failed or returned unexpected format.';
        echo json_encode([
            'success' => false, 
            'data' => [],
            'message' => 'No active games found.',
            'debug' => $debug
        ]);
    }

} catch (Exception $e) {
    $debug['exception'] = $e->getMessage();
    $debug['trace'] = $e->getTraceAsString();
    echo json_encode([
        'success' => false,
        'message' => 'Server Exception: ' . $e->getMessage(),
        'debug' => $debug
    ]);
}

/**
 * Helper to process row data
 */
function process_event_row($row) {
    $min_players = isset($row['min_players']) ? intval($row['min_players']) : 0;
    $current_players = isset($row['current_players']) ? intval($row['current_players']) : 0;
    $venue_name = isset($row['venue_name']) ? $row['venue_name'] : 'Unknown Venue';
    
    $spots_left = $min_players - $current_players;
    $progress = ($min_players > 0) ? ($current_players / $min_players) * 100 : 0;
    
    $row['spots_left'] = max(0, $spots_left);
    $row['progress_percent'] = min(100, $progress);
    
    // Format Distance if available
    if (isset($row['distance'])) {
        $row['venue_name'] = $venue_name . ' (' . number_format($row['distance'], 1) . ' km)';
    }
    
    // Format Dates
    if (isset($row['event_date'])) {
        $row['formatted_date'] = date('D, M j', strtotime($row['event_date']));
    }
    if (isset($row['event_time'])) {
        $row['formatted_time'] = date('H:i', strtotime($row['event_time']));
    }
    
    return $row;
}
exit;
?>