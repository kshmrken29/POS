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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../styles/main.css">
</head>
<body>

  <nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">
        Restaurant POS
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="adminNavbar">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link active" href="index.php">
              Dashboard
            </a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
              Menu Management
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="MenuManagement/input-daily-menu.php">Input Daily Menu</a></li>
              <li><a class="dropdown-item" href="MenuManagement/edit-menu-details.php">Edit Menu Details</a></li>
              <li><a class="dropdown-item" href="MenuManagement/monitor-menu-sales.php">Monitor Menu Sales</a></li>
            </ul>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
              Inventory
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="InventoryManagement/input-purchase-details.php">Input Purchase Details</a></li>
              <li><a class="dropdown-item" href="InventoryManagement/input-daily-usage.php">Input Daily Usage</a></li>
              <li><a class="dropdown-item" href="InventoryManagement/remaining-stock-view.php">Remaining Stock View</a></li>
            </ul>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="sales-reporting.php">
              Reports
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="manage-cashier.php">
              Users
            </a>
          </li>
          <!-- User account dropdown -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
              <?php echo htmlspecialchars($_SESSION['username']); ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="../cashier/index.php">Switch to Cashier</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container-fluid py-4 px-4">
    <div class="main-container">
      <div class="row mb-4">
        <div class="col-12">
          <h2 class="page-header">Admin Dashboard</h2>
          <p class="text-muted">Welcome to the Restaurant Point of Sale system. Manage your restaurant operations from here.</p>
        </div>
      </div>
      
      <!-- Quick Stats Row -->
      <div class="row mb-4">
        <?php
          // Get today's date
          $today = date('Y-m-d');
          
          // Get menu items count
          $menu_count_query = "SELECT COUNT(*) as count FROM menu_items";
          $menu_count_result = mysqli_query($conn, $menu_count_query);
          $menu_count = 0;
          if ($menu_count_result) {
            $menu_count = mysqli_fetch_assoc($menu_count_result)['count'];
          }
          
          // Get today's menu items
          $today_menu_query = "SELECT COUNT(*) as count FROM menu_items WHERE date_added = '$today'";
          $today_menu_result = mysqli_query($conn, $today_menu_query);
          $today_menu_count = 0;
          if ($today_menu_result) {
            $today_menu_count = mysqli_fetch_assoc($today_menu_result)['count'];
          }
          
          // Calculate total sales
          $sales_query = "SELECT SUM(servings_sold * price_per_serve) as total FROM menu_items";
          $sales_result = mysqli_query($conn, $sales_query);
          $total_sales = 0;
          if ($sales_result) {
            $total_sales = mysqli_fetch_assoc($sales_result)['total'] ?: 0;
          }
        ?>
        <div class="col-lg-3 col-md-6 mb-3">
          <div class="card stats-card">
            <div class="card-body">
              <h6 class="mb-3">Menu Items</h6>
              <div class="stats-value"><?php echo number_format($menu_count); ?></div>
              <div class="stats-label">Total menu items</div>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
          <div class="card stats-card">
            <div class="card-body">
              <h6 class="mb-3">Today's Menu</h6>
              <div class="stats-value"><?php echo number_format($today_menu_count); ?></div>
              <div class="stats-label">Added today</div>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
          <div class="card stats-card">
            <div class="card-body">
              <h6 class="mb-3">Total Sales</h6>
              <div class="stats-value">$<?php echo number_format($total_sales, 2); ?></div>
              <div class="stats-label">All time</div>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
          <div class="card stats-card">
            <div class="card-body">
              <h6 class="mb-3">Last Login</h6>
              <div class="stats-value" style="font-size: 1.1rem;">
                <?php
                  // Get last login time
                  $user_id = $_SESSION['user_id'];
                  $last_login_query = "SELECT last_login FROM users WHERE id = $user_id";
                  $last_login_result = mysqli_query($conn, $last_login_query);
                  if ($last_login_result && mysqli_num_rows($last_login_result) > 0) {
                    $last_login = mysqli_fetch_assoc($last_login_result)['last_login'];
                    echo $last_login ? date('g:i a', strtotime($last_login)) : 'First login';
                  } else {
                    echo 'Unknown';
                  }
                ?>
              </div>
              <div class="stats-label">
                <?php
                  if (isset($last_login) && $last_login) {
                    echo date('M d, Y', strtotime($last_login));
                  } else {
                    echo 'Today';
                  }
                ?>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="row">
        <div class="col-12 mb-3">
          <h4 class="border-bottom pb-2">Menu Management</h4>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card feature-card">
            <div class="card-body">
              <h5 class="card-title">Input Daily Menu</h5>
              <p class="card-text">Add new menu items and set prices for today's menu.</p>
              <a href="MenuManagement/input-daily-menu.php" class="btn btn-outline-secondary">Add Menu Items</a>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card feature-card">
            <div class="card-body">
              <h5 class="card-title">Edit Menu Details</h5>
              <p class="card-text">Modify existing menu items, prices, and availability.</p>
              <a href="MenuManagement/edit-menu-details.php" class="btn btn-outline-secondary">Edit Menu</a>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card feature-card">
            <div class="card-body">
              <h5 class="card-title">Monitor Menu Sales</h5>
              <p class="card-text">Track menu item performance and update sales information.</p>
              <a href="MenuManagement/monitor-menu-sales.php" class="btn btn-outline-secondary">View Sales</a>
            </div>
          </div>
        </div>
      </div>
      
      <div class="row">
        <div class="col-12 mb-3">
          <h4 class="border-bottom pb-2">Inventory Management</h4>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card feature-card">
            <div class="card-body">
              <h5 class="card-title">Input Purchase Details</h5>
              <p class="card-text">Record new inventory purchases with quantities and costs.</p>
              <a href="InventoryManagement/input-purchase-details.php" class="btn btn-outline-secondary">Record Purchases</a>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card feature-card">
            <div class="card-body">
              <h5 class="card-title">Input Daily Usage</h5>
              <p class="card-text">Track daily inventory usage for accurate stock management.</p>
              <a href="InventoryManagement/input-daily-usage.php" class="btn btn-outline-secondary">Record Usage</a>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card feature-card">
            <div class="card-body">
              <h5 class="card-title">Remaining Stock View</h5>
              <p class="card-text">View current inventory levels and monitor stock movements.</p>
              <a href="InventoryManagement/remaining-stock-view.php" class="btn btn-outline-secondary">View Stock</a>
            </div>
          </div>
        </div>
      </div>
      
      <div class="row">
        <div class="col-12 mb-3">
          <h4 class="border-bottom pb-2">System Management</h4>
        </div>
        <div class="col-md-6 mb-4">
          <div class="card feature-card">
            <div class="card-body">
              <h5 class="card-title">Sales Reporting</h5>
              <p class="card-text">View sales reports and analyze business performance.</p>
              <a href="MenuManagement/sales-reporting.php" class="btn btn-outline-secondary">View Reports</a>
            </div>
          </div>
        </div>
        <div class="col-md-6 mb-4">
          <div class="card feature-card">
            <div class="card-body">
              <h5 class="card-title">Manage Users</h5>
              <p class="card-text">Add or edit user accounts and manage permissions.</p>
              <a href="MenuManagement/manage-cashier.php" class="btn btn-outline-secondary">Manage Users</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../styles/scripts.js"></script>
</body>
</html>
