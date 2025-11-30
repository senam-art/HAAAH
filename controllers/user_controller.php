<?php

require_once __DIR__ . '/../settings/core.php';
require_once PROJECT_ROOT . '/classes/user_class.php';

class UserController {
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Get user by username or email for login
     * @param string $input
     * @return array|null
     */
    public function get_user_by_username_or_email($input)
    {
        // Try by username first
        $user = $this->userModel->getUserByUsername($input);
        if ($user) return $user;
        // Try by email
        return $this->userModel->getUserByEmail($input);
    }

    /**
     * Login user using username or email and password
     * @param array $data
     * @return array
     */
    public function login_user(array $data)
    {
        // required fields
        $usernameOrEmail = trim($data['username'] ?? $data['email'] ?? '');
        $password = $data['password'] ?? '';

        if (!$usernameOrEmail || !$password) {
            return ['success' => false, 'message' => 'Username/email and password required.'];
        }

        $user = $this->get_user_by_username_or_email($usernameOrEmail);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found.'];
        }

        // Password field name expected in `users` table is `password` (hashed)
        $hash = $user['password'] ?? $user['customer_pass'] ?? null;
        if (!$hash || !password_verify($password, $hash)) {
            return ['success' => false, 'message' => 'Incorrect password.'];
        }

        // set session values (ensure core started the session)
        // try common id field names
        $uid = $user['user_id'] ?? $user['id'] ?? $user['customer_id'] ?? $user['userid'] ?? $user['userId'] ?? null;
        $uname = $user['user_name'] ?? $user['username'] ?? $user['name'] ?? $user['first_name'] ?? null;
        $uemail = $user['email'] ?? $user['customer_email'] ?? null;
        $urole = $user['user_role'] ?? $user['role'] ?? 1;

        if ($uid) $_SESSION['user_id'] = $uid;
        if ($uname) {
            // set both keys to be safe
            $_SESSION['user_name'] = $uname;
            $_SESSION['username'] = $uname;
        }
        if ($uemail) $_SESSION['user_email'] = $uemail;
        $_SESSION['user_role'] = $urole;

        return ['success' => true, 'message' => 'Login successful.', 'data' => ['user_id' => $uid, 'user_name' => $uname, 'user_role' => $urole]];
    }

    /**
     * Register a new user
     * @param array $data
     * @return array [success => bool, message => string, data => mixed]
     */
    public function register_user(array $data)
    {
        // basic server-side validation
        $required = ['first_name','last_name','email','username','password','confirm_password'];
        foreach ($required as $f) {
            if (empty($data[$f])) {
                return ['success'=>false, 'message'=> ucfirst(str_replace('_',' ',$f)) . ' is required.'];
            }
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success'=>false, 'message'=>'Invalid email address.'];
        }

        if (strlen($data['password']) < 8) {
            return ['success'=>false, 'message'=>'Password must be at least 8 characters.'];
        }

        if ($data['password'] !== $data['confirm_password']) {
            return ['success'=>false, 'message'=>'Passwords do not match.'];
        }

        // check if email already exists
        $existing = $this->userModel->getUserByEmail($data['email']);
        if ($existing) {
            return ['success'=>false, 'message'=>'An account with this email already exists.'];
        }

        // create user - city/country/phone optional
        $first_name = trim($data['first_name']);
        $last_name = trim($data['last_name']);
        $email = trim($data['email']);
        $username = trim($data['username']);
        $password = $data['password'];
        // $phone = isset($data['phone']) ? $data['phone'] : '';
        $location = isset($data['location']) ? $data['location'] : '';
        // $city = isset($data['city']) ? $data['city'] : '';

        $newId = $this->userModel->createUser($first_name, $last_name, $email, $username, $password, $location);
        if ($newId) {
            //  set session
            $_SESSION['user_id'] = $newId;
            $_SESSION['username'] = $username;
            $_SESSION['user_email'] = $email;
            return ['success'=>true, 'message'=>'Registration successful','data'=>['user_id'=>$newId]];
        }

        return ['success'=>false, 'message'=>'Could not create user - please try again later.'];
    }
}

?>
