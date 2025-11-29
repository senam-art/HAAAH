# Implementation Plan

- [-] 1. Set up database schema and core infrastructure
  - Create database migration script for all required tables (users, user_profiles, venue_details, sessions, password_resets, activity_logs)
  - Add indexes on frequently queried columns (email, user_id, session_token)
  - Configure database connection settings in settings/db_cred.php
  - _Requirements: 1.1, 2.1, 3.1, 4.1, 5.1, 6.1_

- [ ] 2. Implement User class for data access
  - [ ] 2.1 Create User class extending db_class with registration methods
    - Implement create_user() method with prepared statements
    - Implement email_exists() method for duplicate checking
    - Implement get_user_by_email() method for authentication
    - _Requirements: 1.1, 1.2_

  - [ ] 2.2 Write property test for user registration
    - **Property 1: Valid registration creates account with correct role**
    - **Validates: Requirements 1.1**

  - [ ] 2.3 Add authentication-related methods to User class
    - Implement verify_password() method
    - Implement update_last_login() method
    - Implement failed login tracking methods (increment_failed_login, reset_failed_login, lock_account, is_account_locked)
    - _Requirements: 2.1, 2.2, 6.4_

  - [ ] 2.4 Write property test for invalid credentials rejection
    - **Property 6: Invalid credentials are rejected with generic error**
    - **Validates: Requirements 2.2**

  - [ ] 2.5 Add profile management methods to User class
    - Implement get_user_profile() method
    - Implement update_user_profile() method
    - Implement update_email() and update_password() methods
    - _Requirements: 3.1, 3.2, 3.3_

  - [ ] 2.6 Write property test for profile updates
    - **Property 9: Profile updates are validated and persisted**
    - **Validates: Requirements 3.1, 3.3, 3.5**

  - [ ] 2.7 Add venue-specific methods to User class
    - Implement create_venue_details() method
    - Implement get_venue_details() method
    - Implement update_venue_details() method
    - _Requirements: 1.5, 3.4_

  - [ ] 2.8 Write property test for venue owner functionality
    - **Property 4: Venue owner registration requires venue details**
    - **Property 10: Venue owners can update venue-specific fields**
    - **Validates: Requirements 1.5, 3.4**

  - [ ] 2.9 Add admin methods to User class
    - Implement get_all_users() method with filtering
    - Implement suspend_user(), reactivate_user(), delete_user() methods
    - Implement change_user_role() method
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5_

  - [ ] 2.10 Write property tests for admin operations
    - **Property 13: Account suspension prevents authentication**
    - **Property 14: Role changes update permissions immediately**
    - **Property 15: Account deletion is soft delete**
    - **Validates: Requirements 5.2, 5.3, 5.4, 5.5**

- [ ] 3. Implement Session class for session management
  - [ ] 3.1 Create Session class with core session methods
    - Implement create_session() method with secure token generation
    - Implement validate_session() method
    - Implement get_session_user() method
    - Implement update_session_activity() method
    - Implement destroy_session() and destroy_all_user_sessions() methods
    - Implement cleanup_expired_sessions() method
    - _Requirements: 2.1, 2.4, 6.2, 6.3_

  - [ ] 3.2 Write property tests for session management
    - **Property 5: Valid credentials create authenticated session**
    - **Property 8: Multiple sessions per user are supported**
    - **Property 17: Session tokens are cryptographically secure**
    - **Property 18: Logout invalidates session immediately**
    - **Validates: Requirements 2.1, 2.5, 6.2, 6.3**

- [ ] 4. Implement PasswordReset class for password recovery
  - [ ] 4.1 Create PasswordReset class with reset methods
    - Implement create_reset_token() method
    - Implement validate_reset_token() method
    - Implement mark_token_used() method
    - Implement cleanup_expired_tokens() method
    - _Requirements: 4.1, 4.2, 4.3_

  - [ ] 4.2 Write property tests for password reset
    - **Property 11: Password reset round-trip**
    - **Property 12: Password reset invalidates existing sessions**
    - **Validates: Requirements 4.1, 4.2, 4.4**

- [ ] 5. Implement UserController for business logic
  - [ ] 5.1 Create UserController with validation helpers
    - Implement validate_email() method using filter_var()
    - Implement validate_password_strength() method
    - Implement validate_phone_number() method
    - Implement sanitize_input() method
    - _Requirements: 1.2, 1.3, 3.5_

  - [ ] 5.2 Write property test for password validation
    - **Property 2: Password validation enforces security requirements**
    - **Validates: Requirements 1.3**

  - [ ] 5.3 Implement registration functionality in UserController
    - Implement register_user() method with validation
    - Implement validate_registration_data() method
    - Integrate with User class for account creation
    - _Requirements: 1.1, 1.2, 1.3, 1.5_

  - [ ] 5.4 Implement email verification functionality
    - Implement send_verification_email() method using PHPMailer
    - Create email templates for verification
    - _Requirements: 1.4_

  - [ ] 5.5 Write property test for verification email
    - **Property 3: Verification email sent on registration**
    - **Validates: Requirements 1.4**

  - [ ] 5.6 Implement authentication functionality in UserController
    - Implement login_user() method with credential validation
    - Implement logout_user() method
    - Implement check_authentication() middleware
    - Implement require_role() authorization method
    - _Requirements: 2.1, 2.2, 2.3, 6.3_

  - [ ] 5.7 Write property test for role-based redirect
    - **Property 7: Role determines dashboard redirect**
    - **Validates: Requirements 2.3**

  - [ ] 5.8 Implement profile management in UserController
    - Implement get_user_dashboard_data() method
    - Implement update_profile() method with validation
    - Implement change_password() method with current password verification
    - Implement change_email() method with uniqueness check
    - _Requirements: 3.1, 3.2, 3.3, 7.4_

  - [ ] 5.9 Write property test for profile metadata display
    - **Property 19: Profile displays account metadata**
    - **Validates: Requirements 7.4**

  - [ ] 5.10 Implement password reset in UserController
    - Implement request_password_reset() method
    - Implement reset_password() method
    - Create password reset email templates
    - _Requirements: 4.1, 4.2, 4.5_

- [ ] 6. Implement AdminController for administrative operations
  - [ ] 6.1 Create AdminController extending UserController
    - Implement get_all_users() with pagination and filtering
    - Implement suspend_user_account() method
    - Implement reactivate_user_account() method
    - Implement delete_user_account() method
    - Implement change_user_role() method
    - Implement get_user_activity_log() method
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5_

- [ ] 7. Implement security features
  - [ ] 7.1 Implement password hashing and verification
    - Use password_hash() with PASSWORD_BCRYPT in registration and password changes
    - Use password_verify() in authentication
    - _Requirements: 6.1_

  - [ ] 7.2 Write property test for password hashing
    - **Property 16: Passwords are hashed, never stored plain text**
    - **Validates: Requirements 6.1**

  - [ ] 7.3 Implement account lockout mechanism
    - Add failed login tracking in login flow
    - Implement automatic account locking after 5 failed attempts
    - Implement time-based unlock (30 minutes)
    - _Requirements: 6.4_

  - [ ] 7.4 Implement CSRF protection
    - Create CSRF token generation and validation functions
    - Add CSRF tokens to all state-changing forms
    - _Requirements: 6.5_

- [ ] 8. Create view files for user interface
  - [ ] 8.1 Create registration view
    - Create view/register.php with role selection
    - Add conditional venue fields for venue owners
    - Implement client-side validation
    - Make form mobile-responsive
    - _Requirements: 1.1, 1.5, 8.1, 8.5_

  - [ ] 8.2 Create login view
    - Create view/login.php with email and password fields
    - Add "Forgot Password" link
    - Display error messages appropriately
    - Make form mobile-responsive
    - _Requirements: 2.1, 2.2, 8.1, 8.5_

  - [ ] 8.3 Create dashboard views for each role
    - Create view/player_dashboard.php
    - Create view/organizer_dashboard.php
    - Create view/venue_owner_dashboard.php
    - Create view/admin_dashboard.php
    - Implement mobile-responsive layouts
    - _Requirements: 2.3, 7.1, 7.2, 7.3, 8.2, 8.3_

  - [ ] 8.4 Create profile management view
    - Create view/profile.php with editable fields
    - Add password change form
    - Add email change form
    - Display account metadata (creation date, last login)
    - Make form mobile-responsive with proper touch targets
    - _Requirements: 3.1, 3.2, 3.3, 7.4, 8.2_

  - [ ] 8.5 Write property test for state preservation
    - **Property 20: Orientation changes preserve application state**
    - **Validates: Requirements 8.4**

  - [ ] 8.6 Create password reset views
    - Create view/forgot_password.php for email submission
    - Create view/reset_password.php for new password entry
    - Make forms mobile-responsive
    - _Requirements: 4.1, 4.2, 8.1, 8.5_

  - [ ] 8.7 Create admin user management view
    - Create view/admin_users.php with user list and filters
    - Add action buttons for suspend, reactivate, delete, change role
    - Implement pagination
    - Make interface mobile-responsive
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5_

- [ ] 9. Create action files for request handling
  - [ ] 9.1 Create registration action
    - Create actions/register_action.php
    - Handle POST request with form data
    - Call UserController->register_user()
    - Redirect to appropriate page with success/error messages
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_

  - [ ] 9.2 Create login action
    - Create actions/login_action.php
    - Handle POST request with credentials
    - Call UserController->login_user()
    - Set session cookie and redirect to role-appropriate dashboard
    - _Requirements: 2.1, 2.2, 2.3_

  - [ ] 9.3 Create logout action
    - Create actions/logout_action.php
    - Call UserController->logout_user()
    - Clear session cookie and redirect to login
    - _Requirements: 6.3_

  - [ ] 9.4 Create profile update action
    - Create actions/update_profile_action.php
    - Handle POST request with profile data
    - Call UserController->update_profile()
    - Return success/error response
    - _Requirements: 3.1, 3.5_

  - [ ] 9.5 Create password change action
    - Create actions/change_password_action.php
    - Handle POST request with current and new passwords
    - Call UserController->change_password()
    - Return success/error response
    - _Requirements: 3.3_

  - [ ] 9.6 Create email change action
    - Create actions/change_email_action.php
    - Handle POST request with new email
    - Call UserController->change_email()
    - Return success/error response
    - _Requirements: 3.2_

  - [ ] 9.7 Create password reset actions
    - Create actions/request_reset_action.php for reset request
    - Create actions/reset_password_action.php for password reset
    - _Requirements: 4.1, 4.2, 4.5_

  - [ ] 9.8 Create admin actions
    - Create actions/admin_suspend_user_action.php
    - Create actions/admin_reactivate_user_action.php
    - Create actions/admin_delete_user_action.php
    - Create actions/admin_change_role_action.php
    - _Requirements: 5.2, 5.3, 5.4, 5.5_

- [ ] 10. Implement authentication middleware and helpers
  - [ ] 10.1 Create authentication middleware
    - Create functions/auth_middleware.php
    - Implement check_logged_in() function
    - Implement check_role() function
    - Implement get_current_user() function
    - _Requirements: 2.1, 2.3_

  - [ ] 10.2 Create session cleanup cron job
    - Create functions/cleanup_sessions.php
    - Call Session->cleanup_expired_sessions()
    - Document cron job setup (run hourly)
    - _Requirements: 2.4_

  - [ ] 10.3 Create password reset cleanup cron job
    - Create functions/cleanup_password_resets.php
    - Call PasswordReset->cleanup_expired_tokens()
    - Document cron job setup (run daily)
    - _Requirements: 4.3_

- [ ] 11. Add CSS styling and mobile responsiveness
  - [ ] 11.1 Create main stylesheet
    - Create css/auth.css with styles for all auth-related pages
    - Implement mobile-first responsive design
    - Ensure minimum 44x44px touch targets for mobile
    - Test on multiple screen sizes
    - _Requirements: 8.1, 8.2, 8.3, 8.5_

  - [ ] 11.2 Add JavaScript for client-side enhancements
    - Create js/auth.js for form validation
    - Add password strength indicator
    - Add show/hide password toggle
    - Implement form state preservation on orientation change
    - _Requirements: 1.3, 8.4_

- [ ] 12. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 13. Create email templates
  - [ ] 13.1 Create verification email template
    - Design HTML email template for email verification
    - Include verification link with token
    - Test email rendering across email clients
    - _Requirements: 1.4_

  - [ ] 13.2 Create password reset email template
    - Design HTML email template for password reset
    - Include reset link with token
    - Add expiration notice (1 hour)
    - Test email rendering across email clients
    - _Requirements: 4.1_

- [ ] 14. Implement error handling and logging
  - [ ] 14.1 Create error handling utilities
    - Create functions/error_handler.php
    - Implement log_error() function
    - Implement log_security_event() function
    - Configure error logging to error/php-error.log
    - _Requirements: 6.4_

  - [ ] 14.2 Add error handling to all controllers
    - Wrap database operations in try-catch blocks
    - Log errors appropriately
    - Return user-friendly error messages
    - _Requirements: All_

- [ ] 15. Setup property-based testing framework
  - [ ] 15.1 Install and configure Eris
    - Add Eris to project dependencies
    - Create tests/property/ directory
    - Configure PHPUnit for property tests
    - Create custom generators for user data, emails, passwords
    - _Requirements: All testable properties_

- [ ] 16. Final checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 17. Create documentation
  - [ ] 17.1 Create API documentation
    - Document all controller methods
    - Document all class methods
    - Include usage examples
    - _Requirements: All_

  - [ ] 17.2 Create deployment guide
    - Document database setup steps
    - Document environment configuration
    - Document cron job setup
    - Document security checklist
    - _Requirements: All_
