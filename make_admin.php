<?php
session_start(); // Optional: Could add admin check here too for who can run this
include 'connect.php';

// Simple hardcoded token for protection.
// In a real scenario, this should be a robust check or this script should not exist on a live server.
$secret_token = "supersecretadmintoken123";

if (!isset($_GET['token']) || $_GET['token'] !== $secret_token) {
    die("Access denied. Invalid or missing token.");
}

if (isset($_GET['username'])) {
    $username_to_make_admin = $_GET['username'];
    $new_role = 'admin';

    // Check if user exists
    $sql_check_user = "SELECT id FROM registration WHERE username = ?";
    $stmt_check_user = mysqli_prepare($conn, $sql_check_user);
    mysqli_stmt_bind_param($stmt_check_user, "s", $username_to_make_admin);
    mysqli_stmt_execute($stmt_check_user);
    $result_check_user = mysqli_stmt_get_result($stmt_check_user);

    if (mysqli_num_rows($result_check_user) > 0) {
        // User exists, update their role
        $sql_update_role = "UPDATE registration SET role = ? WHERE username = ?";
        $stmt_update_role = mysqli_prepare($conn, $sql_update_role);
        mysqli_stmt_bind_param($stmt_update_role, "ss", $new_role, $username_to_make_admin);

        if (mysqli_stmt_execute($stmt_update_role)) {
            echo "User '" . htmlspecialchars($username_to_make_admin) . "' has been updated to role '" . htmlspecialchars($new_role) . "'.<br>";
            echo "Please ensure this user logs out and logs back in for the session role to update if they are currently logged in.";
        } else {
            echo "Error updating role for user '" . htmlspecialchars($username_to_make_admin) . "': " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt_update_role);
    } else {
        echo "User '" . htmlspecialchars($username_to_make_admin) . "' not found.";
    }
    mysqli_stmt_close($stmt_check_user);
} else {
    echo "Please provide a 'username' GET parameter to identify the user whose role you want to set to admin.<br>";
    echo "Example: make_admin.php?token=YOUR_SECRET_TOKEN&username=user@example.com";
}

mysqli_close($conn);
?>

<hr>
<p><strong>Usage:</strong> Call this script with `?token=YOUR_SECRET_TOKEN&username=USER_EMAIL`</p>
<p><strong>Security Warning:</strong> This script is for testing and development purposes only. It provides direct database modification capabilities. Remove or secure it properly before deploying to a production environment.</p>
