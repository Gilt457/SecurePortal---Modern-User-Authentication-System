<?php
include 'connect.php'; // Ensure this path is correct

$error_messages = [];
$success_messages = [];

// Check if 'role' column exists in 'registration' table
$check_column_sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'registration' AND COLUMN_NAME = 'role'";
$result = $conn->query($check_column_sql);

if ($result === FALSE) {
    $error_messages[] = "Error checking for 'role' column: " . $conn->error;
} elseif ($result->num_rows == 0) {
    // Column does not exist, so add it
    $sql_alter_registration = "ALTER TABLE registration ADD COLUMN role VARCHAR(50) DEFAULT 'user'";
    if ($conn->query($sql_alter_registration) === TRUE) {
        $success_messages[] = "Column 'role' added to 'registration' table successfully with default 'user'.";
    } else {
        $error_messages[] = "Error adding 'role' column to 'registration' table: " . $conn->error;
    }
} else {
    // Column already exists
    $success_messages[] = "Column 'role' already exists in 'registration' table.";
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
    echo "<p>Database setup for RBAC completed successfully.</p>";
} else {
    echo "<p>There were errors during RBAC database setup. Please check messages above.</p>";
}
?>
