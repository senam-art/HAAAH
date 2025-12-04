<?php
// 1. Bootstrap Core
require_once __DIR__ . '/../settings/core.php';

// 2. Import User Class
require_once PROJECT_ROOT . '/classes/user_class.php';

class UserController {

    // --- GETTERS ---

    public function get_user_by_id_ctr($id) {
        $user = new User();
        return $user->get_user_by_id($id);
    }

    public function get_user_by_username_or_email($input) {
        $user = new User();
        // Try username first
        $result = $user->get_user_by_username($input);
        if ($result) return $result;
        // Try email
        return $user->get_user_by_email($input);
    }

    // --- ACTIONS ---

   /**
     * Register User Action
     * Accepts POST data array
     */
    public function register_user_ctr($data) {
        $user = new User();
        
        // 1. Basic Validation
        if (empty($data['email']) || empty($data['password']) || empty($data['user_name'])) {
            return ['success' => false, 'message' => 'Required fields missing.'];
        }

        // 2. Check for Duplicates (Email)
        if ($user->get_user_by_email($data['email'])) {
            return ['success' => false, 'message' => 'Email already exists.'];
        }

        // 3. Prepare Role & Profile Data
        $role = isset($data['role']) ? intval($data['role']) : 0;
        $profile_json = null;

        // Only process traits if it's a Player (Role 0)
        if ($role === 0) {
            $traits_data = [
                'positions' => isset($data['positions']) ? $data['positions'] : [],
                'traits' => isset($data['traits']) ? $data['traits'] : []
            ];
            $profile_json = json_encode($traits_data);
        }

        // 4. Create User (Calling the updated Model method)
        // UPDATED: Now passing address, lat, and lng
        $address = isset($data['address']) ? $data['address'] : ''; 
        $lat = isset($data['lat']) ? $data['lat'] : 0.0;
        $lng = isset($data['lng']) ? $data['lng'] : 0.0;

        $newId = $user->create_user(
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['user_name'],
            $data['password'],
            $address,       // Replaces old 'location'
            $lat,           // NEW
            $lng,           // NEW
            $role,
            $profile_json
        );

        if ($newId) {
            // Start session immediately
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['user_id'] = $newId;
            $_SESSION['role'] = $role;
            $_SESSION['user_name'] = $data['user_name'];
            $_SESSION['email'] = $data['email'];
            
            return ['success' => true, 'id' => $newId];
        }
        
        return ['success' => false, 'message' => 'Registration failed (Database Error).'];
    }

/**
     * Login User Action
     * UPDATED: Now sets $_SESSION variables on success.
     */
    public function login_user_ctr($usernameOrEmail, $password) {
        $user = new User();

        // 1. Find User (Try Username, then Email)
        $userData = $user->get_user_by_username($usernameOrEmail);
        if (!$userData) {
            $userData = $user->get_user_by_email($usernameOrEmail);
        }
        
        if (!$userData) {
            return ['success' => false, 'message' => 'User not found.'];
        }

        // 2. Verify Password
        if (password_verify($password, $userData['password'])) {
            
            // 3. SET SESSION (The Fix)
            if (session_status() === PHP_SESSION_NONE) session_start();
            
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['role'] = $userData['role'];
            $_SESSION['user_name'] = $userData['user_name'];
            $_SESSION['email'] = $userData['email'];

            return ['success' => true, 'user' => $userData];
        }
    }


    /**
     * Update Profile Action
     */
    public function update_user_profile_ctr($user_id, $data) {
        $user = new User();

        // 1. Validation
        if (empty($data['first_name']) || empty($data['last_name']) || empty($data['user_name'])) {
            return ['success' => false, 'message' => 'Name and Username are required.'];
        }

        // 2. Format JSON for tags
        $tags_data = [
            'positions' => isset($data['positions']) ? $data['positions'] : [],
            'traits' => isset($data['traits']) ? $data['traits'] : []
        ];
        $json_details = json_encode($tags_data);

        // 3. Update
        // Note: You might want to update this later to handle map updates too
        $result = $user->update_user_profile(
            $user_id,
            trim($data['first_name']),
            trim($data['last_name']),
            trim($data['user_name']),
            trim($data['address'] ?? ($data['location'] ?? '')), // Handle both names
            $json_details
        );

        if ($result) {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['user_name'] = trim($data['user_name']);
            return ['success' => true, 'message' => 'Profile updated successfully.'];
        }

        return ['success' => false, 'message' => 'Failed to update profile.'];
    }
}
?>