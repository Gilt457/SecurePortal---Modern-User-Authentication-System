<?php
include 'connect.php';

// SQL to create registration table
$sql_registration = "CREATE TABLE IF NOT EXISTS registration (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
)";

if ($conn->query($sql_registration) === TRUE) {
    echo "Table 'registration' created successfully or already exists.<br>";
} else {
    echo "Error creating table 'registration': " . $conn->error . "<br>";
}

// SQL to create password_resets table
$sql_password_resets = "CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    expires BIGINT NOT NULL
)";

if ($conn->query($sql_password_resets) === TRUE) {
    echo "Table 'password_resets' created successfully or already exists.<br>";
} else {
    echo "Error creating table 'password_resets': " . $conn->error . "<br>";
}

$conn->close();
?>
