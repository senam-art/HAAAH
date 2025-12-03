<?php
// view/debug_venue.php

// 1. Force Error Display
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Venue Profile Debugger</h1>";
echo "<hr>";

// Helper for status
function status($name, $result, $msg = "") {
    $color = $result ? "green" : "red";
    $text = $result ? "PASS" : "FAIL";
    echo "<div style='margin-bottom: 10px; font-family: monospace;'>";
    echo "<strong>$name:</strong> <span style='color:$color; font-weight:bold;'>$text</span> $msg";
    echo "</div>";
    return $result;
}

// TEST 1: Check Core Settings Path
$core_path = __DIR__ . '/../settings/core.php';
if (status("Core File Exists", file_exists($core_path), "($core_path)")) {
    require_once $core_path;
} else {
    die("Stopping: Cannot find core.php");
}

// TEST 2: Check PROJECT_ROOT Constant
if (defined('PROJECT_ROOT')) {
    status("PROJECT_ROOT Defined", true, "Value: " . PROJECT_ROOT);
} else {
    status("PROJECT_ROOT Defined", false, "Is core.php loaded correctly?");
}

// TEST 3: Check Controller Path
// Note: We use __DIR__ relative path first to locate it
$controller_path = __DIR__ . '/../controllers/venue_controller.php';
$exists = file_exists($controller_path);
status("Controller File Exists (Relative)", $exists, "($controller_path)");

// TEST 4: Load Controller
if ($exists) {
    try {
        require_once $controller_path;
        status("Controller Included", true);
    } catch (Exception $e) {
        status("Controller Included", false, "Error: " . $e->getMessage());
    }
}

// TEST 5: Check for Functional Controller Method
$func_name = 'get_venue_details_ctr';
if (function_exists($func_name)) {
    status("Function '$func_name' Exists", true);
} else {
    status("Function '$func_name' Exists", false, "Did you save the functional version of venue_controller.php?");
    // Check if maybe the class version is still active
    if (class_exists('VenueController')) {
        echo "<div style='color:orange'>Warning: 'VenueController' class detected. You might be mixing Class-based and Functional code.</div>";
    }
}

// TEST 6: Database Connection & Fetch
echo "<hr><h3>Database Test</h3>";

if (function_exists($func_name)) {
    // Try fetching Venue ID 1 (Change this ID if you know 1 doesn't exist)
    $test_id = 1; 
    echo "Attempting to fetch Venue ID: $test_id ...<br><br>";
    
    $data = $func_name($test_id);
    
    if ($data) {
        status("Data Fetch", true, "Found venue: " . htmlspecialchars($data['name'] ?? 'Unknown Name'));
        echo "<pre style='background:#f4f4f4; padding:10px;'>";
        print_r($data);
        echo "</pre>";
    } else {
        status("Data Fetch", false, "Venue ID $test_id returned NULL (This might be correct if DB is empty, but check connection).");
    }
}

echo "<hr><p>End of Test.</p>";
?>