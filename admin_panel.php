<?php
session_start();
include 'connect.php'; // For database connection

// Check if user is logged in and is an admin
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Store an error message in session perhaps, or redirect with a query param
    $_SESSION['error_message'] = "You do not have permission to access the Admin Panel.";
    header("Location: home.php"); // Or index.php if home.php also requires login always
    exit();
}

$users = [];
$fetch_error = '';

$sql_get_users = "SELECT id, username, role, is_verified FROM registration ORDER BY id ASC";
$result_get_users = mysqli_query($conn, $sql_get_users);

if ($result_get_users) {
    while ($row = mysqli_fetch_assoc($result_get_users)) {
        $users[] = $row;
    }
} else {
    $fetch_error = "Error fetching users: " . mysqli_error($conn);
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - SecurePortal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fc;
        }
        .admin-container {
            margin: 20px;
            padding: 2rem;
            background-color: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        .admin-header {
            margin-bottom: 1.5rem;
            color: #4e73df;
        }
        .table th {
            background-color: #f2f2f2; /* Light grey for table headers */
        }
        .badge-role-admin {
            background-color: var(--primary-color, #4e73df);
            color: white;
        }
        .badge-role-user {
            background-color: var(--secondary-color, #858796);
            color: white;
        }
         /* Assuming a similar navbar might be included */
        .topbar { /* Minimal styling if navbar is not there or separate */
            height: 70px;
            background-color: white;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(90, 92, 105, 0.15);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <?php
    // Attempt to include a common navbar, if it exists from home.php or profile.php structure
    // This path might need adjustment or the navbar code might be directly in home.php
    // For now, let's assume there might be a 'home_navbar.php' or similar for consistency.
    // If not, this part can be removed or replaced with a simpler static navbar.
    // include 'home_navbar.php';
    ?>
     <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="home.php" style="color: #4e73df; font-weight: bold;">
                <i class="fas fa-user-shield me-2"></i>SecurePortal Admin
            </a>
            <div class="ms-auto">
                <span class="navbar-text me-3">
                    Logged in as: <strong><?php echo htmlspecialchars($_SESSION['username']); ?> (Admin)</strong>
                </span>
                <a href="home.php" class="btn btn-sm btn-outline-secondary me-2"><i class="fas fa-tachometer-alt me-1"></i>Dashboard</a>
                <a href="profile.php" class="btn btn-sm btn-outline-primary me-2"><i class="fas fa-user-cog me-1"></i>Profile</a>
                <a href="logout.php" class="btn btn-sm btn-danger"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
            </div>
        </div>
    </nav>


    <div class="container-fluid admin-container">
        <header class="admin-header">
            <h2><i class="fas fa-users-cog me-2"></i>User Management</h2>
        </header>

        <?php if ($fetch_error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($fetch_error); ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Username (Email)</th>
                        <th>Role</th>
                        <th>Verified</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users) && !$fetch_error): ?>
                        <tr>
                            <td colspan="5" class="text-center">No users found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td>
                                    <span class="badge <?php echo ($user['role'] === 'admin' ? 'bg-primary' : 'bg-secondary'); ?>">
                                        <?php echo htmlspecialchars(ucfirst($user['role'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($user['is_verified'] == 1): ?>
                                        <span class="badge bg-success">Yes</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">No</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <!-- Placeholder for actions like edit role, delete user etc. -->
                                    <a href="#" class="btn btn-sm btn-outline-primary disabled" title="Edit User (Feature not implemented)">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-outline-danger disabled" title="Delete User (Feature not implemented)">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
         <p class="mt-3 text-muted">
            Note: Username is treated as the user's email address throughout the system.
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
