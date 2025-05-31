<?php
session_start();
include 'connect.php';

$error_message = '';
$success_message = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $sql_check_token = "SELECT * FROM email_verifications WHERE token = ? AND expires > ?";
    $stmt_check_token = mysqli_prepare($conn, $sql_check_token);
    mysqli_stmt_bind_param($stmt_check_token, "si", $token, time());
    mysqli_stmt_execute($stmt_check_token);
    $result_check_token = mysqli_stmt_get_result($stmt_check_token);

    if ($result_check_token && mysqli_num_rows($result_check_token) > 0) {
        $verification_data = mysqli_fetch_assoc($result_check_token);
        $user_id = $verification_data['user_id'];

        // Token is valid, update user's verification status
        $sql_update_user = "UPDATE registration SET is_verified = 1 WHERE id = ?";
        $stmt_update_user = mysqli_prepare($conn, $sql_update_user);
        mysqli_stmt_bind_param($stmt_update_user, "i", $user_id);

        if (mysqli_stmt_execute($stmt_update_user)) {
            // User updated, now delete the token
            $sql_delete_token = "DELETE FROM email_verifications WHERE token = ?";
            $stmt_delete_token = mysqli_prepare($conn, $sql_delete_token);
            mysqli_stmt_bind_param($stmt_delete_token, "s", $token);
            mysqli_stmt_execute($stmt_delete_token);
            mysqli_stmt_close($stmt_delete_token);

            $success_message = "Email successfully verified! You can now <a href='index.php' class='alert-link'>login</a>.";
        } else {
            $error_message = "Error updating your account. Please try again or contact support.";
        }
        mysqli_stmt_close($stmt_update_user);
    } else {
        $error_message = "Invalid or expired verification link. Please try registering again or request a new verification email if applicable.";
    }
    mysqli_stmt_close($stmt_check_token);
    mysqli_close($conn);
} else {
    $error_message = "No verification token provided. Please use the link sent to your email address.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f8f9fa; }
        .container { max-width: 600px; background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); text-align: center; }
        .alert { margin-top: 15px; }
        .alert-link { font-weight: bold; color: inherit; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4">Email Verification Status</h2>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <?php if (empty($success_message) && empty($error_message)): ?>
             <div class="alert alert-info" role="alert">
                Processing your verification request...
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <p class="mt-3">Go back to <a href="index.php">Login Page</a> or <a href="signup.php">Sign Up Page</a>.</p>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome for icons (optional, if you want to add them to messages) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>
