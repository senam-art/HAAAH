<?php

// settings/core.php

// 1. Start Session (Safe Check)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Define File System Root (Required for PHP includes)
if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', dirname(__DIR__));
}

// 3. Define Uploads File System Path (Where files are saved)
define('UPLOADS_FS', PROJECT_ROOT . '/uploads');


// 4. Define Browser URL Root (Where images are loaded from)
// -------------------------------------------------------

$web_root = ''; // Default to root

// Check A: Is this a User Directory (Live Server e.g., /~senam.dzomeku)?
if (isset($_SERVER['REQUEST_URI']) && preg_match('|/~[^/]+|', $_SERVER['REQUEST_URI'], $matches)) {
    // Found /~username in the URL, set that as the root
    $web_root = $matches[0]; 
} 
// Check B: Is this a Subfolder (Localhost e.g., /Haaah)?
else {
    // Calculate relative path from Document Root to Project Root
    // Normalize slashes for Windows/Linux compatibility
    $fs_doc_root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
    $fs_proj_root = str_replace('\\', '/', PROJECT_ROOT);
    
    if (strpos($fs_proj_root, $fs_doc_root) === 0) {
        $web_root = substr($fs_proj_root, strlen($fs_doc_root));
    }
}

// Define the final URL constant
define('WEB_ROOT', $web_root);
define('UPLOADS_URL', $web_root . '/uploads');




/**
 * Redirect user if already logged in
 */
function redirectIfLoggedIn()
{
    if (!isset($_SESSION['user_id'])) {
        return; // user not logged in → do nothing
    }

    $role = isset($_SESSION['role']) ? intval($_SESSION['role']) : 0;

    if ($role === 1) {
        header("Location: venue-profile.php");
    } elseif ($role === 2) {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: homepage.php");
    }
    exit();
}

// --- HELPER FUNCTIONS ---

/**
 * Check if user is logged in.
 * If not, redirect to login page.
 */
function check_login() {
    if (!isset($_SESSION['user_id'])) {
        // Remember where they wanted to go
        if (isset($_SERVER['REQUEST_URI'])) {
             $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
        }
        header("Location: ../view/login.php?msg=login_required");
        exit();
    }
}

/**
 * Check if user IS ALREADY logged in.
 * If yes, redirect to homepage (Don't show login form).
 * THIS WAS THE MISSING FUNCTION CAUSING THE 500 ERROR.
 */
function hasLoggedIn() {
    if (isset($_SESSION['user_id'])) {
        header("Location: ../view/homepage.php");
        exit();
    }
}

/**
 * Get current user ID safely
 */
function get_user_id() {
    return $_SESSION['user_id'] ?? false;
}

define('PAYSTACK_SECRET_KEY', 'sk_test_319a4bce5fb94ebdf86fdb8a81a216683008e1d7'); // Paystack Secret Key


/**
 * Check Admin Role
 */
function check_admin_role() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
        header("Location: ../view/homepage.php?error=unauthorized");
        exit();
    }
}
?>