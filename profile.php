<?php
session_start();
include 'connect.php'; // Include connect for all DB operations

// If user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$current_username = $_SESSION['username'];
$user_id_for_2fa_query = 0; // Will be fetched

// Messages for various actions
$username_update_success = ''; $username_update_error = '';
$password_update_success = ''; $password_update_error = '';
$tfa_success_message = ''; $tfa_error_message = ''; $tfa_secret_display = '';

// Fetch current user's data, including 2FA status and ID
$sql_get_user_data = "SELECT id, 2fa_enabled, 2fa_secret FROM registration WHERE username = ?";
$stmt_get_user_data = mysqli_prepare($conn, $sql_get_user_data);
mysqli_stmt_bind_param($stmt_get_user_data, "s", $current_username);
mysqli_stmt_execute($stmt_get_user_data);
$result_user_data = mysqli_stmt_get_result($stmt_get_user_data);
$current_user_db_data = mysqli_fetch_assoc($result_user_data);
mysqli_stmt_close($stmt_get_user_data);

if ($current_user_db_data) {
    $user_id_for_2fa_query = $current_user_db_data['id'];
    $tfa_enabled = $current_user_db_data['2fa_enabled'];
    $current_2fa_secret = $current_user_db_data['2fa_secret']; // Might be null
} else {
    // Should not happen for a logged-in user, but handle defensively
    die("Error fetching user data. Please try logging in again.");
}


// Handle Username Update
if (isset($_POST['update_username'])) {
    // ... (username update logic from previous step, unchanged) ...
    $new_username = trim($_POST['new_username']);
    if (empty($new_username)) {
        $username_update_error = "New username cannot be empty.";
    } elseif ($new_username === $current_username) {
        $username_update_error = "New username is the same as the current one.";
    } else {
        if (!filter_var($new_username, FILTER_VALIDATE_EMAIL)) {
            $username_update_error = "Invalid email format for new username.";
        } else {
            $sql_check_username = "SELECT id FROM registration WHERE username = ? AND username != ?";
            $stmt_check_username = mysqli_prepare($conn, $sql_check_username);
            mysqli_stmt_bind_param($stmt_check_username, "ss", $new_username, $current_username);
            mysqli_stmt_execute($stmt_check_username);
            $result_check_username = mysqli_stmt_get_result($stmt_check_username);
            if (mysqli_num_rows($result_check_username) > 0) {
                $username_update_error = "This username (email) is already taken.";
            } else {
                $sql_update_username = "UPDATE registration SET username = ? WHERE username = ?";
                $stmt_update_username = mysqli_prepare($conn, $sql_update_username);
                mysqli_stmt_bind_param($stmt_update_username, "ss", $new_username, $current_username);
                if (mysqli_stmt_execute($stmt_update_username)) {
                    $sql_update_resets = "UPDATE password_resets SET email = ? WHERE email = ?";
                    $stmt_update_resets = mysqli_prepare($conn, $sql_update_resets);
                    mysqli_stmt_bind_param($stmt_update_resets, "ss", $new_username, $current_username);
                    mysqli_stmt_execute($stmt_update_resets);
                    mysqli_stmt_close($stmt_update_resets);
                    $_SESSION['username'] = $new_username;
                    $current_username = $new_username;
                    $username_update_success = "Username updated successfully to " . htmlspecialchars($new_username) . "!";
                } else {
                    $username_update_error = "Error updating username: " . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt_update_username);
            }
            mysqli_stmt_close($stmt_check_username);
        }
    }
}

// Handle Password Update
if (isset($_POST['update_password'])) {
    // ... (password update logic from previous step, unchanged) ...
    $current_password_form = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];
    if (empty($current_password_form) || empty($new_password) || empty($confirm_new_password)) {
        $password_update_error = "All password fields are required.";
    } elseif ($new_password !== $confirm_new_password) {
        $password_update_error = "New password and confirm password do not match.";
    } else {
        $sql_get_password = "SELECT password FROM registration WHERE username = ?";
        $stmt_get_password = mysqli_prepare($conn, $sql_get_password);
        mysqli_stmt_bind_param($stmt_get_password, "s", $current_username);
        mysqli_stmt_execute($stmt_get_password);
        $result_get_password = mysqli_stmt_get_result($stmt_get_password);
        $user_db_data_for_pass = mysqli_fetch_assoc($result_get_password); // Renamed to avoid conflict
        mysqli_stmt_close($stmt_get_password);
        if ($user_db_data_for_pass && $current_password_form === $user_db_data_for_pass['password']) {
            $sql_update_password = "UPDATE registration SET password = ? WHERE username = ?";
            $stmt_update_password = mysqli_prepare($conn, $sql_update_password);
            mysqli_stmt_bind_param($stmt_update_password, "ss", $new_password, $current_username);
            if (mysqli_stmt_execute($stmt_update_password)) {
                $password_update_success = "Password updated successfully!";
            } else {
                $password_update_error = "Error updating password: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt_update_password);
        } else {
            $password_update_error = "Incorrect current password.";
        }
    }
}

// Handle Enable 2FA
if (isset($_POST['enable_2fa'])) {
    $mock_secret = bin2hex(random_bytes(16)); // Generate a 32-character hex secret

    $sql_enable_2fa = "UPDATE registration SET 2fa_secret = ?, 2fa_enabled = 1 WHERE id = ?";
    $stmt_enable_2fa = mysqli_prepare($conn, $sql_enable_2fa);
    mysqli_stmt_bind_param($stmt_enable_2fa, "si", $mock_secret, $user_id_for_2fa_query);

    if (mysqli_stmt_execute($stmt_enable_2fa)) {
        $tfa_enabled = 1; // Update status for current page
        $current_2fa_secret = $mock_secret; // Update for current page
        $tfa_secret_display = $mock_secret;
        $tfa_success_message = "2FA Enabled successfully! Save this secret in your authenticator app.";
    } else {
        $tfa_error_message = "Error enabling 2FA: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt_enable_2fa);
}

// Handle Disable 2FA
if (isset($_POST['disable_2fa'])) {
    $sql_disable_2fa = "UPDATE registration SET 2fa_secret = NULL, 2fa_enabled = 0 WHERE id = ?";
    $stmt_disable_2fa = mysqli_prepare($conn, $sql_disable_2fa);
    mysqli_stmt_bind_param($stmt_disable_2fa, "i", $user_id_for_2fa_query);

    if (mysqli_stmt_execute($stmt_disable_2fa)) {
        $tfa_enabled = 0; // Update status for current page
        $current_2fa_secret = null;
        $tfa_success_message = "2FA Disabled successfully.";
    } else {
        $tfa_error_message = "Error disabling 2FA: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt_disable_2fa);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - SecurePortal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f8f9fc; }
        .profile-container { max-width: 700px; margin: 50px auto; padding: 2rem; background-color: #fff; border-radius: 0.5rem; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15); }
        .profile-header { margin-bottom: 2rem; text-align: center; color: #4e73df; }
        .form-section { margin-bottom: 2.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid #e3e6f0; }
        .form-section:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
        .form-label { font-weight: 500; }
        .btn-custom-primary { background-color: #4e73df; border-color: #4e73df; color: white; }
        .btn-custom-primary:hover { background-color: #2e59d9; border-color: #2653d4; }
        .btn-custom-danger { background-color: #e74a3b; border-color: #e74a3b; color: white; }
        .btn-custom-danger:hover { background-color: #c82333; border-color: #bd2130; }
        .alert { margin-top: 1rem; }
        .secret-display { background-color: #e9ecef; padding: 0.75rem; border-radius: 0.25rem; font-family: monospace; word-break: break-all; margin-top: 0.5rem;}
    </style>
</head>
<body>
    <?php /* include 'home_navbar.php'; // Assuming a reusable navbar */ ?>
     <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="home.php" style="color: #4e73df; font-weight: bold;">SecurePortal</a>
            <div class="ms-auto">
                 <a href="home.php" class="btn btn-sm btn-outline-secondary me-2">Dashboard</a>
                 <a href="logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container profile-container">
        <header class="profile-header"><h2><i class="fas fa-user-edit me-2"></i>Manage Your Profile</h2></header>
        <p class="lead text-center mb-4">Current Username (Email): <strong><?php echo htmlspecialchars($current_username); ?></strong></p>

        <!-- Username Update Form -->
        <section class="form-section">
            <h4>Update Username/Email</h4>
            <?php if ($username_update_success): ?><div class="alert alert-success"><?php echo $username_update_success; ?></div><?php endif; ?>
            <?php if ($username_update_error): ?><div class="alert alert-danger"><?php echo $username_update_error; ?></div><?php endif; ?>
            <form method="POST" action="profile.php">
                <div class="mb-3"><label for="new_username" class="form-label">New Username (Email Address)</label><input type="email" class="form-control" id="new_username" name="new_username" required></div>
                <button type="submit" name="update_username" class="btn btn-custom-primary w-100"><i class="fas fa-user-check me-2"></i>Update Username</button>
            </form>
        </section>

        <!-- Password Update Form -->
        <section class="form-section">
            <h4>Update Password</h4>
            <?php if ($password_update_success): ?><div class="alert alert-success"><?php echo $password_update_success; ?></div><?php endif; ?>
            <?php if ($password_update_error): ?><div class="alert alert-danger"><?php echo $password_update_error; ?></div><?php endif; ?>
            <form method="POST" action="profile.php">
                <div class="mb-3"><label for="current_password" class="form-label">Current Password</label><input type="password" class="form-control" id="current_password" name="current_password" required></div>
                <div class="mb-3"><label for="new_password" class="form-label">New Password</label><input type="password" class="form-control" id="new_password" name="new_password" required></div>
                <div class="mb-3"><label for="confirm_new_password" class="form-label">Confirm New Password</label><input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" required></div>
                <button type="submit" name="update_password" class="btn btn-custom-primary w-100"><i class="fas fa-key me-2"></i>Update Password</button>
            </form>
        </section>

        <!-- Two-Factor Authentication Section -->
        <section class="form-section">
            <h4>Two-Factor Authentication (2FA)</h4>
            <?php if ($tfa_success_message): ?><div class="alert alert-success"><?php echo $tfa_success_message; ?></div><?php endif; ?>
            <?php if ($tfa_error_message): ?><div class="alert alert-danger"><?php echo $tfa_error_message; ?></div><?php endif; ?>

            <?php if ($tfa_enabled): ?>
                <p>Status: <span class="badge bg-success">Enabled</span></p>
                <?php if (!empty($current_2fa_secret) && empty($tfa_secret_display)) : // Only show stored secret if not just generated to avoid confusion ?>
                     <p class="mt-2"><strong>Current 2FA Secret:</strong>
                        <span class="secret-display text-muted small">(Stored - Not shown for security after initial setup)</span>
                     </p>
                <?php endif; ?>
                <form method="POST" action="profile.php" class="mt-3">
                    <button type="submit" name="disable_2fa" class="btn btn-custom-danger w-100">
                        <i class="fas fa-shield-alt me-2"></i>Disable 2FA
                    </button>
                </form>
            <?php else: ?>
                <p>Status: <span class="badge bg-secondary">Disabled</span></p>
                <p class="text-muted small">Enable 2FA to add an extra layer of security to your account. You will need an authenticator app (e.g., Google Authenticator, Authy).</p>
                <form method="POST" action="profile.php" class="mt-3">
                    <button type="submit" name="enable_2fa" class="btn btn-custom-primary w-100">
                        <i class="fas fa-shield-alt me-2"></i>Enable 2FA
                    </button>
                </form>
            <?php endif; ?>

            <?php if ($tfa_secret_display): ?>
                <div class="mt-3">
                    <p><strong>Your 2FA Secret:</strong></p>
                    <div class="secret-display"><?php echo htmlspecialchars($tfa_secret_display); ?></div>
                    <p class="small text-muted mt-2">Please save this secret in your authenticator app. You will use the codes generated by the app to log in.</p>
                    <p class="small text-danger"><strong>Important:</strong> This secret will only be shown once. Store it securely.</p>
                    <div class="mt-2 text-center">
                        <img src="https://via.placeholder.com/150?text=QR+Code+Placeholder" alt="QR Code Placeholder" class="img-thumbnail">
                        <p class="small text-muted">Scan with authenticator app (placeholder)</p>
                    </div>
                </div>
            <?php endif; ?>
        </section>

        <div class="text-center mt-4">
            <a href="home.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back to Dashboard</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
