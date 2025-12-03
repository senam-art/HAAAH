<?php
require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/classes/venue_class.php';

// --- NEW: Get My Venues (Owner Dashboard) ---
function get_my_venues_ctr() {
    $venue = new Venue();
    if (!isset($_SESSION['user_id'])) return [];
    
    $venues = $venue->get_venues_by_owner($_SESSION['user_id']);

    if ($venues) {
        foreach ($venues as &$v) {
            // Process Images
            if (isset($v['image_urls'])) {
                $decoded = is_string($v['image_urls']) ? json_decode($v['image_urls'], true) : $v['image_urls'];
                $v['image_urls'] = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [];
            } else { $v['image_urls'] = []; }

            // Process Amenities
            if (isset($v['amenities'])) {
                $decoded = is_string($v['amenities']) ? json_decode($v['amenities'], true) : $v['amenities'];
                $v['amenities'] = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [];
            } else { $v['amenities'] = []; }

            $v['cover_image'] = !empty($v['image_urls']) ? $v['image_urls'][0] : 'https://images.unsplash.com/photo-1522770179533-24471fcdba45?auto=format&fit=crop&q=80';
        }
    }
    return $venues;
}

// --- NEW: Update Venue Logic ---
function update_venue_ctr($data, $files) {
    $venue = new Venue();
    
    // 1. Basic Inputs
    $venue_id = intval($data['venue_id']);
    $owner_id = $_SESSION['user_id'];
    $name = htmlspecialchars(trim($data['venue_name']));
    $address = htmlspecialchars(trim($data['venue_address']));
    $lat = !empty($data['lat']) ? floatval($data['lat']) : 0.0;
    $lng = !empty($data['lng']) ? floatval($data['lng']) : 0.0;
    $cost = floatval($data['cost_per_hour']);
    $capacity = intval($data['capacity']);
    $phone = htmlspecialchars(trim($data['phone']));
    $email = htmlspecialchars(trim($data['email']));
    $desc = htmlspecialchars(trim($data['description']));

    // 2. Amenities
    $amenities_list = isset($data['amenities']) ? $data['amenities'] : [];
    if (!empty($data['custom_amenities'])) {
        $custom_list = array_map('trim', explode(',', $data['custom_amenities']));
        $amenities_list = array_merge($amenities_list, $custom_list);
    }
    $amenities_json = json_encode(array_values(array_unique(array_filter($amenities_list))));

    // 3. Images (Merge existing with new)
    $existing_images = isset($data['existing_images']) ? $data['existing_images'] : []; // Array of URLs
    
    // Handle New Uploads
    $new_images = [];
    $upload_dir = dirname(PROJECT_ROOT) . '/uploads/venues/';
    if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);

    if (isset($files['venue_images']) && !empty($files['venue_images']['name'][0])) {
        foreach ($files['venue_images']['name'] as $key => $filename) {
            $tmp_name = $files['venue_images']['tmp_name'][$key];
            if ($files['venue_images']['error'][$key] === UPLOAD_ERR_OK) {
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                $new_name = uniqid('venue_', true) . '.' . $ext;
                if (move_uploaded_file($tmp_name, $upload_dir . $new_name)) {
                    $new_images[] = '/uploads/venues/' . $new_name;
                }
            }
        }
    }
    
    // Combine: Existing first (preserved order), then new
    $final_images = array_merge($existing_images, $new_images);
    $images_json = json_encode($final_images);

    // 4. Update
    return $venue->update_venue($venue_id, $owner_id, $name, $address, $lat, $lng, $cost, $desc, $capacity, $amenities_json, $images_json, $phone, $email);
}


// --- EXISTING FUNCTIONS (Preserved) ---

function get_all_venues_ctr() {
    $venue = new Venue();
    $venues = $venue->get_all_venues();
    if ($venues) {
        foreach ($venues as &$v) {
            // Process Images
            if (isset($v['image_urls'])) {
                $decoded = is_string($v['image_urls']) ? json_decode($v['image_urls'], true) : $v['image_urls'];
                $v['image_urls'] = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [];
            } else { $v['image_urls'] = []; }

            // Process Amenities
            if (isset($v['amenities'])) {
                $decoded = is_string($v['amenities']) ? json_decode($v['amenities'], true) : $v['amenities'];
                $v['amenities'] = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [];
            } else { $v['amenities'] = []; }

            $v['cover_image'] = !empty($v['image_urls']) ? $v['image_urls'][0] : 'https://images.unsplash.com/photo-1522770179533-24471fcdba45?auto=format&fit=crop&q=80';
        }
    }
    return $venues;
}

function get_venue_details_ctr($venue_id) {
    $venueModel = new Venue();
    $data = $venueModel->get_venue_by_id($venue_id);
    if ($data) {
        if (isset($data['image_urls']) && is_string($data['image_urls'])) {
            $decoded = json_decode($data['image_urls'], true);
            $data['image_urls'] = (json_last_error() === JSON_ERROR_NONE) ? $decoded : [];
        } else { $data['image_urls'] = []; }

        if (isset($data['amenities']) && is_string($data['amenities'])) {
            $decoded = json_decode($data['amenities'], true);
            $data['amenities'] = (json_last_error() === JSON_ERROR_NONE) ? $decoded : [];
        } else { $data['amenities'] = []; }
    }
    return $data;   
}

function get_venue_availability_ctr($venue_id, $date) {
    $venue = new Venue();
    if ($date < date('Y-m-d')) return ['error' => 'Cannot book past dates.'];
    
    $opening = 6; $closing = 22; 
    $booked_slots = $venue->get_booked_slots($venue_id, $date);
    $available_slots = [];
    $current_hour = ($date === date('Y-m-d')) ? intval(date('H')) + 1 : 0;

    for ($h = $opening; $h < $closing; $h++) {
        if ($date === date('Y-m-d') && $h < $current_hour) continue;
        $time_str = sprintf("%02d:00", $h);
        if (!in_array($time_str, $booked_slots)) $available_slots[] = $time_str;
    }
    return ['venue_id' => $venue_id, 'date' => $date, 'available' => $available_slots];
}

// --- NEW: Add Venue Controller (With External Upload Path) ---
function add_venue_ctr($data, $files) {
    $venue = new Venue();
    
    // 1. Sanitize Basic Inputs
    $owner_id = $_SESSION['user_id'];
    $name = htmlspecialchars(trim($data['venue_name']));
    $address = htmlspecialchars(trim($data['venue_address']));
    $lat = !empty($data['lat']) ? floatval($data['lat']) : 0.0;
    $lng = !empty($data['lng']) ? floatval($data['lng']) : 0.0;
    $cost = floatval($data['cost_per_hour']);
    $capacity = intval($data['capacity']);
    $phone = htmlspecialchars(trim($data['phone']));
    $email = htmlspecialchars(trim($data['email']));
    
    // 2. Handle Description & Dimensions
    $desc = htmlspecialchars(trim($data['description']));
    if (!empty($data['pitch_length']) && !empty($data['pitch_width'])) {
        $desc .= "\n\nDimensions: " . htmlspecialchars($data['pitch_length']) . "m x " . htmlspecialchars($data['pitch_width']) . "m";
    }

    // 3. Handle Amenities
    $amenities_list = isset($data['amenities']) ? $data['amenities'] : [];
    if (!empty($data['custom_amenities'])) {
        $custom_list = array_map('trim', explode(',', $data['custom_amenities']));
        $amenities_list = array_merge($amenities_list, $custom_list);
    }
    $amenities_list = array_unique(array_filter($amenities_list));
    $amenities_json = json_encode(array_values($amenities_list));

    // 4. Handle Image Uploads (OUTSIDE PROJECT DIRECTORY)
    $uploaded_urls = [];
    
    // FIX: Step out of PROJECT_ROOT to find the sibling 'uploads' folder
    // Example: If project is C:/xampp/htdocs/HAAAH, this points to C:/xampp/htdocs/uploads/venues/
    $upload_dir = dirname(PROJECT_ROOT) . '/uploads/venues/';
    
    // Create directory if not exists (Recursive)
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            error_log("Failed to create upload directory: " . $upload_dir);
            return false; // Fail early if we can't save files
        }
    }

    if (isset($files['venue_images']) && !empty($files['venue_images']['name'][0])) {
        foreach ($files['venue_images']['name'] as $key => $filename) {
            $tmp_name = $files['venue_images']['tmp_name'][$key];
            $error = $files['venue_images']['error'][$key];
            
            if ($error === UPLOAD_ERR_OK) {
                // Generate unique name
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                $new_name = uniqid('venue_', true) . '.' . $ext;
                $destination = $upload_dir . $new_name;
                
                if (move_uploaded_file($tmp_name, $destination)) {
                    // Store ABSOLUTE WEB PATH
                    // Assuming 'uploads' is in the server root (htdocs), this URL works from anywhere
                    $uploaded_urls[] = '/uploads/venues/' . $new_name;
                }
            }
        }
    }
    
    // Fallback image
    if (empty($uploaded_urls)) {
        $uploaded_urls[] = 'https://images.unsplash.com/photo-1522770179533-24471fcdba45?auto=format&fit=crop&q=80';
    }
    $images_json = json_encode($uploaded_urls);

    // 5. Call Model
    return $venue->add_venue($owner_id, $name, $address, $lat, $lng, $cost, $desc, $capacity, $amenities_json, $images_json, $phone, $email);
}

// --- NEW: Delete Venue Logic ---
function delete_venue_ctr($venue_id) {
    $venue = new Venue();
    // Security: Check session here or rely on Action file
    if (!isset($_SESSION['user_id'])) return false;
    
    return $venue->delete_venue($venue_id, $_SESSION['user_id']);
}
?>