# SecurePortal

![SecurePortal Logo](https://via.placeholder.com/150x50?text=SecurePortal)

## 🔐 Modern User Authentication & Dashboard System

SecurePortal is a clean, professional PHP authentication system featuring a modern UI, secure user registration, and a responsive dashboard. It provides a solid foundation for building web applications requiring user authentication and advanced user management features.

## 🌟 Features

- **Modern UI/UX Design**: Sleek interface with intuitive user experience.
- **Secure Authentication**: User login and registration with proper security measures.
- **Email Verification**: New users receive a verification link to activate their accounts.
- **Password Reset**: Secure password reset functionality via email token.
- **User Profile Management**: Users can view and update their username (email) and password.
- **Role-Based Access Control (RBAC)**: Basic admin role with access to an admin panel for user listing.
- **Two-Factor Authentication (Conceptual)**: Users can enable 2FA for an extra layer of security (current implementation is conceptual, using mock validation).
- **Dark Mode Toggle**: Dashboard interface supports a user-selectable dark mode, with preference saved.
- **Responsive Dashboard**: Professional admin panel that works on all device sizes.
- **Database Integration**: Secure MySQL connection with prepared statements.
- **Form Validation**: Client and server-side validation for data integrity.
- **Session Management**: Secure handling of user sessions.

## 🚀 Getting Started

### Prerequisites

- PHP 7.4 or higher (ensure `php-mysql` and `php-cli` extensions are enabled/installed)
- MySQL 5.7 or higher
- Web server (Apache, Nginx, etc.)

### Installation

1.  Clone the repository:
    ```bash
    git clone https://github.com/yourusername/secure-portal.git
    ```
    (Replace `yourusername/secure-portal` with the actual repository URL)

2.  **Database Setup:**
    *   Ensure your MySQL server is running.
    *   Create a database (e.g., `signupforms`).
    *   Update `connect.php` with your database credentials (hostname, username, password, database name).
    *   Run the setup scripts in the specified order to initialize the database schema. These scripts create necessary tables and columns:
        *   `php setup_password_reset.php` (creates `registration` and `password_resets` tables)
        *   `php setup_email_verification.php` (adds `is_verified` to `registration`, creates `email_verifications` table)
        *   `php setup_rbac.php` (adds `role` to `registration` table)
        *   `php setup_2fa.php` (adds `2fa_secret` and `2fa_enabled` to `registration` table)
    *   *Note: These setup scripts are designed to be idempotent (safe to run multiple times).*

3.  Start your web server and navigate to the project URL.

4.  **(Optional - For Testing RBAC)** To make a user an admin:
    Use the `make_admin.php` script (protect this script in production):
    `make_admin.php?token=YOUR_SECRET_TOKEN&username=user@example.com`
    (Replace `YOUR_SECRET_TOKEN` with the one in the script, and `user@example.com` with the target user's email).
    The user needs to log out and log back in for the role change to take effect in their session.

## 📂 Project Structure

```
secure-portal/
├── connect.php                 # Database connection handler
├── index.php                   # Main entry point with login form
├── signup.php                  # User registration page
├── home.php                    # Dashboard for authenticated users
├── logout.php                  # Handles session termination
├── profile.php                 # User profile management (username/password updates, 2FA setup)
├── forgot_password.php         # Form to request password reset
├── reset_password.php          # Form to set new password using token
├── verify_email.php            # Handles email verification via token
├── admin_panel.php             # Admin panel for user listing (requires admin role)
├── verify_2fa.php              # Page for entering 2FA code during login
├── setup_password_reset.php    # Utility: Creates/updates tables for users and password resets
├── setup_email_verification.php # Utility: Adds email verification fields/tables
├── setup_rbac.php              # Utility: Adds role field for RBAC
├── setup_2fa.php               # Utility: Adds 2FA fields
├── make_admin.php              # Utility: Promotes a user to admin (for testing)
└── documentation.txt           # Detailed project documentation
```

## 💾 Database Schema Highlights

-   **`registration` table:**
    -   `id` (PK, AI)
    -   `username` (VARCHAR, UNIQUE) - Also used as user's email.
    -   `password` (VARCHAR) - *Note: Currently stored as plain text.*
    -   `is_verified` (TINYINT, default 0) - For email verification status.
    -   `role` (VARCHAR, default 'user') - For RBAC (e.g., 'user', 'admin').
    -   `2fa_secret` (VARCHAR, nullable) - Stores the secret key for 2FA.
    -   `2fa_enabled` (TINYINT, default 0) - Indicates if 2FA is active for the user.
-   **`password_resets` table:** Stores tokens for password reset requests (`id`, `email`, `token`, `expires`).
-   **`email_verifications` table:** Stores tokens for email verification (`id`, `user_id`, `token`, `expires`).

## 🔧 Technical Details

- **PHP**: Backend scripting
- **MySQL**: Database management
- **Bootstrap 5**: Frontend framework 
- **Font Awesome**: Icon library
- **JavaScript**: Client-side functionality (e.g., Dark Mode toggle, sidebar behavior)
- **LocalStorage**: Used for persisting Dark Mode preference.

## 🔒 Security Features

- Prepared statements to prevent SQL injection.
- **Email Verification**: Ensures users own the email they sign up with.
- **Password Reset via Email Token**: Secure way for users to recover account access.
- **Conceptual Two-Factor Authentication (2FA)**: Framework for adding an extra security layer during login.
- Session protection mechanisms.
- Input sanitization (e.g., `htmlspecialchars`).
- *Note: Password hashing is not yet implemented; passwords are currently stored in plain text. This is a critical security improvement needed.*

## 🖼️ Screenshots

*(Screenshots would ideally be updated to reflect new features like Profile page, Admin Panel, 2FA setup, Dark Mode)*

![Login Page](https://via.placeholder.com/800x400?text=Login+Page)
![Dashboard (Light Mode)](https://via.placeholder.com/800x400?text=Dashboard+Light+Mode)
![Dashboard (Dark Mode)](https://via.placeholder.com/800x400?text=Dashboard+Dark+Mode)
![Signup Page](https://via.placeholder.com/800x400?text=Signup+Page)
![Profile Page](https://via.placeholder.com/800x400?text=Profile+Page+with+2FA)
![Admin Panel](https://via.placeholder.com/800x400?text=Admin+Panel)


## 🔜 Future Enhancements

- **Implement Proper Password Hashing**: Critical security update (e.g., using `password_hash()` and `password_verify()`).
- **Full TOTP 2FA Implementation**: Replace mock 2FA validation with a standard TOTP library (e.g., generating QR codes, validating codes).
- **User Interface for Role Management**: Allow admins to change user roles via the admin panel.
- **Activity Logging**: Track important user actions.
- **Enhanced Form Validation**: More robust client-side and server-side validation.
- **CSRF Protection**: Implement CSRF tokens for all sensitive form submissions.
- **Refine UI/UX**: Further improvements to user experience and design consistency.
- **Consolidate Setup Scripts**: Create a single master script to handle all database initializations.
- **Code Cleanup**: Address any duplicate HTML/code found in files like `signup.php` and `home.php` (resulting from tool limitations during development).

## 📜 License

This project is licensed under the MIT License - see the `LICENSE` file for details (assuming one exists).

## 📞 Contact

Your Name - [your.email@example.com](mailto:lepatioborel@gmail.com)
Project Link: [https://github.com/yourusername/secure-portal](https://github.com/yourusername/secure-portal)
