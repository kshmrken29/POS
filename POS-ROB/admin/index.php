<?php
// Include authentication system
require_once '../auth_session.php';
require_admin();

// Log that admin dashboard was accessed
log_activity('accessed admin dashboard');

include 'connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>

  <div class="navbar">
    <div class="navbar-container">
      <a class="navbar-brand" href="index.php">Restaurant POS - Admin</a>
      <ul class="navbar-menu">
        <li class="nav-item"><a class="nav-link active" href="index.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="process-void-requests.php">Process Void Requests</a></li>
        <li class="nav-item"><a class="nav-link" href="../cashier/index.php">Cashier Panel</a></li>
        <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
      </ul>
    </div>
  </div>

  <div class="container">
    <h1 class="text-center mb-4">Admin Dashboard</h1>
    
    <!-- User welcome message -->
    <div class="alert alert-info">
      Welcome back, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>! 
      Your last login was: 
      <?php
        // Get last login time
        $user_id = $_SESSION['user_id'];
        $last_login_query = "SELECT last_login FROM users WHERE id = $user_id";
        $last_login_result = mysqli_query($conn, $last_login_query);
        if ($last_login_result && mysqli_num_rows($last_login_result) > 0) {
          $last_login = mysqli_fetch_assoc($last_login_result)['last_login'];
          echo $last_login ? date('F j, Y, g:i a', strtotime($last_login)) : 'First login';
        } else {
          echo 'Unknown';
        }
      ?>
    </div>
    
    <!-- Menu Management Section -->
    <h2 class="mb-4">Menu Management</h2>
    
    <div class="card-grid">
      <div class="card feature-card">
        <h3 class="card-title">Input Daily Menu</h3>
        <p>Add new menu items with cost, servings, and pricing information.</p>
        <a href="MenuManagement/input-daily-menu.php" class="btn btn-primary">Go to Input Daily Menu</a>
      </div>
      
      <div class="card feature-card">
        <h3 class="card-title">Edit Menu Details</h3>
        <p>Modify existing menu items including pricing and servings.</p>
        <a href="MenuManagement/edit-menu-details.php" class="btn btn-primary">Go to Edit Menu</a>
      </div>
      
      <div class="card feature-card">
        <h3 class="card-title">Monitor Menu Sales</h3>
        <p>Track menu item performance and update sales data.</p>
        <a href="MenuManagement/monitor-menu-sales.php" class="btn btn-primary">Go to Monitor Sales</a>
      </div>
    </div>
    
    <!-- Inventory Management Section -->
    <h2 class="mb-4 mt-4">Inventory Management</h2>
    
    <div class="card-grid">
      <div class="card feature-card">
        <h3 class="card-title">Input Purchase Details</h3>
        <p>Record new inventory purchases with quantities and costs.</p>
        <a href="InventoryManagement/input-purchase-details.php" class="btn btn-success">Go to Purchase Details</a>
      </div>
      
      <div class="card feature-card">
        <h3 class="card-title">Input Daily Usage</h3>
        <p>Track daily inventory usage for accurate stock management.</p>
        <a href="InventoryManagement/input-daily-usage.php" class="btn btn-success">Go to Daily Usage</a>
      </div>
      
      <div class="card feature-card">
        <h3 class="card-title">Remaining Stock View</h3>
        <p>View current inventory levels and track stock movement.</p>
        <a href="InventoryManagement/remaining-stock-view.php" class="btn btn-success">Go to Stock View</a>
      </div>
    </div>
    
    <!-- <div class="mt-4 text-center">
      <a href="InventoryManagement/setup_tables.php" class="btn btn-secondary">Setup Inventory Tables</a>
    </div> -->
    
    <!-- Additional features -->
    <h2 class="mb-4 mt-4">Additional Features</h2>
    
    <div class="card-grid">
      <div class="card feature-card">
        <h3 class="card-title">Sales Reporting</h3>
        <p>View detailed sales reports and analytics for your restaurant.</p>
        <a href="./MenuManagement/sales-reporting.php" class="btn btn-primary">Go to Sales Reports</a>
      </div>
      
      <div class="card feature-card">
        <h3 class="card-title">Manage Cashiers</h3>
        <p>Add, edit, or remove cashier accounts for your POS system.</p>
        <a href="./MenuManagement/manage-cashier.php" class="btn btn-primary">Go to Manage Cashiers</a>
      </div>
    </div>
  </div>

</body>
</html>
