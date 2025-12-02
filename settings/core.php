<?php
// settings/core.php

// 1. Start Session (Safe Check)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Define Root Path (Required for classes to find files)
if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', dirname(__DIR__));
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