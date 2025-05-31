<?php
session_start();
include 'connect.php';

// If 2FA user ID is not set in session, redirect to login (means user didn't pass password check)
if (!isset($_SESSION['2fa_user_id'])) {
    header("Location: index.php");
    exit();
}

$error_message = '';
$user_id = $_SESSION['2fa_user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $submitted_code = trim($_POST['2fa_code']);

    if (empty($submitted_code)) {
        $error_message = "Please enter your authentication code.";
    } else {
        // Fetch user's 2FA secret and other details needed for final login
        $sql_get_user = "SELECT username, role, 2fa_secret FROM registration WHERE id = ?";
        $stmt_get_user = mysqli_prepare($conn, $sql_get_user);
        mysqli_stmt_bind_param($stmt_get_user, "i", $user_id);
        mysqli_stmt_execute($stmt_get_user);
        $result_user = mysqli_stmt_get_result($stmt_get_user);
        $user_data = mysqli_fetch_assoc($result_user);
        mysqli_stmt_close($stmt_get_user);

        if ($user_data && !empty($user_data['2fa_secret'])) {
            // ** MOCK VALIDATION **
            // In a real application, use a TOTP library (e.g., PHPGangsta_GoogleAuthenticator, RobThree/TwoFactorAuth)
            // For this conceptual step, we'll do a placeholder check.
            // Example: Check if the code is "123456" OR if it's a prefix of the secret (highly insecure, for demo only)
            $is_code_valid = false;
            if ($submitted_code === "123456") { // Universal test code
                $is_code_valid = true;
            } elseif (strpos($user_data['2fa_secret'], $submitted_code) === 0 && strlen($submitted_code) >= 6) {
                // Example: If secret is "abc123def456" and code is "abc123", it's "valid" for this mock.
                // $is_code_valid = true; // Commented out as "123456" is simpler for testing.
            }


            if ($is_code_valid) {
                // Code is valid, complete login
                $_SESSION['username'] = $user_data['username'];
                $_SESSION['role'] = $user_data['role'];

                // Clear the temporary 2FA session variable
                unset($_SESSION['2fa_user_id']);

                header("Location: home.php");
                exit();
            } else {
                $error_message = "Invalid authentication code. Please try again.";
            }
        } else {
            // Should not happen if 2FA is enabled and user ID is valid
            $error_message = "Could not retrieve 2FA information. Please try logging in again.";
            unset($_SESSION['2fa_user_id']); // Clear session to force re-login
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Two-Factor Authentication - SecurePortal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f8f9fc; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px;}
        .verify-container { max-width: 450px; background-color: #fff; padding: 2.5rem; border-radius: 0.5rem; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15); }
        .verify-header { margin-bottom: 1.5rem; text-align: center; color: #4e73df; }
        .form-label { font-weight: 500; }
        .btn-custom-primary { background-color: #4e73df; border-color: #4e73df; color: white; }
        .btn-custom-primary:hover { background-color: #2e59d9; border-color: #2653d4; }
        .alert { margin-top: 1rem; }
    </style>
</head>
<body>
    <div class="container verify-container">
        <header class="verify-header">
            <h2><i class="fas fa-shield-halved me-2"></i>Enter Authentication Code</h2>
        </header>
        <p class="text-muted text-center mb-4">Open your authenticator app and enter the 6-digit code for your account.</p>

        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form method="POST" action="verify_2fa.php">
            <div class="mb-3">
                <label for="2fa_code" class="form-label">Authentication Code</label>
                <input type="text" class="form-control form-control-lg" id="2fa_code" name="2fa_code"
                       required autofocus autocomplete="one-time-code" inputmode="numeric" pattern="[0-9]*" maxlength="6">
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-custom-primary btn-lg">
                    <i class="fas fa-check-circle me-2"></i>Verify Code
                </button>
            </div>
        </form>
        <div class="text-center mt-4">
            <a href="index.php?logout=1" class="text-muted small">Cancel and return to login</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
