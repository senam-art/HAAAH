<?php
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../settings/connection.php';

// 1. Security: Ensure user is Admin
// Assuming you have a 'role' column in your users table (1=Admin, 0=User)
// if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
//     die("Access Denied");
// }

if (isset($_POST['approve_btn'])) {
    $event_id = intval($_POST['event_id']);

    // 2. Update Database
    $sql = "UPDATE events SET is_approved = 1 WHERE event_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $event_id);

    if ($stmt->execute()) {
        header("Location: ../admin/dashboard.php?msg=approved");
    } else {
        header("Location: ../admin/dashboard.php?error=failed");
    }
}
?>