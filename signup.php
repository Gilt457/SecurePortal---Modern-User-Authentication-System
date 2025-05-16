<?php
$success = 0;
$user = 0;

if($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'connect.php';   
    $username = $_POST['username'];  
    $password = $_POST['pwd'];

    // Check if username already exists - using prepared statements for security
    $sql = "SELECT * FROM registration WHERE username=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if($result) {
        $num = mysqli_num_rows($result);
        if($num > 0) {
            $user = 1;
        } else {
            $sql = "INSERT INTO `registration` (username, password) VALUES (?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ss", $username, $password);
            $insertResult = mysqli_stmt_execute($stmt);
            
            if($insertResult) {
                $success = 1;
            } else {
                die(mysqli_error($conn));
            }
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
    <title>Sign Up - Create Your Account</title>
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
        
        .signup-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }
        
        .card-left {
            background: linear-gradient(135deg, #1cc88a, var(--accent-color));
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
        
        .btn-signup {
            background-color: var(--success-color);
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-signup:hover {
            background-color: #169f75;
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
                if($user) {
                    echo '<div class="alert alert-danger alert-dismissible fade show animated" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><strong>Oh no!</strong> Username already exists!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>'; 
                }
                ?>

                <?php
                if($success) {
                    echo '<div class="alert alert-success alert-dismissible fade show animated" role="alert">
                    <i class="fas fa-check-circle me-2"></i><strong>Success!</strong> You have signed up successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>'; 
                }
                ?>
                
                <div class="card signup-card animated">
                    <div class="row g-0">
                        <div class="col-lg-6 card-left">
                            <div class="brand-logo">
                                <i class="fas fa-cubes me-2"></i>AppName
                            </div>
                            <h2 class="mb-4">Join our platform today!</h2>
                            <p class="mb-5">Create an account to get started and unlock all features of our platform.</p>
                            
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                                <div>
                                    <h5 class="m-0">Easy Registration</h5>
                                    <small>Simple and quick signup process</small>
                                </div>
                            </div>
                            
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-bell"></i>
                                </div>
                                <div>
                                    <h5 class="m-0">Instant Access</h5>
                                    <small>Get immediate access to all features</small>
                                </div>
                            </div>
                            
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div>
                                    <h5 class="m-0">Premium Features</h5>
                                    <small>Unlock exclusive content and tools</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6 card-right">
                            <h3 class="text-center mb-4">Create Your Account</h3>
                            <form action="signup.php" method="POST">
                                <div class="mb-4">
                                    <label for="username" class="form-label">Username</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-user text-muted"></i>
                                        </span>
                                        <input type="text" class="form-control" id="username" name="username" placeholder="Choose a username" required>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="pwd" class="form-label">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-lock text-muted"></i>
                                        </span>
                                        <input type="password" class="form-control" id="pwd" name="pwd" placeholder="Create a password" required>
                                    </div>
                                </div>
                                
                                <div class="mb-4 form-check">
                                    <input type="checkbox" class="form-check-input" id="terms" required>
                                    <label class="form-check-label" for="terms">I agree to the <a href="#" class="login-link">Terms & Conditions</a></label>
                                </div>
                                
                                <div class="d-grid mb-4">
                                    <button type="submit" class="btn btn-success btn-signup">
                                        <i class="fas fa-user-plus me-2"></i>Create Account
                                    </button>
                                </div>
                                
                                <div class="text-center">
                                    <p>Already have an account? <a href="index.php" class="login-link">Login</a></p>
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

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
  </head>
  <body>
    <?php
     if($user) {
      echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
  <strong>Oh no sorry </strong> User already exists!
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>'; 
     }
    ?>

    <?php
     if($success) {
      echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
  <strong>Success </strong>You have signed up successfully!
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>'; 
     }
    ?>
    <div class="container mt-5">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="card">
            <div class="card-header">
              <h3 class="text-center">Sign Up</h3>
            </div>
            <div class="card-body">
              <form action="signup.php" method="POST">
                <div class="mb-3">
                  <label for="username" class="form-label">Username</label>
                  <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                  <label for="pwd" class="form-label">Password</label>
                  <input type="password" class="form-control" id="pwd" name="pwd" required>
                </div>
                <div class="d-grid">
                  <button type="submit" class="btn btn-primary">Sign Up</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-a2N9OgpbzZlB7MK5Vd8UEsm/BtfVRcbr1Ct7iK/NvR0wZNxyvH0zViDqP2IfAdG" crossorigin="anonymous"></script>
  </body>
</html>