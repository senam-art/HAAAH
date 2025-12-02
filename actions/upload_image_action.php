<?php
// actions/upload_image_action.php

require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/classes/user_class.php';

// 1. Security Check
check_login();

// 2. Check for File
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['profile_pic'])) {
    header("Location: ../view/profile.php");
    exit();
}

$file = $_FILES['profile_pic'];
$user_id = get_user_id();

// 3. Validation
if ($file['error'] !== UPLOAD_ERR_OK) {
    header("Location: ../view/profile.php?error=upload_error_code_" . $file['error']);
    exit();
}

$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime_type = $finfo->file($file['tmp_name']);

if (!in_array($mime_type, $allowed_types)) {
    header("Location: ../view/profile.php?error=invalid_file_type");
    exit();
}

if ($file['size'] > 5 * 1024 * 1024) {
    header("Location: ../view/profile.php?error=file_too_large");
    exit();
}

// 4. Path Configuration
// Go up two levels to reach 'htdocs' (or project parent)
$server_root = dirname(PROJECT_ROOT); 
$upload_dir = $server_root . '/uploads/user_profile_pictures/' . $user_id . '/';

// Create directory if it doesn't exist
if (!is_dir($upload_dir)) {
    if (!mkdir($upload_dir, 0777, true)) {
        header("Location: ../view/profile.php?error=directory_create_failed");
        exit();
    }
}

// 5. CLEANUP: Delete Old Image
$user = new User();
$current_user = $user->get_user_by_id($user_id);

if ($current_user && !empty($current_user['profile_details'])) {
    $details = json_decode($current_user['profile_details'], true);
    
    // Check if a profile image is already set
    if (isset($details['profile_image']) && !empty($details['profile_image'])) {
        // Construct the absolute system path to the old file
        // Stored path: /uploads/user_profile_pictures/1/avatar.jpg
        $old_file_path = $server_root . $details['profile_image'];
        
        // Ensure we only delete files within our specific uploads directory (Security check)
        if (strpos($old_file_path, '/uploads/user_profile_pictures/') !== false) {
            if (file_exists($old_file_path)) {
                unlink($old_file_path); // Delete the file
            }
        }
    }
}

// 6. Generate Filename & Move New File
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$new_filename = 'profile_' . time() . '.' . $extension; 
$destination = $upload_dir . $new_filename;

if (move_uploaded_file($file['tmp_name'], $destination)) {
    
    // 7. Update Database
    $web_path = '/uploads/user_profile_pictures/' . $user_id . '/' . $new_filename;

    if ($user->update_profile_image($user_id, $web_path)) {
        header("Location: ../view/profile.php?msg=avatar_updated");
    } else {
        header("Location: ../view/profile.php?error=db_update_failed");
    }

} else {
    header("Location: ../view/profile.php?error=move_failed");
}
exit();
?>