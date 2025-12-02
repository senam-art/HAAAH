<?php
session_start();
require_once __DIR__ . '/../controllers/venue_controller.php';

// 1. Check Login & Authorization
if (!isset($_SESSION['user_id'])) {
    header("Location: ../view/login.php?msg=login_to_list_venue");
    exit();
}
$owner_id = $_SESSION['user_id'];

if (!isset($_POST['submit_venue'])) {
    header("Location: ../view/venue_portal.php");
    exit();
}

// 2. Collect & Process Inputs
$name = $_POST['venue_name'];
$address = $_POST['venue_address'];
$lat = floatval($_POST['lat'] ?? 0.0);
$lng = floatval($_POST['lng'] ?? 0.0);
$cost = floatval($_POST['cost_per_hour'] ?? 0.0);
$capacity = intval($_POST['capacity'] ?? 0);
$phone = $_POST['phone'];
$email = $_POST['email'];

// --- PITCH DIMENSIONS & DESCRIPTION ---
$pitch_length = $_POST['pitch_length'] ?? null;
$pitch_width = $_POST['pitch_width'] ?? null;
$description = $_POST['description'];

if (!empty($pitch_length) && !empty($pitch_width)) {
    $dimensions_text = "Dimensions: " . htmlspecialchars($pitch_length) . "m x " . htmlspecialchars($pitch_width) . "m. ";
    $description = $dimensions_text . $description;
}

// --- AMENITIES PROCESSING ---
$amenities_array = isset($_POST['amenities']) ? $_POST['amenities'] : [];
$custom_amenities_string = trim($_POST['custom_amenities'] ?? '');

if (!empty($custom_amenities_string)) {
    $custom_amenities = array_map('trim', explode(',', $custom_amenities_string));
    $amenities_array = array_merge($amenities_array, $custom_amenities);
}
$amenities_json = json_encode(array_values(array_unique(array_filter($amenities_array))));


// 3. Handle MULTIPLE Image Uploads (FIXED PATH LOGIC)
$image_paths = [];
$venue_folder_name = uniqid('v_');

// Primary Target (Outside HAAAH folder - Sibling to HAAAH in htdocs)
$base_upload_dir = dirname(__DIR__, 2) . "/uploads/venues/";
$base_web_url = "/uploads/venues/"; 
$target_dir = $base_upload_dir . $venue_folder_name . "/";
$target_url = $base_web_url . $venue_folder_name . "/";

// Fallback path inside the HAAAH project folder (relative path)
$fallback_dir = dirname(__DIR__) . "/assets/uploads/venues/" . $venue_folder_name . "/";
$fallback_url = "../assets/uploads/venues/" . $venue_folder_name . "/"; 


// 3.1. Determine Final Paths and Create Directory
if (is_writable(dirname(__DIR__, 2) . "/uploads/") && !file_exists($target_dir)) {
    // Attempt to use external folder
    $final_dir = $target_dir;
    $final_url_base = $target_url;
} else {
    // Use fallback inside project (more reliable on constrained local environments)
    $final_dir = $fallback_dir;
    $final_url_base = $fallback_url;
}

if (!file_exists($final_dir)) {
    if (!mkdir($final_dir, 0777, true)) {
        // If directory creation fails, we cannot proceed with image upload.
        $image_urls_json = json_encode(["assets/images/venue_placeholder.jpg"]);
        goto skip_file_upload;
    }
}


if (isset($_FILES['venue_images']) && is_array($_FILES['venue_images']['name'])) {
    foreach ($_FILES['venue_images']['tmp_name'] as $key => $tmp_name) {
        $file_error = $_FILES['venue_images']['error'][$key];
        
        if ($file_error === UPLOAD_ERR_OK) {
            $file_name = $_FILES['venue_images']['name'][$key];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($file_ext, $allowed)) {
                // Naming convention: main image is always index 0
                $new_name = $key === 0 ? "main." . $file_ext : "img_" . uniqid() . "." . $file_ext;
                $destination = $final_dir . $new_name;

                if (move_uploaded_file($tmp_name, $destination)) {
                    // Store the reliable URL path
                    $image_paths[] = $final_url_base . $new_name;
                }
            }
        }
    }
}

// Default image if no valid images uploaded
if (empty($image_paths)) {
    $image_paths[] = "../assets/images/venue_placeholder.jpg"; 
}
$image_urls_json = json_encode($image_paths);

skip_file_upload: // Label for goto if directory failed

// 4. Save to Database
$result = create_venue_ctr($owner_id, $name, $address, $lat, $lng, $cost, $description, $capacity, $amenities_json, $image_urls_json, $phone, $email);

if ($result['success']) {
    header("Location: ../view/homepage.php?msg=venue_submitted_for_review");
} else {
    // Log the error for the developer and redirect user
    // In a real system, you'd unlink the files here if DB failed.
    error_log("Venue Creation DB Error: " . $result['message']);
    header("Location: ../view/venue_portal.php?error=db_error");
}
?>