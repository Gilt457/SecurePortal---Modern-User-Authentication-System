<?php
include 'connect.php';

$error_messages = [];
$success_messages = [];

// Check if 'is_verified' column exists
$check_column_sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'registration' AND COLUMN_NAME = 'is_verified'";
$result = $conn->query($check_column_sql);

if ($result === FALSE) {
    $error_messages[] = "Error checking for 'is_verified' column: " . $conn->error;
} elseif ($result->num_rows == 0) {
    // Column does not exist, so add it
    $sql_alter_registration = "ALTER TABLE registration ADD COLUMN is_verified TINYINT(1) DEFAULT 0";
    if ($conn->query($sql_alter_registration) === TRUE) {
        $success_messages[] = "Column 'is_verified' added to 'registration' table successfully.";
    } else {
        $error_messages[] = "Error adding 'is_verified' column to 'registration' table: " . $conn->error;
    }
} else {
    // Column already exists
    $success_messages[] = "Column 'is_verified' already exists in 'registration' table.";
}

// SQL to CREATE email_verifications table
$sql_create_email_verifications = "CREATE TABLE IF NOT EXISTS email_verifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    expires BIGINT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES registration(id) ON DELETE CASCADE
)";

if ($conn->query($sql_create_email_verifications) === TRUE) {
    $success_messages[] = "Table 'email_verifications' created successfully or already exists.";
} else {
    $error_messages[] = "Error creating 'email_verifications' table: " . $conn->error;
}

$conn->close();

// Display messages
if (!empty($success_messages)) {
    foreach ($success_messages as $msg) {
        echo "<p style='color: green;'>" . htmlspecialchars($msg) . "</p>";
    }
}
if (!empty($error_messages)) {
    foreach ($error_messages as $msg) {
        echo "<p style='color: red;'>" . htmlspecialchars($msg) . "</p>";
    }
}

if (empty($error_messages)) {
    echo "<p>Database setup for email verification completed successfully.</p>";
} else {
    echo "<p>There were errors during database setup for email verification. Please check messages above.</p>";
}

?>
