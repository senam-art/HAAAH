<?php

require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/settings/db_class.php';

class User extends db_connection
{
    private $user_id;
    private $name;
    private $email;
    private $phone_number;
    private $date_created;

    public function __construct($user_id = null)
    {
        parent::db_connect();
        if ($user_id) {
            $this->user_id = $user_id;
            $this->loadUser();
        }
    }

    // Load user by ID into the object
    private function loadUser(): bool
    {
        $stmt = $this->db->prepare("SELECT * FROM customer WHERE customer_id = ?");
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_assoc();

        if (!$data) return false;

        $this->first_name = $data['first_name'];
        $this->last_name = $data['last_name'];
        $this->email = $data['customer_email'];
        $this->phone_number = $data['customer_contact'];
        $this->date_created = $data['date_created'] ?? null;

        return true;
    }

    // Create a new user
    public function createUser($first_name, $last_name, $email, $username, $password, $location)
    {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare(
            "INSERT INTO users 
            (first_name, last_name, email, user_name, password, location) 
            VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssssss", $first_name, $last_name, $email, $username, $hashed_password, $location);
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    // Get user data by email
    public function getUserByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }


    // Get user data by username
    public function getUserByUsername($username)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE user_name = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Getters
    public function getId() { return $this->user_id; }
    public function getName() { return $this->name; }
    public function getEmail() { return $this->email; }
    public function getPhoneNumber() { return $this->phone_number; }
    public function getDateCreated() { return $this->date_created; }
}
