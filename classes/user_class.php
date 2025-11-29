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

        $this->name = $data['customer_name'];
        $this->email = $data['customer_email'];
        $this->phone_number = $data['customer_contact'];
        $this->date_created = $data['date_created'] ?? null;

        return true;
    }

    // Create a new user
    public function createUser($name, $email, $password, $phone, $country, $city)
    {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare(
            "INSERT INTO customer (customer_name, customer_email, customer_pass, customer_contact, customer_country, customer_city) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssssss", $name, $email, $hashed_password, $phone, $country, $city);
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    // Get user data by email
    public function getUserByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM customer WHERE customer_email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Login user
    public function loginUser($email, $password)
    {
        $user = $this->getUserByEmail($email);
        if ($user && password_verify($password, $user['customer_pass'])) {
            return $user;
        }
        return false;
    }

    // Getters
    public function getId() { return $this->user_id; }
    public function getName() { return $this->name; }
    public function getEmail() { return $this->email; }
    public function getPhoneNumber() { return $this->phone_number; }
    public function getDateCreated() { return $this->date_created; }
}
