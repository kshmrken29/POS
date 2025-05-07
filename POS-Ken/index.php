<?php
// Start session
session_start();

// Check if user is already logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['user_type'])) {
    // Redirect based on user type
    if ($_SESSION['user_type'] == 'admin') {
        header('Location: admin/index.php');
    } else {
        header('Location: cashier/index.php');
    }
    exit;
} else {
    // Redirect to login page
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Menu Management</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .nav-link {
      font-weight: 500;
    }
    .navbar-brand {
      font-weight: bold;
    }
    .container {
      margin-top: 50px;
    }
  </style>
</head>
<body>

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Restaurant POS - Admin</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="adminNavbar">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="menu-management.html">Menu Management</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="input-daily-menu.html">Input Daily Menu</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="edit-menu-details.html">Edit Menu Details</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="monitor-menu-sales.html">Monitor Menu Sales</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="sales-reporting.html">Sales Reporting</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="manage-cashier.html">Manage Cashier</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container text-center">
    <h1 class="mt-5">Welcome to the Admin Dashboard</h1>
    <p>Select a menu item from the navigation bar to begin managing the restaurant system.</p>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
