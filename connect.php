<?php
// Database connection parameters
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'signupforms';

// Create connection with error handling
try {
    // Create connection
    $conn = mysqli_connect($hostname, $username, $password, $database);
    
    // Check connection
    if (!$conn) {
        throw new Exception("Connection failed: " . mysqli_connect_error());
    }
    
    // Set character set to UTF-8
    mysqli_set_charset($conn, "utf8mb4");
} catch (Exception $e) {
    // Log the error (in a production environment, consider using a proper logging system)
    error_log("Database connection error: " . $e->getMessage());
    
    // Display user-friendly message
    die("We're experiencing technical difficulties. Please try again later.");
}
?>