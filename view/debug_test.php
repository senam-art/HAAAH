<?php
// view/debug_analytics.php

// 1. FORCE ERROR DISPLAY (Crucial for "Blank Page" issues)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Analytics Debugger</h1>";
echo "<hr>";

// 2. PATH CHECK
echo "<h3>1