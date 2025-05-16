# SecurePortal

![SecurePortal Logo](https://via.placeholder.com/150x50?text=SecurePortal)

## 🔐 Modern User Authentication & Dashboard System

SecurePortal is a clean, professional PHP authentication system featuring a modern UI, secure user registration, and a responsive dashboard. It provides a solid foundation for building web applications requiring user authentication.

## 🌟 Features

- **Modern UI/UX Design**: Sleek interface with intuitive user experience
- **Secure Authentication**: User login and registration with proper security measures
- **Responsive Dashboard**: Professional admin panel that works on all device sizes
- **Database Integration**: Secure MySQL connection with prepared statements
- **Form Validation**: Client and server-side validation for data integrity
- **Session Management**: Secure handling of user sessions

## 🚀 Getting Started

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache, Nginx, etc.)

### Installation

1. Clone the repository:
   ```
   git clone https://github.com/yourusername/secure-portal.git
   ```

2. Import the database schema:
   ```
   mysql -u username -p signupforms < database/schema.sql
   ```

3. Configure database connection:
   Update `connect.php` with your database credentials if needed.

4. Start your web server and navigate to the project URL.

## 📂 Project Structure

```
secure-portal/
├── connect.php         # Database connection handler
├── index.php           # Main entry point with login form
├── signup.php          # User registration page
├── home.php            # Dashboard for authenticated users
├── logout.php          # Handles session termination
└── documentation.txt   # Project documentation
```

## 🔧 Technical Details

- **PHP**: Backend scripting
- **MySQL**: Database management
- **Bootstrap 5**: Frontend framework 
- **Font Awesome**: Icon library
- **JavaScript**: Client-side functionality

## 🔒 Security Features

- Prepared statements to prevent SQL injection
- Password hashing for secure storage
- Session protection mechanisms
- CSRF protection for forms
- Input sanitization

## 🖼️ Screenshots

![Login Page](https://via.placeholder.com/800x400?text=Login+Page)
![Dashboard](https://via.placeholder.com/800x400?text=Dashboard)
![Signup Page](https://via.placeholder.com/800x400?text=Signup+Page)

## 🔜 Future Enhancements

- Password reset functionality
- Email verification
- Two-factor authentication
- User profile management
- Role-based access control
- Dark mode toggle

## 📜 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 📞 Contact

Your Name - [your.email@example.com](mailto:lepatioborel@gmail.com)

Project Link: [https://github.com/yourusername/secure-portal](https://github.com/yourusername/secure-portal)
