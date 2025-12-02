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
     * Matches the method name called in debug_test.php
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

        // 3. Create User
        $newId = $user->create_user(
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['user_name'],
            $data['password'],
            $data['location'] ?? ''
        );

        if ($newId) {
            // Start session immediately
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['user_id'] = $newId;
            $_SESSION['role'] = 0;
            $_SESSION['user_name'] = $data['user_name'];
            
            return ['success' => true, 'id' => $newId];
        }
        
        return ['success' => false, 'message' => 'Registration failed (Database Error).'];
    }

    /**
     * Login User Action
     */
    public function login_user_ctr($usernameOrEmail, $password) {
        // 1. Find User
        $userData = $this->get_user_by_username_or_email($usernameOrEmail);
        
        if (!$userData) {
            return ['success' => false, 'message' => 'User not found.'];
        }

        // 2. Verify Password
        if (password_verify($password, $userData['password'])) {
            
            // 3. Set Session
            if (session_status() === PHP_SESSION_NONE) session_start();
            
            // Use 'id' as per your database schema
            $_SESSION['user_id'] = $userData['id']; 
            $_SESSION['role'] = $userData['role'];
            $_SESSION['user_name'] = $userData['user_name'];

            // 4. Intelligent Redirect
            $redirect = '../view/homepage.php'; // Default
            
            if (isset($_SESSION['redirect_to'])) {
                $redirect = $_SESSION['redirect_to'];
                unset($_SESSION['redirect_to']);
            } elseif ($userData['role'] == 1) {
                $redirect = '../admin/dashboard.php';
            }

            return [
                'success' => true, 
                'message' => 'Login successful',
                'redirect' => $redirect,
                'user_id' => $userData['id']
            ];
        }

        return ['success' => false, 'message' => 'Incorrect password.'];
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
        $result = $user->update_user_profile(
            $user_id,
            trim($data['first_name']),
            trim($data['last_name']),
            trim($data['user_name']),
            trim($data['location'] ?? ''),
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