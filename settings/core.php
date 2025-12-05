<?php
// settings/core.php

// 1. Start Session (Safe Check)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- AUTO-LOGOUT LOGIC ---
// Duration in seconds (1800 = 30 minutes)
$timeout_duration = 1800; 

if (isset($_SESSION['user_id'])) {
    // Check if we have a last activity timestamp
    if (isset($_SESSION['LAST_ACTIVITY'])) {
        $inactive_time = time() - $_SESSION['LAST_ACTIVITY'];
        
        // If inactive time exceeds duration, log out
        if ($inactive_time > $timeout_duration) {
            session_unset();
            session_destroy();
            header("Location: ../view/login.php?msg=session_expired");
            exit();
        }
    }
    // Update last activity timestamp
    $_SESSION['LAST_ACTIVITY'] = time();
}
// -------------------------

// 2. Define File System Root
if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', dirname(__DIR__));
}

// 3. Define Web Path Constants (Crucial for Images & Links)
const PROJECT_FOLDER_NAME = 'HAAAH'; 

// Calculate Web Root
if (isset($_SERVER['SERVER_NAME'])) {
    if (strpos($_SERVER['REQUEST_URI'], '/' . PROJECT_FOLDER_NAME) === 0) {
        $web_root = '/' . PROJECT_FOLDER_NAME;
    } else {
        $web_root = ''; 
    }
} else {
    $web_root = '/' . PROJECT_FOLDER_NAME; 
}

define('WEB_ROOT', $web_root);
// Assumes uploads are inside the project folder as per your latest setup
define('UPLOADS_FS', PROJECT_ROOT . '/uploads');
define('UPLOADS_URL', $web_root . '/uploads');


// --- HELPER FUNCTIONS ---

/**
 * Redirect user if already logged in
 */
function redirectIfLoggedIn()
{
    if (!isset($_SESSION['user_id'])) {
        return; 
    }

    $role = isset($_SESSION['role']) ? intval($_SESSION['role']) : 0;

    if ($role === 1) {
        // Redirect Venue Managers to their dashboard
        header("Location: manage_venues.php"); 
    } elseif ($role === 2) {
        // Redirect Admins to their dashboard
        header("Location: admin_dashboard.php");
    } else {
        // Regular users go to homepage
        header("Location: homepage.php");
    }
    exit();
}

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

/**
 * Check Admin Role (Role = 2)
 */
function check_admin_role() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] != 2) {
        header("Location: ../view/homepage.php?error=unauthorized");
        exit();
    }
}

// Paystack Secret Key
if (!defined('PAYSTACK_SECRET_KEY')) {
    define('PAYSTACK_SECRET_KEY', 'sk_test_319a4bce5fb94ebdf86fdb8a81a216683008e1d7');
}
?>