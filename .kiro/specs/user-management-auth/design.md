# Design Document: User Management & Authentication

## Overview

The User Management & Authentication system is built using PHP with a MySQL database backend, following an MVC (Model-View-Controller) architecture pattern. The system provides secure user registration, authentication, session management, and role-based access control for four distinct user types: Players, Organizers, Venue Owners, and Administrators.

The design leverages the existing project structure with classes for data access, controllers for business logic, and views for presentation. Security is paramount, implementing industry-standard practices including bcrypt password hashing, secure session management, CSRF protection, and SQL injection prevention through prepared statements.

## Architecture

### System Components

The system follows a three-tier architecture:

1. **Presentation Layer (View)**: HTML/CSS/JavaScript interfaces for user interaction, mobile-responsive using Bootstrap or similar framework
2. **Business Logic Layer (Controller)**: PHP controllers handling authentication logic, validation, and authorization
3. **Data Access Layer (Class)**: PHP classes extending db_class for database operations with prepared statements

### Technology Stack

- **Backend**: PHP 7.4+ with object-oriented programming
- **Database**: MySQL 8.0+ with InnoDB storage engine
- **Session Management**: PHP native sessions with secure configuration
- **Password Hashing**: PHP password_hash() using bcrypt (PASSWORD_BCRYPT)
- **Frontend**: HTML5, CSS3, JavaScript (vanilla or jQuery), mobile-first responsive design
- **Email**: PHPMailer (already present in project) for verification and password reset emails

### Database Schema

The system uses a normalized relational database structure:

**users table**:
- user_id (INT, PRIMARY KEY, AUTO_INCREMENT)
- email (VARCHAR(255), UNIQUE, NOT NULL)
- password_hash (VARCHAR(255), NOT NULL)
- first_name (VARCHAR(100), NOT NULL)
- last_name (VARCHAR(100), NOT NULL)
- phone_number (VARCHAR(20), NOT NULL)
- role (ENUM('player', 'organizer', 'venue_owner', 'admin'), NOT NULL)
- email_verified (BOOLEAN, DEFAULT FALSE)
- account_status (ENUM('active', 'suspended', 'deleted'), DEFAULT 'active')
- created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
- last_login (TIMESTAMP, NULL)
- failed_login_attempts (INT, DEFAULT 0)
- account_locked_until (TIMESTAMP, NULL)

**user_profiles table**:
- profile_id (INT, PRIMARY KEY, AUTO_INCREMENT)
- user_id (INT, FOREIGN KEY REFERENCES users(user_id))
- bio (TEXT, NULL)
- profile_image_url (VARCHAR(500), NULL)
- location (VARCHAR(255), NULL)
- date_of_birth (DATE, NULL)
- updated_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)

**venue_details table**:
- venue_id (INT, PRIMARY KEY, AUTO_INCREMENT)
- user_id (INT, FOREIGN KEY REFERENCES users(user_id))
- venue_name (VARCHAR(255), NOT NULL)
- venue_address (TEXT, NOT NULL)
- venue_city (VARCHAR(100), NOT NULL)
- venue_capacity (INT, NULL)
- facilities (TEXT, NULL)
- created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)

**sessions table**:
- session_id (VARCHAR(128), PRIMARY KEY)
- user_id (INT, FOREIGN KEY REFERENCES users(user_id))
- session_token (VARCHAR(255), UNIQUE, NOT NULL)
- ip_address (VARCHAR(45), NOT NULL)
- user_agent (VARCHAR(500), NOT NULL)
- created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
- expires_at (TIMESTAMP, NOT NULL)
- last_activity (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)

**password_resets table**:
- reset_id (INT, PRIMARY KEY, AUTO_INCREMENT)
- user_id (INT, FOREIGN KEY REFERENCES users(user_id))
- reset_token (VARCHAR(255), UNIQUE, NOT NULL)
- created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
- expires_at (TIMESTAMP, NOT NULL)
- used (BOOLEAN, DEFAULT FALSE)

**activity_logs table**:
- log_id (INT, PRIMARY KEY, AUTO_INCREMENT)
- user_id (INT, FOREIGN KEY REFERENCES users(user_id))
- activity_type (VARCHAR(100), NOT NULL)
- activity_description (TEXT, NULL)
- ip_address (VARCHAR(45), NOT NULL)
- created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)

## Components and Interfaces

### User Class (classes/user_class.php)

Extends db_class and provides data access methods:

```php
class User extends db_class {
    // Registration
    public function create_user($email, $password_hash, $first_name, $last_name, $phone, $role)
    public function email_exists($email)
    
    // Authentication
    public function get_user_by_email($email)
    public function verify_password($user_id, $password)
    public function update_last_login($user_id)
    public function increment_failed_login($user_id)
    public function reset_failed_login($user_id)
    public function lock_account($user_id, $duration_minutes)
    public function is_account_locked($user_id)
    
    // Profile Management
    public function get_user_profile($user_id)
    public function update_user_profile($user_id, $data)
    public function update_email($user_id, $new_email)
    public function update_password($user_id, $new_password_hash)
    
    // Email Verification
    public function mark_email_verified($user_id)
    public function is_email_verified($user_id)
    
    // Admin Functions
    public function get_all_users($filters = [])
    public function suspend_user($user_id)
    public function reactivate_user($user_id)
    public function delete_user($user_id)
    public function change_user_role($user_id, $new_role)
    
    // Venue Owner Functions
    public function create_venue_details($user_id, $venue_data)
    public function get_venue_details($user_id)
    public function update_venue_details($user_id, $venue_data)
}
```

### Session Class (classes/session_class.php)

Manages secure session operations:

```php
class Session extends db_class {
    public function create_session($user_id, $ip_address, $user_agent)
    public function validate_session($session_token)
    public function get_session_user($session_token)
    public function update_session_activity($session_token)
    public function destroy_session($session_token)
    public function destroy_all_user_sessions($user_id)
    public function cleanup_expired_sessions()
    public function generate_secure_token()
}
```

### PasswordReset Class (classes/password_reset_class.php)

Handles password reset functionality:

```php
class PasswordReset extends db_class {
    public function create_reset_token($user_id)
    public function validate_reset_token($token)
    public function mark_token_used($token)
    public function cleanup_expired_tokens()
}
```

### UserController (controllers/user_controller.php)

Handles business logic and validation:

```php
class UserController {
    // Registration
    public function register_user($form_data)
    public function validate_registration_data($data)
    public function send_verification_email($user_id, $email)
    
    // Authentication
    public function login_user($email, $password)
    public function logout_user($session_token)
    public function check_authentication()
    public function require_role($allowed_roles)
    
    // Profile Management
    public function get_user_dashboard_data($user_id)
    public function update_profile($user_id, $profile_data)
    public function change_password($user_id, $current_password, $new_password)
    public function change_email($user_id, $new_email)
    
    // Password Reset
    public function request_password_reset($email)
    public function reset_password($token, $new_password)
    
    // Validation Helpers
    private function validate_email($email)
    private function validate_password_strength($password)
    private function validate_phone_number($phone)
    private function sanitize_input($data)
}
```

### AdminController (controllers/admin_controller.php)

Handles administrative operations:

```php
class AdminController extends UserController {
    public function get_all_users($page, $filters)
    public function suspend_user_account($user_id, $reason)
    public function reactivate_user_account($user_id)
    public function delete_user_account($user_id)
    public function change_user_role($user_id, $new_role)
    public function get_user_activity_log($user_id)
}
```

## Data Models

### User Model

```php
[
    'user_id' => int,
    'email' => string,
    'first_name' => string,
    'last_name' => string,
    'phone_number' => string,
    'role' => 'player'|'organizer'|'venue_owner'|'admin',
    'email_verified' => bool,
    'account_status' => 'active'|'suspended'|'deleted',
    'created_at' => datetime,
    'last_login' => datetime|null
]
```

### Profile Model

```php
[
    'profile_id' => int,
    'user_id' => int,
    'bio' => string|null,
    'profile_image_url' => string|null,
    'location' => string|null,
    'date_of_birth' => date|null,
    'updated_at' => datetime
]
```

### Venue Details Model

```php
[
    'venue_id' => int,
    'user_id' => int,
    'venue_name' => string,
    'venue_address' => string,
    'venue_city' => string,
    'venue_capacity' => int|null,
    'facilities' => string|null,
    'created_at' => datetime
]
```

### Session Model

```php
[
    'session_id' => string,
    'user_id' => int,
    'session_token' => string,
    'ip_address' => string,
    'user_agent' => string,
    'created_at' => datetime,
    'expires_at' => datetime,
    'last_activity' => datetime
]
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system—essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*


### Property 1: Valid registration creates account with correct role
*For any* valid user registration data (email, password, name, phone, role), submitting the registration should create a new user account with the specified role assigned correctly.
**Validates: Requirements 1.1**

### Property 2: Password validation enforces security requirements
*For any* password string, the validation function should reject passwords that don't meet the requirements (minimum 8 characters, one uppercase, one lowercase, one number) and accept passwords that do meet all requirements.
**Validates: Requirements 1.3**

### Property 3: Verification email sent on registration
*For any* successful user registration, the system should trigger a verification email to be sent to the registered email address.
**Validates: Requirements 1.4**

### Property 4: Venue owner registration requires venue details
*For any* registration attempt with the venue_owner role, the system should reject the registration if venue name and location are missing, and accept it when these fields are provided.
**Validates: Requirements 1.5**

### Property 5: Valid credentials create authenticated session
*For any* registered user, authenticating with their correct email and password should create a valid session token that can be used to access protected resources.
**Validates: Requirements 2.1**

### Property 6: Invalid credentials are rejected with generic error
*For any* login attempt with incorrect credentials (wrong password or non-existent email), the system should reject the attempt and return a generic error message that doesn't reveal which credential was incorrect.
**Validates: Requirements 2.2**

### Property 7: Role determines dashboard redirect
*For any* authenticated user, the dashboard redirect URL should correspond to their assigned role (player, organizer, venue_owner, or admin).
**Validates: Requirements 2.3**

### Property 8: Multiple sessions per user are supported
*For any* user, authenticating from different devices (different user agents) should create separate valid sessions that can coexist.
**Validates: Requirements 2.5**

### Property 9: Profile updates are validated and persisted
*For any* authenticated user and valid profile data, updating the profile should validate the data according to field-specific rules (email uniqueness, phone format, password verification) and persist the changes to the database.
**Validates: Requirements 3.1, 3.3, 3.5**

### Property 10: Venue owners can update venue-specific fields
*For any* user with the venue_owner role, the system should allow updates to venue details (name, location, facilities), and for users with other roles, the system should not provide access to venue detail updates.
**Validates: Requirements 3.4**

### Property 11: Password reset round-trip
*For any* registered user, requesting a password reset should generate a valid token, and using that token to set a new password should allow authentication with the new password while invalidating the old password.
**Validates: Requirements 4.1, 4.2**

### Property 12: Password reset invalidates existing sessions
*For any* user with active sessions, successfully resetting their password should invalidate all existing session tokens for that user.
**Validates: Requirements 4.4**

### Property 13: Account suspension prevents authentication
*For any* user account, when an administrator suspends the account, the user should be unable to authenticate and any active sessions should be terminated. When the administrator reactivates the account, the user should be able to authenticate again.
**Validates: Requirements 5.2, 5.3**

### Property 14: Role changes update permissions immediately
*For any* user, when an administrator changes their role, subsequent authentication and authorization checks should reflect the new role's permissions.
**Validates: Requirements 5.4**

### Property 15: Account deletion is soft delete
*For any* user account, when an administrator deletes it, the account should be marked as deleted (preventing authentication) but the user's data should remain in the database for audit purposes.
**Validates: Requirements 5.5**

### Property 16: Passwords are hashed, never stored plain text
*For any* user registration or password change, the password stored in the database should be a hash that is not equal to the plain text password and should verify correctly using the password verification function.
**Validates: Requirements 6.1**

### Property 17: Session tokens are cryptographically secure
*For any* session creation, the generated session token should be unique, sufficiently long (at least 32 characters), and appear random (high entropy).
**Validates: Requirements 6.2**

### Property 18: Logout invalidates session immediately
*For any* active session, calling the logout function should invalidate the session token such that subsequent requests using that token are rejected.
**Validates: Requirements 6.3**

### Property 19: Profile displays account metadata
*For any* user viewing their profile, the displayed data should include their account creation date and last login timestamp.
**Validates: Requirements 7.4**

### Property 20: Orientation changes preserve application state
*For any* user with form data entered or an active session, simulating a device orientation change should preserve both the form data and the session state.
**Validates: Requirements 8.4**

## Error Handling

### Validation Errors

The system implements comprehensive input validation with user-friendly error messages:

- **Email validation**: Check format using filter_var() with FILTER_VALIDATE_EMAIL, verify uniqueness against database
- **Password validation**: Enforce minimum length, character requirements, provide specific feedback on which requirements are not met
- **Phone validation**: Check format using regex pattern, support international formats
- **Required fields**: Validate all required fields are present and non-empty before processing

Error responses should include:
- HTTP status code (400 for validation errors, 401 for authentication errors, 403 for authorization errors)
- User-friendly error message
- Field-specific errors for form validation

### Database Errors

- Use try-catch blocks around all database operations
- Log detailed error information for debugging (without exposing to users)
- Return generic error messages to users ("An error occurred. Please try again.")
- Implement transaction rollback for multi-step operations
- Handle connection failures gracefully with retry logic

### Authentication Errors

- Failed login: Generic message "Invalid email or password"
- Account locked: Specific message with unlock time
- Session expired: Redirect to login with message "Your session has expired. Please log in again."
- Insufficient permissions: "You do not have permission to access this resource"

### Email Sending Errors

- Log email failures for administrator review
- Do not block user registration if verification email fails
- Provide option to resend verification email
- Implement retry logic with exponential backoff for transient failures

### Security Error Handling

- Never expose system internals in error messages
- Log security events (failed logins, account lockouts, suspicious activity)
- Implement rate limiting on authentication endpoints
- Use HTTPS for all authentication-related requests
- Sanitize all user inputs to prevent XSS and SQL injection

## Testing Strategy

The User Management & Authentication system will be tested using a dual approach combining unit tests and property-based tests to ensure comprehensive coverage.

### Unit Testing Approach

Unit tests will verify specific examples, edge cases, and integration points:

- **Registration edge cases**: Empty email, duplicate email, weak passwords
- **Authentication edge cases**: Non-existent user, wrong password, locked account
- **Session management**: Session creation, expiration, cleanup
- **Password reset flow**: Token generation, expiration, usage
- **Admin operations**: Specific suspension, reactivation, deletion scenarios
- **Email integration**: Verification email sending, password reset email sending

Unit tests will use PHPUnit as the testing framework and will mock database connections where appropriate to isolate business logic.

### Property-Based Testing Approach

Property-based tests will verify universal properties across many randomly generated inputs:

- **Testing framework**: We will use Eris (https://github.com/giorgiosironi/eris), a property-based testing library for PHP
- **Test configuration**: Each property test will run a minimum of 100 iterations with randomly generated data
- **Test tagging**: Each property-based test will include a comment explicitly referencing the correctness property from this design document using the format: `// Feature: user-management-auth, Property X: [property text]`
- **Generator strategy**: We will create custom generators for:
  - Valid/invalid emails
  - Valid/invalid passwords (meeting/violating security requirements)
  - User data with different roles
  - Session tokens
  - Profile update data
  
Property-based tests will focus on:
- Registration with various valid input combinations
- Password validation across many password patterns
- Authentication with different credential combinations
- Profile updates with various data types
- Role-based access control across all roles
- Security properties (hashing, token generation, session management)

### Test Organization

Tests will be organized in a `tests/` directory with subdirectories:
- `tests/unit/` - Unit tests for specific scenarios
- `tests/property/` - Property-based tests for universal properties
- `tests/integration/` - Integration tests for end-to-end flows

### Continuous Testing

- Run unit tests on every code change
- Run property-based tests before merging to main branch
- Monitor test coverage and aim for >80% code coverage
- Use automated testing in CI/CD pipeline

## Security Considerations

### Password Security

- Use PHP's password_hash() with PASSWORD_BCRYPT algorithm (cost factor 12)
- Never log or display passwords
- Enforce password complexity requirements
- Implement password change confirmation
- Invalidate all sessions on password change

### Session Security

- Generate cryptographically secure random tokens using random_bytes()
- Store session tokens hashed in database
- Set session expiration (24 hours of inactivity)
- Bind sessions to IP address and user agent (with flexibility for mobile networks)
- Implement session cleanup for expired sessions
- Use httpOnly and secure flags for session cookies

### Input Validation and Sanitization

- Validate all inputs on server side (never trust client)
- Use prepared statements for all database queries
- Sanitize output to prevent XSS attacks
- Implement CSRF tokens for state-changing operations
- Validate file uploads (if profile images are added)

### Rate Limiting and Account Protection

- Implement account lockout after 5 failed login attempts within 15 minutes
- Lock account for 30 minutes after threshold reached
- Log all failed authentication attempts
- Implement rate limiting on password reset requests
- Monitor for suspicious patterns (multiple accounts from same IP)

### Data Protection

- Use HTTPS for all communications
- Encrypt sensitive data at rest (consider PHP encryption libraries)
- Implement proper access controls (users can only access their own data)
- Log access to sensitive operations
- Implement data retention policies
- Provide data export functionality for GDPR compliance

### Email Security

- Use authenticated SMTP for sending emails
- Implement SPF, DKIM, and DMARC records
- Include unsubscribe options where applicable
- Rate limit email sending to prevent abuse
- Validate email addresses before sending

## Performance Considerations

### Database Optimization

- Index frequently queried columns (email, user_id, session_token)
- Use connection pooling
- Implement query caching for user profile data
- Optimize JOIN operations
- Regular database maintenance (analyze, optimize tables)

### Session Management

- Implement session cleanup cron job (run hourly)
- Use database sessions for scalability across multiple servers
- Consider Redis for session storage in high-traffic scenarios
- Implement lazy loading for user profile data

### Caching Strategy

- Cache user profile data with short TTL (5 minutes)
- Cache role permissions
- Invalidate cache on profile updates
- Use APCu or Redis for PHP opcode caching

### Response Time Targets

- Registration: < 2 seconds
- Login: < 1 second
- Profile load: < 3 seconds (as per requirements)
- Profile update: < 2 seconds
- Password reset request: < 2 seconds

## Mobile Responsiveness

### Design Principles

- Mobile-first approach using responsive CSS framework (Bootstrap 5)
- Touch-friendly interface with minimum 44x44px touch targets
- Optimized forms with appropriate input types (email, tel, password)
- Progressive enhancement for advanced features
- Minimize data transfer for mobile networks

### Responsive Breakpoints

- Mobile: < 768px
- Tablet: 768px - 1024px
- Desktop: > 1024px

### Mobile-Specific Features

- Simplified navigation for small screens
- Collapsible sections for long forms
- Auto-focus on primary input fields
- Clear error messages positioned near relevant fields
- Loading indicators for async operations

## Deployment Considerations

### Environment Configuration

- Separate configuration files for development, staging, production
- Environment variables for sensitive data (database credentials, API keys)
- Debug mode disabled in production
- Error logging to files (not displayed to users)

### Database Migration

- Create migration scripts for database schema
- Version control for schema changes
- Backup database before migrations
- Test migrations in staging environment

### Monitoring and Logging

- Log all authentication events
- Monitor failed login attempts
- Track session creation and destruction
- Alert on unusual patterns (spike in registrations, failed logins)
- Implement application performance monitoring (APM)

### Backup and Recovery

- Daily database backups
- Backup retention policy (30 days)
- Test backup restoration regularly
- Document recovery procedures
