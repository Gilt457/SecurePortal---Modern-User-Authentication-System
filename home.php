<?php
// Start session to manage user login state
session_start();

// Check if user is logged in, if not redirect to login page
if(!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Get username from session
$username = $_SESSION['username'];
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Welcome <?php echo htmlspecialchars($username); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #f8f9fc;
            --accent-color: #36b9cc;
            --dark-color: #5a5c69;
            --success-color: #1cc88a;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
        }
        
        body {
            font-family: 'Nunito', sans-serif;
            background-color: var(--secondary-color);
        }
        
        /* Sidebar styles */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--primary-color) 0%, #224abe 100%);
            color: white;
            transition: all 0.3s;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            z-index: 1000;
        }
        
        .sidebar-brand {
            padding: 1.5rem 1rem;
            display: flex;
            align-items: center;
            font-size: 1.25rem;
            font-weight: 800;
            margin-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-nav {
            padding: 0;
            list-style: none;
        }
        
        .sidebar-nav .nav-item {
            margin-bottom: 0.25rem;
        }
        
        .sidebar-nav .nav-link {
            padding: 1rem 1rem;
            color: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .sidebar-nav .nav-link:hover, 
        .sidebar-nav .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            border-left: 4px solid white;
        }
        
        .sidebar-nav .nav-link i {
            margin-right: 0.75rem;
            width: 18px;
            text-align: center;
        }
        
        .sidebar-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin: 1rem 0;
        }
        
        /* Main content area */
        .content {
            margin-left: 250px;
            padding: 1.5rem;
        }
        
        /* Topbar */
        .topbar {
            height: 70px;
            background-color: white;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(90, 92, 105, 0.15);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .topbar-divider {
            width: 0;
            border-right: 1px solid #e3e6f0;
            height: 2.375rem;
            margin: auto 1rem;
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(90, 92, 105, 0.15);
            margin-bottom: 1.5rem;
            transition: all 0.3s;
        }
        
        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 0.5rem 2rem 0 rgba(90, 92, 105, 0.2);
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 1.25rem;
            font-weight: 700;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .card-body {
            padding: 1.25rem;
        }
        
        /* Info cards */
        .info-card {
            border-left: 0.25rem solid var(--primary-color);
        }
        
        .info-card.primary {
            border-left-color: var(--primary-color);
        }
        
        .info-card.success {
            border-left-color: var(--success-color);
        }
        
        .info-card.warning {
            border-left-color: var(--warning-color);
        }
        
        .info-card.danger {
            border-left-color: var(--danger-color);
        }
        
        .info-card .card-body {
            padding: 1rem;
        }
        
        .info-card-text {
            color: var(--dark-color);
            text-transform: uppercase;
            font-size: 0.8rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        
        .info-card-number {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--dark-color);
        }
        
        .info-card-icon {
            font-size: 2rem;
            opacity: 0.3;
        }
        
        /* Feature boxes */
        .feature-box {
            padding: 1.5rem;
            border-radius: 0.5rem;
            background-color: white;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(90, 92, 105, 0.15);
            transition: all 0.3s;
            height: 100%;
        }
        
        .feature-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 2rem 0 rgba(90, 92, 105, 0.2);
        }
        
        .feature-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: var(--light-color);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
            color: var(--primary-color);
            font-size: 1.5rem;
        }
        
        /* Footer */
        .footer {
            background-color: white;
            padding: 1.5rem;
            margin-top: 2rem;
            color: var(--dark-color);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 100px;
                overflow: hidden;
            }
            
            .sidebar-brand {
                justify-content: center;
                padding: 1.5rem 0.5rem;
            }
            
            .sidebar-brand span {
                display: none;
            }
            
            .sidebar-nav .nav-link span {
                display: none;
            }
            
            .sidebar-nav .nav-link {
                justify-content: center;
                padding: 1rem 0.5rem;
            }
            
            .sidebar-nav .nav-link i {
                margin-right: 0;
                font-size: 1.2rem;
            }
            
            .content {
                margin-left: 100px;
            }
        }
        
        @media (max-width: 576px) {
            .sidebar {
                width: 0;
            }
            
            .content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-cubes me-2"></i>
            <span>AppName</span>
        </div>
        
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a class="nav-link active" href="home.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <div class="sidebar-divider"></div>
            
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Analytics</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Projects</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-fw fa-calendar"></i>
                    <span>Calendar</span>
                </a>
            </li>
            
            <div class="sidebar-divider"></div>
            
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-fw fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>
    
    <!-- Main Content -->
    <div class="content">
        <!-- Topbar -->
        <div class="topbar">
            <button class="btn btn-link d-md-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <form class="d-none d-md-flex ms-4 w-50">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search for..." aria-label="Search">
                    <button class="btn btn-primary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
            
            <div class="ms-auto d-flex">
                <a href="#" class="btn btn-link position-relative">
                    <i class="fas fa-bell fa-fw"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        3+
                    </span>
                </a>
                
                <a href="#" class="btn btn-link">
                    <i class="fas fa-envelope fa-fw"></i>
                </a>
                
                <div class="topbar-divider"></div>
                
                <div class="dropdown">
                    <a class="btn btn-link dropdown-toggle" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="d-none d-lg-inline text-gray-600 small me-2"><?php echo htmlspecialchars($username); ?></span>
                        <img class="rounded-circle" src="https://ui-avatars.com/api/?name=<?php echo urlencode($username); ?>&background=random" width="32" height="32">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cogs fa-sm fa-fw me-2 text-gray-400"></i> Settings</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-list fa-sm fa-fw me-2 text-gray-400"></i> Activity Log</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Page Content -->
        <div class="container-fluid">
            <!-- Page Heading -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 text-gray-800">Dashboard</h1>
                <a href="#" class="btn btn-primary">
                    <i class="fas fa-download fa-sm"></i> Generate Report
                </a>
            </div>
            
            <!-- Content Row - Info Cards -->
            <div class="row">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card info-card primary">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="info-card-text">Earnings (Monthly)</div>
                                    <div class="info-card-number">$40,000</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar info-card-icon text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card info-card success">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="info-card-text">Earnings (Annual)</div>
                                    <div class="info-card-number">$215,000</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-dollar-sign info-card-icon text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card info-card warning">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="info-card-text">Tasks</div>
                                    <div class="info-card-number">50%</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clipboard-list info-card-icon text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card info-card danger">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="info-card-text">Pending Requests</div>
                                    <div class="info-card-number">18</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-comments info-card-icon text-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Content Row - Charts & Tables -->
            <div class="row">
                <!-- Main Chart -->
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold">Earnings Overview</h6>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-link" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                                    <li><a class="dropdown-item" href="#">Action</a></li>
                                    <li><a class="dropdown-item" href="#">Another action</a></li>
                                    <li><a class="dropdown-item" href="#">Something else here</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <div style="height: 320px; background-color: #f8f9fc; display: flex; align-items: center; justify-content: center; border-radius: 0.35rem;">
                                <div class="text-center">
                                    <i class="fas fa-chart-line fa-3x text-primary mb-3"></i>
                                    <h5>Earnings Overview Chart</h5>
                                    <p class="text-muted">Your earnings data visualization would appear here</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Pie Chart -->
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold">Revenue Sources</h6>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-link" type="button" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton2">
                                    <li><a class="dropdown-item" href="#">Action</a></li>
                                    <li><a class="dropdown-item" href="#">Another action</a></li>
                                    <li><a class="dropdown-item" href="#">Something else here</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <div style="height: 320px; background-color: #f8f9fc; display: flex; align-items: center; justify-content: center; border-radius: 0.35rem;">
                                <div class="text-center">
                                    <i class="fas fa-chart-pie fa-3x text-primary mb-3"></i>
                                    <h5>Revenue Sources Chart</h5>
                                    <p class="text-muted">Your revenue source data would appear here</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Content Row - Features -->
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <h4>Fast Performance</h4>
                        <p>Our platform is optimized for speed and efficiency. Experience lightning-fast loading times and smooth interactions throughout your journey.</p>
                        <a href="#" class="btn btn-primary btn-sm mt-3">Learn More</a>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h4>Advanced Security</h4>
                        <p>Your data is protected with industry-leading security measures. We employ the latest encryption technology to keep your information safe.</p>
                        <a href="#" class="btn btn-primary btn-sm mt-3">Learn More</a>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h4>24/7 Support</h4>
                        <p>Our dedicated support team is available around the clock to assist you with any questions or issues you may encounter.</p>
                        <a href="#" class="btn btn-primary btn-sm mt-3">Contact Support</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6 text-start">
                        <span>&copy; <?php echo date("Y"); ?> AppName. All rights reserved.</span>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="#" class="text-muted me-3">Privacy Policy</a>
                        <a href="#" class="text-muted me-3">Terms of Service</a>
                        <a href="#" class="text-muted">Contact Us</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar on mobile
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.querySelector('.sidebar');
            const content = document.querySelector('.content');
            
            if (sidebar.style.width === '0px' || sidebar.style.width === '') {
                sidebar.style.width = '250px';
                content.style.marginLeft = '250px';
            } else {
                sidebar.style.width = '0px';
                content.style.marginLeft = '0px';
            }
        });
    </script>
</body>
</html>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <style>
      .hero-section {
        background-color: #f8f9fa;
        padding: 60px 0;
        margin-bottom: 30px;
      }
      .feature-box {
        padding: 20px;
        border-radius: 5px;
        margin-bottom: 20px;
        background-color: #fff;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: transform 0.3s;
      }
      .feature-box:hover {
        transform: translateY(-5px);
      }
    </style>
  </head>
  <body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
      <div class="container">
        <a class="navbar-brand" href="home.php">My Website</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="home.php">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">About</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">Services</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">Contact</a>
            </li>
          </ul>
          <div class="d-flex align-items-center">
            <span class="text-light me-3">Welcome, <?php echo htmlspecialchars($username); ?></span>
            <a href="logout.php" class="btn btn-outline-light">Logout</a>
          </div>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-6">
            <h1>Welcome to My Website</h1>
            <p class="lead">This is a simple homepage for your PHP project. Feel free to customize it based on your needs.</p>
            <a href="#" class="btn btn-primary btn-lg">Learn More</a>
          </div>
          <div class="col-lg-6">
            <img src="https://via.placeholder.com/600x400" alt="Hero Image" class="img-fluid rounded">
          </div>
        </div>
      </div>
    </section>

    <!-- Main Content -->
    <div class="container mb-5">
      <h2 class="text-center mb-4">Our Features</h2>
      <div class="row">
        <div class="col-md-4">
          <div class="feature-box">
            <h3>Feature 1</h3>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam at dui vel ipsum faucibus vestibulum.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="feature-box">
            <h3>Feature 2</h3>
            <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="feature-box">
            <h3>Feature 3</h3>
            <p>Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
      <div class="container">
        <div class="row">
          <div class="col-md-6">
            <h5>My Website</h5>
            <p>A simple PHP project with login functionality.</p>
          </div>
          <div class="col-md-3">
            <h5>Links</h5>
            <ul class="list-unstyled">
              <li><a href="home.php" class="text-white">Home</a></li>
              <li><a href="#" class="text-white">About</a></li>
              <li><a href="#" class="text-white">Services</a></li>
              <li><a href="#" class="text-white">Contact</a></li>
            </ul>
          </div>
          <div class="col-md-3">
            <h5>Contact</h5>
            <address>
              123 Main Street<br>
              City, State 12345<br>
              <a href="mailto:info@example.com" class="text-white">info@example.com</a>
            </address>
          </div>
        </div>
        <hr>
        <div class="text-center">
          <p class="mb-0">&copy; <?php echo date("Y"); ?> My Website. All rights reserved.</p>
        </div>
      </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-a2N9OgpbzZlB7MK5Vd8UEsm/BtfVRcbr1Ct7iK/NvR0wZNxyvH0zViDqP2IfAdG" crossorigin="anonymous"></script>
  </body>
</html>