<?php
// 1. FORCE ERROR DISPLAY
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>🕵️‍♂️ System Diagnostic Tool</h1>";
echo "<hr>";

// STEP 1: Check File Paths
echo "<h3>Step 1: Checking File Paths</h3>";

$controller_path = __DIR__ . '/../controllers/guest_controller.php';
$class_path = __DIR__ . '/../classes/guest_class.php';
$settings_path = __DIR__ . '/../settings/db_class.php';

check_file($controller_path, "Guest Controller");
check_file($class_path, "Guest Class");
check_file($settings_path, "DB Settings");

// STEP 2: Test Includes & Instantiation
echo "<h3>Step 2: Testing Class Loading</h3>";

try {
    if (file_exists($controller_path)) {
        require_once $controller_path;
        echo "<div style='color:green'>✅ Controller included successfully.</div>";
    } else {
        throw new Exception("Cannot include controller - file missing.");
    }

    if (class_exists('Guest')) {
        echo "<div style='color:green'>✅ 'Guest' Class definition found.</div>";
    } else {
        throw new Exception("Class 'Guest' not found. Check class naming in guest_class.php.");
    }

    // Try to instantiate
    $guest = new Guest();
    echo "<div style='color:green'>✅ Guest Object instantiated successfully. (DB Connection working)</div>";

} catch (Throwable $e) {
    die("<div style='color:red; font-weight:bold'>❌ FATAL ERROR in Step 2: " . $e->getMessage() . "</div>");
}

// STEP 3: Test Data Fetching
echo "<h3>Step 3: Testing Database Query (Event ID: 1)</h3>";

try {
    // Test getEvent
    if (method_exists($guest, 'getEvent')) {
        $event = $guest->getEvent(1); // Try to fetch ID 1
        
        if ($event) {
            echo "<div style='color:green'>✅ getEvent(1) returned data:</div>";
            echo "<pre style='background:#f4f4f4; padding:10px; border:1px solid #ccc;'>" . print_r($event, true) . "</pre>";
        } else {
            echo "<div style='color:orange'>⚠️ getEvent(1) returned nothing. (Query ran, but maybe ID 1 doesn't exist?)</div>";
            echo "<p>SQL executed properly but returned 0 rows.</p>";
        }
    } else {
        throw new Exception("Method 'getEvent' is missing in Guest class.");
    }

    // Test getEventPlayers
    if (method_exists($guest, 'getEventPlayers')) {
        $players = $guest->getEventPlayers(1);
        echo "<div style='color:green'>✅ getEventPlayers(1) ran successfully. Count: " . (is_array($players) ? count($players) : 0) . "</div>";
    }

} catch (Throwable $e) {
    die("<div style='color:red; font-weight:bold'>❌ DATABASE ERROR in Step 3: " . $e->getMessage() . "</div>");
}

echo "<hr><h3 style='color:green'>🎉 If you see this, the Backend Logic is 100% Healthy!</h3>";

// --- Helper Function ---
function check_file($path, $name) {
    if (file_exists($path)) {
        echo "<div style='color:green'>✅ Found $name</div>";
    } else {
        echo "<div style='color:red; font-weight:bold'>❌ MISSING $name at: $path</div>";
    }
}
?>