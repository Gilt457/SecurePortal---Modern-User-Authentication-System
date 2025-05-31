<?php
session_start(); // Good practice, though not strictly used in this minimal version yet
$success_message = '';
$user_exists_error = 0;
$general_error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'connect.php';
    $username = $_POST['username']; // This is treated as email
    $password = $_POST['pwd'];

    if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
        $general_error = "Invalid email format. Please use a valid email address as your username.";
    } else {
        $sql_check_user = "SELECT * FROM registration WHERE username = ?";
        $stmt_check_user = mysqli_prepare($conn, $sql_check_user);
        mysqli_stmt_bind_param($stmt_check_user, "s", $username);
        mysqli_stmt_execute($stmt_check_user);
        $result_check_user = mysqli_stmt_get_result($stmt_check_user);

        if ($result_check_user && mysqli_num_rows($result_check_user) > 0) {
            $user_exists_error = 1;
        } else {
            // Storing password as plain text as per existing project structure. Hashing is recommended.
            $sql_insert_user = "INSERT INTO registration (username, password) VALUES (?, ?)";
            $stmt_insert_user = mysqli_prepare($conn, $sql_insert_user);
            mysqli_stmt_bind_param($stmt_insert_user, "ss", $username, $password);

            if (mysqli_stmt_execute($stmt_insert_user)) {
                $user_id = mysqli_insert_id($conn);

                $token = bin2hex(random_bytes(32));
                $expires = time() + 86400; // 24 hours

                $sql_insert_token = "INSERT INTO email_verifications (user_id, token, expires) VALUES (?, ?, ?)";
                $stmt_insert_token = mysqli_prepare($conn, $sql_insert_token);
                mysqli_stmt_bind_param($stmt_insert_token, "isi", $user_id, $token, $expires);

                if (mysqli_stmt_execute($stmt_insert_token)) {
                    $success_message = "Registration successful! A verification email has been sent to " . htmlspecialchars($username) . ". Please check your inbox. For testing, your token is: " . $token;
                } else {
                    $general_error = "Failed to store verification token. Please contact support. Error: " . mysqli_error($conn);
                    // Consider deleting the user or marking for admin review
                }
                mysqli_stmt_close($stmt_insert_token);
            } else {
                $general_error = "Registration failed: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt_insert_user);
        }
        mysqli_stmt_close($stmt_check_user);
    }
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f8f9fa; }
        .container { max-width: 500px; background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .alert { margin-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4">Create Account</h2>

        <?php if ($user_exists_error): ?>
            <div class="alert alert-danger">Username (email) already exists!</div>
        <?php endif; ?>
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (!empty($general_error)): ?>
            <div class="alert alert-danger"><?php echo $general_error; ?></div>
        <?php endif; ?>

        <form action="signup.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Email (as Username):</label>
                <input type="email" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="pwd" class="form-label">Password:</label>
                <input type="password" class="form-control" id="pwd" name="pwd" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Sign Up</button>
            </div>
        </form>
        <p class="text-center mt-3">Already have an account? <a href="index.php">Login</a></p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
