<?php
require_once '../settings/core.php';

// Destroy the session
session_destroy();

// Redirect to homepage
header('Location: ../view/homepage.php');
exit;
