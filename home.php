<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
$username = $_SESSION['username'];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - <?php echo htmlspecialchars($username); ?> - SecurePortal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4e73df; --secondary-color: #f8f9fc; --accent-color: #36b9cc;
            --dark-color: #5a5c69; --success-color: #1cc88a; --warning-color: #f6c23e;
            --danger-color: #e74a3b; --light-color: #f8f9fc;
            --text-dark: #343a40; --text-light: #f8f9fa;
            --bg-dark-main: #1a1a1a; --bg-dark-surface: #282828; --bg-dark-header: #2d2d2d;
            --border-dark: #383838;
        }
        body { font-family: 'Nunito', sans-serif; background-color: var(--secondary-color); color: var(--text-dark); transition: background-color 0.3s, color 0.3s; }
        .sidebar {
            min-height: 100vh; background: linear-gradient(180deg, var(--primary-color) 0%, #224abe 100%);
            color: var(--text-light); transition: all 0.3s; position: fixed; top: 0; left: 0; width: 250px; z-index: 1000;
        }
        .sidebar-brand {
            padding: 1.5rem 1rem; display: flex; align-items: center; font-size: 1.25rem;
            font-weight: 800; margin-bottom: 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .sidebar-nav { padding: 0; list-style: none; }
        .sidebar-nav .nav-item { margin-bottom: 0.25rem; }
        .sidebar-nav .nav-link {
            padding: 1rem 1rem; color: rgba(255, 255, 255, 0.8); display: flex;
            align-items: center; font-weight: 600; transition: all 0.3s;
        }
        .sidebar-nav .nav-link:hover, .sidebar-nav .nav-link.active {
            color: var(--text-light); background-color: rgba(255, 255, 255, 0.1); border-left: 4px solid var(--text-light);
        }
        .sidebar-nav .nav-link i { margin-right: 0.75rem; width: 18px; text-align: center; }
        .sidebar-divider { border-top: 1px solid rgba(255, 255, 255, 0.1); margin: 1rem 0; }
        .content { margin-left: 250px; padding: 0; transition: margin-left 0.3s; }
        .topbar {
            height: 70px; background-color: var(--light-color); box-shadow: 0 0.15rem 1.75rem 0 rgba(90, 92, 105, 0.15);
            display: flex; align-items: center; padding: 0 1.5rem; transition: background-color 0.3s, color 0.3s;
        }
        .main-content-area { padding: 1.5rem; }
        .topbar-divider { width: 0; border-right: 1px solid #e3e6f0; height: 2.375rem; margin: auto 1rem; }
        .card {
            border: none; border-radius: 0.5rem; box-shadow: 0 0.15rem 1.75rem 0 rgba(90, 92, 105, 0.15);
            margin-bottom: 1.5rem; transition: all 0.3s; background-color: var(--light-color); color: var(--text-dark);
        }
        .card:hover { transform: translateY(-4px); box-shadow: 0 0.5rem 2rem 0 rgba(90, 92, 105, 0.2); }
        .card-header {
            background-color: var(--light-color); border-bottom: 1px solid #e3e6f0; padding: 1rem 1.25rem;
            font-weight: 700; font-size: 1.1rem; display: flex; align-items: center; justify-content: space-between;
            transition: background-color 0.3s, color 0.3s, border-color 0.3s;
        }
        .info-card { border-left-width: 0.25rem; border-left-style: solid; }
        .info-card.primary { border-left-color: var(--primary-color); }
        .info-card.success { border-left-color: var(--success-color); }
        .info-card.warning { border-left-color: var(--warning-color); }
        .info-card.danger { border-left-color: var(--danger-color); }
        .info-card-text { text-transform: uppercase; font-size: 0.8rem; font-weight: 700; margin-bottom: 0.25rem; }
        .info-card-number { font-size: 1.75rem; font-weight: 700; }
        .info-card-icon { font-size: 2rem; opacity: 0.3; }
        .footer { background-color: var(--light-color); padding: 1.5rem; margin-top: 1.5rem; color: var(--dark-color); transition: background-color 0.3s, color 0.3s;}

        /* Dark Mode Styles */
        body.dark-mode { background-color: var(--bg-dark-main); color: var(--text-light); }
        .dark-mode .sidebar { background: linear-gradient(180deg, #2c3e50 0%, #1a252f 100%); border-right: 1px solid var(--border-dark); }
        .dark-mode .sidebar-brand { color: var(--text-light); border-bottom-color: rgba(255, 255, 255, 0.15); }
        .dark-mode .sidebar-nav .nav-link { color: rgba(255, 255, 255, 0.7); }
        .dark-mode .sidebar-nav .nav-link:hover, .dark-mode .sidebar-nav .nav-link.active { color: #fff; background-color: rgba(255, 255, 255, 0.12); border-left-color: #fff; }
        .dark-mode .topbar { background-color: var(--bg-dark-surface); box-shadow: 0 0.15rem 1.75rem 0 rgba(0,0,0, 0.25); color: var(--text-light); }
        .dark-mode .topbar-divider { border-right-color: var(--border-dark); }
        .dark-mode .card { background-color: var(--bg-dark-surface); border-color: var(--border-dark); color: var(--text-light); }
        .dark-mode .card-header { background-color: var(--bg-dark-header); border-bottom-color: var(--border-dark); color: var(--text-light); }
        .dark-mode .text-gray-600 { color: #adb5bd !important; }
        .dark-mode .text-gray-800 { color: #e9ecef !important; } /* Adjusted for better visibility */
        .dark-mode .info-card-text { color: #adb5bd; }
        .dark-mode .info-card-number { color: var(--text-light); }
        .dark-mode .footer { background-color: var(--bg-dark-surface); color: #adb5bd; border-top: 1px solid var(--border-dark); }
        .dark-mode .form-control { background-color: #333; color: #fff; border-color: #555; }
        .dark-mode .form-control::placeholder { color: #aaa; }
        .dark-mode .input-group-text { background-color: #444; color: #fff; border-color: #555; }
        .dark-mode .dropdown-menu { background-color: #2c2f33; border-color: var(--border-dark); }
        .dark-mode .dropdown-item { color: var(--text-light); }
        .dark-mode .dropdown-item:hover, .dark-mode .dropdown-item:focus { background-color: #40444b; color: #ffffff; }
        .dark-mode .dropdown-divider { border-top-color: var(--border-dark); }
        .dark-mode .btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); } /* Ensure buttons are styled for dark mode if needed */
        .dark-mode .btn-primary:hover { background-color: #2e59d9; border-color: #2653d4; }
        .dark-mode .btn-link { color: var(--accent-color); }
        .dark-mode .form-check-input { background-color: #454d55; border-color: rgba(255,255,255,0.25); }
        .dark-mode .form-check-input:checked { background-color: var(--primary-color); border-color: var(--primary-color); }
        .dark-mode .form-check-label { color: var(--text-light); }


        @media (max-width: 768px) { /* Styles for when sidebar is collapsed but icons visible */
            .sidebar { width: 100px; overflow: hidden; }
            .sidebar-brand { justify-content: center; padding: 1.5rem 0.5rem; }
            .sidebar-brand span { display: none; }
            .sidebar-nav .nav-link span { display: none; }
            .sidebar-nav .nav-link { justify-content: center; padding: 1rem 0.5rem; }
            .sidebar-nav .nav-link i { margin-right: 0; font-size: 1.2rem; }
            .content { margin-left: 100px; }
        }
        @media (max-width: 576px) { /* Styles for when sidebar can be completely hidden/toggled */
            .sidebar { width: 0; }
            .content { margin-left: 0; }
            .sidebar.active { width: 250px; }
            .content.sidebar-active { margin-left: 250px; }
        }
    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand"><i class="fas fa-user-shield me-2"></i><span>SecurePortal</span></div>
        <ul class="sidebar-nav">
            <li class="nav-item"><a class="nav-link active" href="home.php"><i class="fas fa-fw fa-tachometer-alt"></i><span>Dashboard</span></a></li>
            <div class="sidebar-divider"></div>
            <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-fw fa-chart-area"></i><span>Analytics</span></a></li>
            <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-fw fa-folder"></i><span>Projects</span></a></li>
            <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-fw fa-calendar"></i><span>Calendar</span></a></li>
            <div class="sidebar-divider"></div>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <li class="nav-item"><a class="nav-link" href="admin_panel.php"><i class="fas fa-fw fa-user-shield"></i><span>Admin Panel</span></a></li>
            <?php endif; ?>
            <li class="nav-item"><a class="nav-link" href="profile.php"><i class="fas fa-fw fa-cog"></i><span>Profile Settings</span></a></li>
            <div class="sidebar-divider"></div>
            <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-fw fa-sign-out-alt"></i><span>Logout</span></a></li>
        </ul>
    </div>

    <div class="content" id="content">
        <div class="topbar">
            <button class="btn btn-link d-md-none" id="sidebarToggle"><i class="fas fa-bars"></i></button>
            <form class="d-none d-sm-inline-block form-inline ms-auto me-0 my-2 my-md-0 mw-100 navbar-search w-50">
                <div class="input-group"><input type="text" class="form-control" placeholder="Search for..."><button class="btn btn-primary" type="button"><i class="fas fa-search"></i></button></div>
            </form>
            <div class="ms-auto d-flex align-items-center">
                <div class="form-check form-switch me-3">
                    <input class="form-check-input" type="checkbox" id="darkModeToggle" title="Toggle dark mode">
                    <label class="form-check-label" for="darkModeToggle"><i class="fas fa-moon"></i></label>
                </div>
                <a href="#" class="btn btn-link position-relative text-decoration-none me-2"><i class="fas fa-bell fa-fw"></i><span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3+</span></a>
                <a href="#" class="btn btn-link text-decoration-none me-2"><i class="fas fa-envelope fa-fw"></i></a>
                <div class="topbar-divider d-none d-sm-block"></div>
                <div class="dropdown">
                    <a class="btn btn-link dropdown-toggle text-decoration-none" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="d-none d-lg-inline text-gray-600 small me-2"><?php echo htmlspecialchars($username); ?></span>
                        <img class="rounded-circle" src="https://ui-avatars.com/api/?name=<?php echo urlencode($username); ?>&background=random&color=fff" width="32" height="32">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-cog fa-sm fa-fw me-2 text-gray-400"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cogs fa-sm fa-fw me-2 text-gray-400"></i> Settings (App)</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-list fa-sm fa-fw me-2 text-gray-400"></i> Activity Log</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="main-content-area">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
            </div>
            <div class="row">
                 <div class="col-xl-3 col-md-6 mb-4"><div class="card info-card primary h-100"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="info-card-text text-primary">Earnings (Monthly)</div><div class="info-card-number mb-0">$40,000</div></div><div class="col-auto"><i class="fas fa-calendar fa-2x info-card-icon"></i></div></div></div></div></div>
                <div class="col-xl-3 col-md-6 mb-4"><div class="card info-card success h-100"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="info-card-text text-success">Earnings (Annual)</div><div class="info-card-number mb-0">$215,000</div></div><div class="col-auto"><i class="fas fa-dollar-sign fa-2x info-card-icon"></i></div></div></div></div></div>
                <div class="col-xl-3 col-md-6 mb-4"><div class="card info-card warning h-100"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="info-card-text text-warning">Tasks</div><div class="info-card-number mb-0">50%</div></div><div class="col-auto"><i class="fas fa-clipboard-list fa-2x info-card-icon"></i></div></div></div></div></div>
                <div class="col-xl-3 col-md-6 mb-4"><div class="card info-card danger h-100"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="info-card-text text-danger">Pending Requests</div><div class="info-card-number mb-0">18</div></div><div class="col-auto"><i class="fas fa-comments fa-2x info-card-icon"></i></div></div></div></div></div>
            </div>
            <div class="row">
                <div class="col-lg-8 mb-4"><div class="card shadow"><div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Earnings Overview</h6></div><div class="card-body"><div class="text-center p-5"><i class="fas fa-chart-area fa-3x text-gray-300 mb-3"></i><h5>Chart Area Placeholder</h5></div></div></div></div>
                <div class="col-lg-4 mb-4"><div class="card shadow"><div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Revenue Sources</h6></div><div class="card-body"><div class="text-center p-5"><i class="fas fa-chart-pie fa-3x text-gray-300 mb-3"></i><h5>Pie Chart Placeholder</h5></div></div></div></div>
            </div>
        </div>
        <footer class="footer"><div class="container-fluid"><div class="row"><div class="col-md-6 text-center text-md-start"><small>&copy; <?php echo date("Y"); ?> SecurePortal. All rights reserved.</small></div><div class="col-md-6 text-center text-md-end"><small><a href="#" class="text-muted me-3">Privacy Policy</a><a href="#" class="text-muted">Terms of Service</a></small></div></div></div></footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar Toggle
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('content');
        const sidebarToggle = document.getElementById('sidebarToggle');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
                content.classList.toggle('sidebar-active');
            });
        }
        // Responsive sidebar adjustments
        function adjustSidebar() {
            if (window.innerWidth <= 768 && !sidebar.classList.contains('active')) {
                sidebar.style.width = '100px'; // Collapsed width for tablet
                content.style.marginLeft = '100px';
            } else if (window.innerWidth <= 576 && !sidebar.classList.contains('active')) {
                sidebar.style.width = '0'; // Hidden for mobile
                content.style.marginLeft = '0';
            } else if (sidebar.classList.contains('active')) {
                 sidebar.style.width = '250px'; // Full width when active
                 content.style.marginLeft = '250px';
            }
             else { // Default for larger screens or when active
                sidebar.style.width = '250px';
                content.style.marginLeft = '250px';
            }
        }
        window.addEventListener('resize', adjustSidebar);
        document.addEventListener('DOMContentLoaded', adjustSidebar); // Adjust on load

        // Dark Mode Toggle
        const darkModeToggle = document.getElementById('darkModeToggle');
        const body = document.body;
        const moonIcon = '<i class="fas fa-moon"></i>';
        const sunIcon = '<i class="fas fa-sun"></i>';
        const toggleLabel = document.querySelector('label[for="darkModeToggle"]');

        function applyDarkMode(isDark) {
            if (isDark) {
                body.classList.add('dark-mode');
                if(toggleLabel) toggleLabel.innerHTML = sunIcon;
                localStorage.setItem('darkMode', 'enabled');
            } else {
                body.classList.remove('dark-mode');
                if(toggleLabel) toggleLabel.innerHTML = moonIcon;
                localStorage.setItem('darkMode', 'disabled');
            }
        }

        darkModeToggle.addEventListener('change', function() {
            applyDarkMode(this.checked);
        });

        // Load preference on page load
        document.addEventListener('DOMContentLoaded', function() {
            const userPreference = localStorage.getItem('darkMode');
            if (userPreference === 'enabled') {
                darkModeToggle.checked = true;
                applyDarkMode(true);
            } else {
                darkModeToggle.checked = false;
                applyDarkMode(false);
            }
        });
    </script>
</body>
</html>
