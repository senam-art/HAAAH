<?php
// 1. Bootstrap Core
require_once __DIR__ . '/../settings/core.php';

// 2. Import Database Class
require_once PROJECT_ROOT . '/settings/db_class.php';

class User extends db_connection
{
    public function __construct()
    {
        parent::db_connect(); 
    }

    /**
     * Create a new user (Sign Up)
     */
    public function create_user($first_name, $last_name, $email, $username, $password, $location)
    {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Matches your DB columns exactly: id, first_name, last_name, email, user_name, password, location, role, created_at
        $sql = "INSERT INTO users (first_name, last_name, email, user_name, password, location, role, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, 0, NOW())";

        if (!$this->db) {
            $this->db_connect();
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssssss", $first_name, $last_name, $email, $username, $hashed_password, $location);

        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    /**
     * Get user details by ID
     */
    public function get_user_by_id($id)
    {
        $sql = "SELECT * FROM users WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Get user details by Email
     */
    public function get_user_by_email($email)
    {
        $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Get user details by Username
     */
    public function get_user_by_username($username)
    {
        $sql = "SELECT * FROM users WHERE user_name = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Update Profile Tags Only (Specific Update)
     */
    public function update_profile_tags($user_id, $positions_array, $traits_array)
    {
        $data = ['positions' => $positions_array, 'traits' => $traits_array];
        $json_data = json_encode($data);

        $sql = "UPDATE users SET profile_details = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $json_data, $user_id);
        
        return $stmt->execute();
    }

    /**
     * Update Full User Profile (General Info + Tags)
     * Used by the Edit Profile Page
     */
    public function update_user_profile($user_id, $fname, $lname, $username, $location, $json_details)
    {
        $sql = "UPDATE users 
                SET first_name = ?, last_name = ?, user_name = ?, location = ?, profile_details = ? 
                WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sssssi", $fname, $lname, $username, $location, $json_details, $user_id);
        
        return $stmt->execute();
    }

    /**
     * Get Aggregated Player Stats (Goals, MVPs, Rating)
     * Note: This requires a 'player_stats' table. Returns zero-values if table doesn't exist yet.
     */
    public function get_player_stats_aggregate($user_id)
    {
        // Check if table exists first to avoid errors during development
        $check = $this->db->query("SHOW TABLES LIKE 'player_stats'");
        if($check->num_rows == 0) {
            return ['total_goals' => 0, 'total_mvps' => 0, 'avg_rating' => 0];
        }

        $sql = "SELECT 
                    SUM(goals) as total_goals, 
                    SUM(is_mvp) as total_mvps, 
                    AVG(NULLIF(rating, 0)) as avg_rating 
                FROM player_stats 
                WHERE user_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Update only the profile image path in JSON
     */
    public function update_profile_image($user_id, $image_path)
    {
        // 1. Fetch current details
        $current = $this->get_user_by_id($user_id);
        if (!$current) return false;

        $details = [];
        if (!empty($current['profile_details'])) {
            $decoded = json_decode($current['profile_details'], true);
            if (is_array($decoded)) $details = $decoded;
        }

        // 2. Update image path
        $details['profile_image'] = $image_path;
        $new_json = json_encode($details);

        // 3. Save back
        $sql = "UPDATE users SET profile_details = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $new_json, $user_id);
        
        return $stmt->execute();
    }
}
?>