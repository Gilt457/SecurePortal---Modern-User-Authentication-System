<?php
session_start();
$message = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'connect.php';
    $email = $_POST['email'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Check if email exists in the registration table (username column)
        $sql_check_email = "SELECT * FROM registration WHERE username = ?";
        $stmt_check_email = mysqli_prepare($conn, $sql_check_email);
        mysqli_stmt_bind_param($stmt_check_email, "s", $email);
        mysqli_stmt_execute($stmt_check_email);
        $result_check_email = mysqli_stmt_get_result($stmt_check_email);

        if (mysqli_num_rows($result_check_email) > 0) {
            // Email exists, generate token
            $token = bin2hex(random_bytes(32));
            $expires = time() + 3600; // Token expires in 1 hour

            // Store token in password_resets table
            $sql_insert_token = "INSERT INTO password_resets (email, token, expires) VALUES (?, ?, ?)";
            $stmt_insert_token = mysqli_prepare($conn, $sql_insert_token);
            mysqli_stmt_bind_param($stmt_insert_token, "ssi", $email, $token, $expires);

            if (mysqli_stmt_execute($stmt_insert_token)) {
                // Mock email sending
                $message = "If an account with that email exists, a password reset link has been sent. Please check your inbox. Token: " . $token; // Display token for testing
            } else {
                $error = "Error storing reset token: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt_insert_token);
        } else {
            // Email does not exist, but show a generic message for security
            $message = "If an account with that email exists, a password reset link has been sent. Please check your inbox.";
        }
        mysqli_stmt_close($stmt_check_email);
    }
    mysqli_close($conn);
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password - SecurePortal</title>
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
        .login-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .login-link:hover {
            color: #2e59d9;
        }
        .alert {
            border-radius: 0.5rem;
            font-weight: 500;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h3 class="form-title"><i class="fas fa-key me-2"></i>Forgot Your Password?</h3>
        <p class="text-muted text-center mb-4">Enter your email address below and we'll send you a link to reset your password.</p>

        <?php if (!empty($message)): ?>
            <div class="alert alert-success animated" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo $message; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger animated" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="forgot_password.php" method="POST">
            <div class="mb-4">
                <label for="email" class="form-label">Email address</label>
                <div class="input-group">
                    <span class="input-group-text bg-light">
                        <i class="fas fa-envelope text-muted"></i>
                    </span>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                </div>
            </div>

            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-submit">
                    <i class="fas fa-paper-plane me-2"></i>Send Reset Link
                </button>
            </div>

            <div class="text-center">
                <p><a href="index.php" class="login-link"><i class="fas fa-arrow-left me-1"></i>Back to Login</a></p>
            </div>
        </form>
        <div class="text-center mt-4 text-muted">
            <small>&copy; <?php echo date("Y"); ?> SecurePortal. All rights reserved.</small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
