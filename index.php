<?php
$login = 0;
$invalid = 0;

if($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'connect.php';   
    $username = $_POST['username'];  
    $password = $_POST['pwd'];

    // login to the website - using prepared statements for security
    $sql = "SELECT * FROM registration WHERE username=? AND password=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if($result) {
        $num = mysqli_num_rows($result);
        if($num > 0) {
            $login = 1;
            session_start();
            $_SESSION['username'] = $username;
            header("Location: home.php");
            exit();
        } else {
            $invalid = 1;
        }  
    }
    mysqli_stmt_close($stmt);
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to Our Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #f8f9fc;
            --accent-color: #36b9cc;
            --dark-color: #5a5c69;
            --success-color: #1cc88a;
        }
        
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(120deg, var(--secondary-color), #ffffff);
            height: 100vh;
        }
        
        .main-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .login-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }
        
        .card-left {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .brand-logo {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 2rem;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .feature-icon {
            background-color: rgba(255, 255, 255, 0.2);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
        }
        
        .card-right {
            padding: 2.5rem;
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
        
        .btn-login {
            background-color: var(--primary-color);
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            background-color: #2e59d9;
            transform: translateY(-3px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .signup-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .signup-link:hover {
            color: #2e59d9;
        }
        
        .alert {
            border-radius: 0.5rem;
            font-weight: 500;
        }
        
        /* Animation for the card */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animated {
            animation: fadeIn 0.5s ease-out forwards;
        }
        
        /* Responsive adjustments */
        @media (max-width: 992px) {
            .card-left {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container main-container">
        <div class="row justify-content-center w-100">
            <div class="col-lg-10 col-xl-9">
                <?php 
                if($login) {
                    echo '<div class="alert alert-success alert-dismissible fade show animated" role="alert">
                    <i class="fas fa-check-circle me-2"></i><strong>Success!</strong> You have successfully logged in!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>'; 
                }
                ?>

                <?php 
                if($invalid) {
                    echo '<div class="alert alert-danger alert-dismissible fade show animated" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><strong>Error!</strong> Invalid username or password!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>'; 
                }
                ?>
                
                <div class="card login-card animated">
                    <div class="row g-0">
                        <div class="col-lg-6 card-left">
                            <div class="brand-logo">
                                <i class="fas fa-cubes me-2"></i>AppName
                            </div>
                            <h2 class="mb-4">Welcome to our platform!</h2>
                            <p class="mb-5">Access all the features our platform offers with a simple login. Join our community today.</p>
                            
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div>
                                    <h5 class="m-0">Secure Access</h5>
                                    <small>Your data is encrypted and secure</small>
                                </div>
                            </div>
                            
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-tachometer-alt"></i>
                                </div>
                                <div>
                                    <h5 class="m-0">Fast Performance</h5>
                                    <small>Optimized for speed and efficiency</small>
                                </div>
                            </div>
                            
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-cog"></i>
                                </div>
                                <div>
                                    <h5 class="m-0">Easy Management</h5>
                                    <small>Intuitive interface for all users</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6 card-right">
                            <h3 class="text-center mb-4">Sign In to Your Account</h3>
                            <form action="index.php" method="POST">
                                <div class="mb-4">
                                    <label for="username" class="form-label">Username</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-user text-muted"></i>
                                        </span>
                                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="pwd" class="form-label">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-lock text-muted"></i>
                                        </span>
                                        <input type="password" class="form-control" id="pwd" name="pwd" placeholder="Enter your password" required>
                                    </div>
                                </div>
                                
                                <div class="mb-4 form-check">
                                    <input type="checkbox" class="form-check-input" id="remember">
                                    <label class="form-check-label" for="remember">Remember me</label>
                                </div>
                                
                                <div class="d-grid mb-4">
                                    <button type="submit" class="btn btn-primary btn-login">
                                        <i class="fas fa-sign-in-alt me-2"></i>Login
                                    </button>
                                </div>
                                
                                <div class="text-center">
                                    <p>Don't have an account? <a href="signup.php" class="signup-link">Sign Up</a></p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4 text-muted">
                    <small>&copy; <?php echo date("Y"); ?> AppName. All rights reserved.</small>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>