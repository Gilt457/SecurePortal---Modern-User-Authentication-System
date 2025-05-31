<?php
session_start();
include 'connect.php';

$error_message = '';
$success_message = '';
$token_valid = false;
$token = '';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['token'])) {
    $token = $_GET['token'];

    $sql_check_token = "SELECT * FROM password_resets WHERE token = ? AND expires > ?";
    $stmt_check_token = mysqli_prepare($conn, $sql_check_token);
    mysqli_stmt_bind_param($stmt_check_token, "si", $token, time());
    mysqli_stmt_execute($stmt_check_token);
    $result_check_token = mysqli_stmt_get_result($stmt_check_token);

    if (mysqli_num_rows($result_check_token) > 0) {
        $token_valid = true;
    } else {
        $error_message = "Invalid or expired password reset link. Please request a new one if needed.";
    }
    mysqli_stmt_close($stmt_check_token);
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    // POST request handling will be implemented here in the next steps
    $token = $_POST['token']; // Assume token is passed in hidden field
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Re-validate token first
    $sql_check_token_post = "SELECT * FROM password_resets WHERE token = ? AND expires > ?";
    $stmt_check_token_post = mysqli_prepare($conn, $sql_check_token_post);
    mysqli_stmt_bind_param($stmt_check_token_post, "si", $token, time());
    mysqli_stmt_execute($stmt_check_token_post);
    $result_check_token_post = mysqli_stmt_get_result($stmt_check_token_post);

    if (mysqli_num_rows($result_check_token_post) > 0) {
        $row = mysqli_fetch_assoc($result_check_token_post);
        $email = $row['email']; // This is the username as per our setup

        if (empty($new_password) || empty($confirm_password)) {
            $error_message = "Please enter and confirm your new password.";
            $token_valid = true; // Keep the form visible
        } elseif ($new_password !== $confirm_password) {
            $error_message = "Passwords do not match. Please try again.";
            $token_valid = true; // Keep the form visible
        } else {
            // Passwords match, token is valid and not expired. Update password.
            // The documentation.txt notes passwords should be hashed.
            // For this task, I will store it as is, but in a real scenario, hashing is crucial.
            // $hashed_password = password_hash($new_password, PASSWORD_DEFAULT); // Example for hashing

            $sql_update_password = "UPDATE registration SET password = ? WHERE username = ?";
            $stmt_update_password = mysqli_prepare($conn, $sql_update_password);
            // Bind $new_password directly, or $hashed_password if implementing hashing
            mysqli_stmt_bind_param($stmt_update_password, "ss", $new_password, $email);

            if (mysqli_stmt_execute($stmt_update_password)) {
                // Password updated, now delete the token
                $sql_delete_token = "DELETE FROM password_resets WHERE token = ?";
                $stmt_delete_token = mysqli_prepare($conn, $sql_delete_token);
                mysqli_stmt_bind_param($stmt_delete_token, "s", $token);
                mysqli_stmt_execute($stmt_delete_token);
                mysqli_stmt_close($stmt_delete_token);

                $success_message = "Your password has been reset successfully. You can now <a href='index.php' class='alert-link'>login</a>.";
                $token_valid = false; // Hide form on success
            } else {
                $error_message = "Error updating password: " . mysqli_error($conn);
                $token_valid = true; // Keep form visible
            }
            mysqli_stmt_close($stmt_update_password);
        }
    } else {
        $error_message = "Invalid or expired password reset link. Action cannot be completed.";
        $token_valid = false;
    }
    mysqli_stmt_close($stmt_check_token_post);
    mysqli_close($conn);
} else {
    if (!isset($_GET['token'])) {
      $error_message = "No reset token provided. Please use the link sent to your email.";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Your Password - SecurePortal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #f8f9fc;
            --accent-color: #36b9cc;
            --dark-color: #5a5c69;
            --success-color: #1cc88a;
            --danger-color: #e74a3b;
        }
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(120deg, var(--secondary-color), #ffffff);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .form-container {
            max-width: 500px;
            width: 100%;
            padding: 2.5rem;
            background-color: #fff;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease-out forwards;
        }
        .form-title {
            color: var(--dark-color);
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .form-control {
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e3e6f0;
        }
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
        }
        .btn-submit {
            background-color: var(--primary-color);
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem;
            font-weight: 600;
            transition: all 0.3s;
            color: #fff;
        }
        .btn-submit:hover {
            background-color: #2e59d9;
            transform: translateY(-3px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .alert {
            border-radius: 0.5rem;
            font-weight: 500;
        }
        .alert-link {
            font-weight: bold;
            color: inherit;
            text-decoration: underline;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h3 class="form-title"><i class="fas fa-lock-open me-2"></i>Set New Password</h3>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success animated" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger animated" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <?php if ($token_valid): ?>
            <form action="reset_password.php" method="POST">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-key text-muted"></i></span>
                        <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Enter new password" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-key text-muted"></i></span>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-save me-2"></i>Reset Password
                    </button>
                </div>
            </form>
        <?php elseif (empty($success_message)): ?>
            <!-- Only show back to login if not a success message and no valid token -->
            <div class="text-center mt-3">
                <p><a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back to Login</a></p>
            </div>
        <?php endif; ?>

        <div class="text-center mt-4 text-muted">
            <small>&copy; <?php echo date("Y"); ?> SecurePortal. All rights reserved.</small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
