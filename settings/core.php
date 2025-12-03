<?php

// 1. Start Session (Safe Check)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Root of the project (HAAAH)
// 2. Define Root Path (Required for classes to find files)
if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', dirname(__DIR__));
}


// settings/paths.php or core.php

// Filesystem path (always absolute, stays the same)
define('UPLOADS_FS', dirname(PROJECT_ROOT) . '/uploads');

// Browser URL path (dynamic)
$doc_root = $_SERVER['DOCUMENT_ROOT']; // e.g., /home/senam.dzomeku/public_html

if (strpos(PROJECT_ROOT, $doc_root) === 0) {
    // Local XAMPP or server without ~username
    $uploads_url = '/uploads';
} else {
    // Live server using ~username
    $user_dir = str_replace($doc_root, '', PROJECT_ROOT);
    $uploads_url = $user_dir . '/uploads';
}

define('UPLOADS_URL', $uploads_url);



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