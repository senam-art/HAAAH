<?php
require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/classes/venue_class.php';

// --- ADD VENUE (Create) ---
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

    // 4. Handle Image Uploads (Relative to Project Root)
    $uploaded_urls = [];
    
    // Define path relative to this file (controllers/../uploads/venues)
    $upload_dir = dirname(__DIR__) . '/uploads/venues';

    // Create directory if missing
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    if (isset($files['venue_images']) && !empty($files['venue_images']['name'][0])) {
        foreach ($files['venue_images']['name'] as $key => $filename) {
            $tmp_name = $files['venue_images']['tmp_name'][$key];
            $error = $files['venue_images']['error'][$key];

            if ($error === UPLOAD_ERR_OK && is_uploaded_file($tmp_name)) {
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                
                if (in_array($ext, $allowed)) {
                    $new_name = uniqid('venue_', true) . '.' . $ext;
                    $destination = $upload_dir . '/' . $new_name;

                    if (move_uploaded_file($tmp_name, $destination)) {
                        // STORE RELATIVE PATH FOR VIEWS
                        // Views are in 'view/' folder, so they need to go up one level to find uploads
                        $uploaded_urls[] = '../uploads/venues/' . $new_name;
                    }
                }
            }
        }
    }
    
    // Fallback image if empty
    if (empty($uploaded_urls)) {
        $uploaded_urls[] = 'https://images.unsplash.com/photo-1522770179533-24471fcdba45?auto=format&fit=crop&q=80';
    }
    $images_json = json_encode($uploaded_urls);

    // 5. Call Model
    return $venue->add_venue($owner_id, $name, $address, $lat, $lng, $cost, $desc, $capacity, $amenities_json, $images_json, $phone, $email);
}

// --- UPDATE VENUE (Edit) ---
function update_venue_ctr($data, $files) {
    $venue = new Venue();
    
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

    // Amenities
    $amenities_list = isset($data['amenities']) ? $data['amenities'] : [];
    if (!empty($data['custom_amenities'])) {
        $custom_list = array_map('trim', explode(',', $data['custom_amenities']));
        $amenities_list = array_merge($amenities_list, $custom_list);
    }
    $amenities_json = json_encode(array_values(array_unique(array_filter($amenities_list))));

    // Images
    $existing_images = isset($data['existing_images']) ? $data['existing_images'] : [];
    $new_images = [];
    
    // Define path relative to this file
    $upload_dir = dirname(__DIR__) . '/uploads/venues';

    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

    if (isset($files['venue_images']) && !empty($files['venue_images']['name'][0])) {
        foreach ($files['venue_images']['name'] as $key => $filename) {
            $tmp_name = $files['venue_images']['tmp_name'][$key];
            if ($files['venue_images']['error'][$key] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                $new_name = uniqid('venue_', true) . '.' . $ext;
                if (move_uploaded_file($tmp_name, $upload_dir . '/' . $new_name)) {
                    // Store relative path
                    $new_images[] = '../uploads/venues/' . $new_name;
                }
            }
        }
    }

    $final_images = array_merge($existing_images, $new_images);
    $images_json = json_encode($final_images);

    return $venue->update_venue($venue_id, $owner_id, $name, $address, $lat, $lng, $cost, $desc, $capacity, $amenities_json, $images_json, $phone, $email);
}

// --- GETTERS (Owner) ---
function get_my_venues_ctr() {
    $venue = new Venue();
    if (!isset($_SESSION['user_id'])) return [];
    
    $venues = $venue->get_venues_by_owner($_SESSION['user_id']);

    if ($venues) {
        foreach ($venues as &$v) {
            $v['image_urls'] = (!empty($v['image_urls'])) ? json_decode($v['image_urls'], true) : [];
            $v['amenities'] = (!empty($v['amenities'])) ? json_decode($v['amenities'], true) : [];
            $v['cover_image'] = !empty($v['image_urls']) ? $v['image_urls'][0] : '';
        }
    }
    return $venues;
}

// --- GETTERS (Public) ---
function get_all_venues_ctr() {
    $venue = new Venue();
    $venues = $venue->get_all_venues();
    if ($venues) {
        foreach ($venues as &$v) {
            $v['image_urls'] = (!empty($v['image_urls'])) ? json_decode($v['image_urls'], true) : [];
            $v['amenities'] = (!empty($v['amenities'])) ? json_decode($v['amenities'], true) : [];
            $v['cover_image'] = !empty($v['image_urls']) ? $v['image_urls'][0] : '';
        }
    }
    return $venues;
}

// --- GETTERS (Popular) ---
function get_popular_venues_ctr() {
    $venue = new Venue();
    $venues = $venue->get_popular_venues();
    if ($venues) {
        foreach ($venues as &$v) {
            $v['image_urls'] = (!empty($v['image_urls'])) ? json_decode($v['image_urls'], true) : [];
            // Assign cover image for the card
            $v['cover_image'] = !empty($v['image_urls']) ? $v['image_urls'][0] : 'https://images.unsplash.com/photo-1522770179533-24471fcdba45?auto=format&fit=crop&q=80';
        }
    }
    return $venues;
}


function get_venue_details_ctr($venue_id) {
    $venueModel = new Venue();
    $data = $venueModel->get_venue_by_id($venue_id);
    if ($data) {
        $data['image_urls'] = (!empty($data['image_urls'])) ? json_decode($data['image_urls'], true) : [];
        $data['amenities'] = (!empty($data['amenities'])) ? json_decode($data['amenities'], true) : [];
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

// --- DELETE ---
function delete_venue_ctr($venue_id) {
    $venue = new Venue();
    if (!isset($_SESSION['user_id'])) return false;
    return $venue->delete_venue($venue_id, $_SESSION['user_id']);
}
?>