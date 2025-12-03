<?php
// --- CONFIGURATION ---
// Ensure this matches the filename where you saved the class code you provided
$class_file = 'db_class.php'; 

// Basic styling for readability
echo '
<style>
    body { font-family: monospace; background: #1a1a1a; color: #ddd; padding: 20px; }
    .card { background: #2a2a2a; padding: 20px; border-radius: 8px; border: 1px solid #444; max-width: 600px; margin: 0 auto; }
    .success { color: #4ade80; font-weight: bold; }
    .error { color: #f87171; font-weight: bold; }
    .info { color: #60a5fa; }
    hr { border: 0; border-top: 1px solid #444; margin: 15px 0; }
</style>
<div class="card">';

echo "<h2>🔌 Database Connection Test</h2>";

// 1. Check if Class File Exists
if (!file_exists($class_file)) {
    die("<p class='error'>❌ Error: Could not find '$class_file'. <br>Please make sure this test file is in the same folder as your database class file.</p></div>");
}

require_once $class_file;

// 2. Check if Constants are Defined (Loaded from db_cred.php via the class file)
echo "<p>Checking Credentials...</p>";
$creds = ['SERVER', 'USERNAME', 'PASSWD', 'DATABASE'];
$missing_creds = [];

foreach ($creds as $cred) {
    if (defined($cred)) {
        $val = constant($cred);
        // Mask password for security
        if ($cred === 'PASSWD') $val = str_repeat('*', strlen($val) ?: 0) . (strlen($val) ? ' (Set)' : ' (Empty)');
        echo "<div><strong>$cred:</strong> <span class='info'>$val</span></div>";
    } else {
        $missing_creds[] = $cred;
        echo "<div><strong>$cred:</strong> <span class='error'>NOT DEFINED</span></div>";
    }
}

echo "<hr>";

if (!empty($missing_creds)) {
    die("<p class='error'>❌ Critical: Missing constants in db_cred.php. Connection aborted.</p></div>");
}

// 3. Attempt Connection
echo "<p>Attempting Connection...</p>";

try {
    $db_obj = new db_connection();
    
    // Attempt to connect
    if ($db_obj->db_connect()) {
        echo "<p class='success'>✅ CONNECTION SUCCESSFUL!</p>";
        
        // Output Server Info
        if ($db_obj->db) {
            echo "<div><strong>Host Info:</strong> " . $db_obj->db->host_info . "</div>";
            echo "<div><strong>Server Version:</strong> " . $db_obj->db->server_info . "</div>";
            echo "<div><strong>Protocol:</strong> " . $db_obj->db->protocol_version . "</div>";
        }
        
    } else {
        echo "<p class='error'>❌ CONNECTION FAILED</p>";
        echo "<div><strong>MySQLi Error:</strong> " . mysqli_connect_error() . "</div>";
        echo "<div><strong>Error No:</strong> " . mysqli_connect_errno() . "</div>";
    }

} catch (Exception $e) {
    echo "<p class='error'>❌ PHP Exception Triggered:</p>";
    echo "<div>" . $e->getMessage() . "</div>";
}

echo "</div>";
?>