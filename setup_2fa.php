<?php
include 'connect.php'; // Ensure this path is correct

$error_messages = [];
$success_messages = [];

// --- Add '2fa_secret' column ---
$check_column_2fa_secret_sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'registration' AND COLUMN_NAME = '2fa_secret'";
$result_2fa_secret = $conn->query($check_column_2fa_secret_sql);

if ($result_2fa_secret === FALSE) {
    $error_messages[] = "Error checking for '2fa_secret' column: " . $conn->error;
} elseif ($result_2fa_secret->num_rows == 0) {
    $sql_alter_2fa_secret = "ALTER TABLE registration ADD COLUMN 2fa_secret VARCHAR(255) NULL DEFAULT NULL";
    if ($conn->query($sql_alter_2fa_secret) === TRUE) {
        $success_messages[] = "Column '2fa_secret' added to 'registration' table successfully.";
    } else {
        $error_messages[] = "Error adding '2fa_secret' column: " . $conn->error;
    }
} else {
    $success_messages[] = "Column '2fa_secret' already exists in 'registration' table.";
}

// --- Add '2fa_enabled' column ---
$check_column_2fa_enabled_sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'registration' AND COLUMN_NAME = '2fa_enabled'";
$result_2fa_enabled = $conn->query($check_column_2fa_enabled_sql);

if ($result_2fa_enabled === FALSE) {
    $error_messages[] = "Error checking for '2fa_enabled' column: " . $conn->error;
} elseif ($result_2fa_enabled->num_rows == 0) {
    $sql_alter_2fa_enabled = "ALTER TABLE registration ADD COLUMN 2fa_enabled TINYINT(1) DEFAULT 0";
    if ($conn->query($sql_alter_2fa_enabled) === TRUE) {
        $success_messages[] = "Column '2fa_enabled' added to 'registration' table successfully with default 0.";
    } else {
        $error_messages[] = "Error adding '2fa_enabled' column: " . $conn->error;
    }
} else {
    $success_messages[] = "Column '2fa_enabled' already exists in 'registration' table.";
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
    echo "<p>Database setup for 2FA completed successfully.</p>";
} else {
    echo "<p>There were errors during 2FA database setup. Please check messages above.</p>";
}
?>
