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
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../styles/main.css">
</head>
<body>

  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">
        <img src="../styles/logo.svg" alt="Logo" width="30" height="30" class="d-inline-block align-text-top me-2">
        Restaurant POS
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="adminNavbar">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link active" href="index.php">
              <i class="bi bi-speedometer2 me-1"></i> Dashboard
            </a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
              <i class="bi bi-menu-button-wide me-1"></i> Menu Management
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="MenuManagement/input-daily-menu.php">Input Daily Menu</a></li>
              <li><a class="dropdown-item" href="MenuManagement/edit-menu-details.php">Edit Menu Details</a></li>
              <li><a class="dropdown-item" href="MenuManagement/monitor-menu-sales.php">Monitor Menu Sales</a></li>
            </ul>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
              <i class="bi bi-box-seam me-1"></i> Inventory
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="InventoryManagement/input-purchase-details.php">Input Purchase Details</a></li>
              <li><a class="dropdown-item" href="InventoryManagement/input-daily-usage.php">Input Daily Usage</a></li>
              <li><a class="dropdown-item" href="InventoryManagement/remaining-stock-view.php">Remaining Stock View</a></li>
            </ul>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="sales-reporting.php">
              <i class="bi bi-graph-up me-1"></i> Reports
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="manage-cashier.php">
              <i class="bi bi-people me-1"></i> Users
            </a>
          </li>
          <!-- User account dropdown -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
              <div class="user-info">
                <div class="user-avatar">
                  <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                </div>
                <span class="d-none d-sm-inline-block"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
              </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="../cashier/index.php"><i class="bi bi-arrow-left-right me-2"></i>Switch to Cashier</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="../logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
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
              <div class="d-flex align-items-center mb-3">
                <div class="rounded-circle p-2 me-3" style="background-color: rgba(52, 152, 219, 0.1);">
                  <i class="bi bi-menu-button text-info fs-4"></i>
                </div>
                <h6 class="mb-0">Menu Items</h6>
              </div>
              <div class="stats-value"><?php echo number_format($menu_count); ?></div>
              <div class="stats-label">Total menu items</div>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
          <div class="card stats-card">
            <div class="card-body">
              <div class="d-flex align-items-center mb-3">
                <div class="rounded-circle p-2 me-3" style="background-color: rgba(22, 160, 133, 0.1);">
                  <i class="bi bi-plus-circle text-secondary fs-4"></i>
                </div>
                <h6 class="mb-0">Today's Menu</h6>
              </div>
              <div class="stats-value"><?php echo number_format($today_menu_count); ?></div>
              <div class="stats-label">Added today</div>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
          <div class="card stats-card">
            <div class="card-body">
              <div class="d-flex align-items-center mb-3">
                <div class="rounded-circle p-2 me-3" style="background-color: rgba(46, 204, 113, 0.1);">
                  <i class="bi bi-currency-dollar text-success fs-4"></i>
                </div>
                <h6 class="mb-0">Total Sales</h6>
              </div>
              <div class="stats-value">$<?php echo number_format($total_sales, 2); ?></div>
              <div class="stats-label">All time</div>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
          <div class="card stats-card">
            <div class="card-body">
              <div class="d-flex align-items-center mb-3">
                <div class="rounded-circle p-2 me-3" style="background-color: rgba(155, 89, 182, 0.1);">
                  <i class="bi bi-person-check text-accent fs-4"></i>
                </div>
                <h6 class="mb-0">Last Login</h6>
              </div>
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
                    echo date('M j, Y', strtotime($last_login));
                  } else {
                    echo '&nbsp;';
                  }
                ?>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Feature Sections -->
      <div class="row">
        <div class="col-12 mb-3">
          <h4><i class="bi bi-menu-button-wide text-secondary me-2"></i> Menu Management</h4>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card feature-card h-100">
            <div class="card-body d-flex flex-column">
              <div class="mb-3">
                <i class="bi bi-plus-circle-dotted card-icon"></i>
                <h5 class="card-title">Input Daily Menu</h5>
                <p class="card-text">Add today's menu items with cost, servings, and pricing information.</p>
              </div>
              <div class="mt-auto">
                <a href="MenuManagement/input-daily-menu.php" class="btn btn-outline-secondary">Add Menu Items</a>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card feature-card h-100">
            <div class="card-body d-flex flex-column">
              <div class="mb-3">
                <i class="bi bi-pencil-square card-icon"></i>
                <h5 class="card-title">Edit Menu Details</h5>
                <p class="card-text">Modify existing menu items including pricing and available servings.</p>
              </div>
              <div class="mt-auto">
                <a href="MenuManagement/edit-menu-details.php" class="btn btn-outline-secondary">Edit Menu</a>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card feature-card h-100">
            <div class="card-body d-flex flex-column">
              <div class="mb-3">
                <i class="bi bi-graph-up card-icon"></i>
                <h5 class="card-title">Monitor Menu Sales</h5>
                <p class="card-text">Track menu item performance and update sales information.</p>
              </div>
              <div class="mt-auto">
                <a href="MenuManagement/monitor-menu-sales.php" class="btn btn-outline-secondary">View Sales</a>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="row">
        <div class="col-12 mb-3">
          <h4><i class="bi bi-box-seam text-secondary me-2"></i> Inventory Management</h4>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card feature-card h-100">
            <div class="card-body d-flex flex-column">
              <div class="mb-3">
                <i class="bi bi-cart-plus card-icon"></i>
                <h5 class="card-title">Input Purchase Details</h5>
                <p class="card-text">Record new inventory purchases with quantities and costs.</p>
              </div>
              <div class="mt-auto">
                <a href="InventoryManagement/input-purchase-details.php" class="btn btn-outline-secondary">Record Purchases</a>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card feature-card h-100">
            <div class="card-body d-flex flex-column">
              <div class="mb-3">
                <i class="bi bi-clipboard-data card-icon"></i>
                <h5 class="card-title">Input Daily Usage</h5>
                <p class="card-text">Track daily inventory usage for accurate stock management.</p>
              </div>
              <div class="mt-auto">
                <a href="InventoryManagement/input-daily-usage.php" class="btn btn-outline-secondary">Record Usage</a>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card feature-card h-100">
            <div class="card-body d-flex flex-column">
              <div class="mb-3">
                <i class="bi bi-boxes card-icon"></i>
                <h5 class="card-title">Remaining Stock View</h5>
                <p class="card-text">View current inventory levels and monitor stock movements.</p>
              </div>
              <div class="mt-auto">
                <a href="InventoryManagement/remaining-stock-view.php" class="btn btn-outline-secondary">View Stock</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-12 mb-3">
          <h4><i class="bi bi-gear text-secondary me-2"></i> System Management</h4>
        </div>
        <div class="col-md-6 mb-4">
          <div class="card feature-card h-100">
            <div class="card-body d-flex flex-column">
              <div class="mb-3">
                <i class="bi bi-file-earmark-bar-graph card-icon"></i>
                <h5 class="card-title">Sales Reporting</h5>
                <p class="card-text">View detailed sales reports and analytics for your restaurant.</p>
              </div>
              <div class="mt-auto">
                <a href="./MenuManagement/sales-reporting.php" class="btn btn-outline-secondary">View Reports</a>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6 mb-4">
          <div class="card feature-card h-100">
            <div class="card-body d-flex flex-column">
              <div class="mb-3">
                <i class="bi bi-people card-icon"></i>
                <h5 class="card-title">Manage Cashiers</h5>
                <p class="card-text">Add, edit, or remove cashier accounts for your POS system.</p>
              </div>
              <div class="mt-auto">
                <a href="./MenuManagement/manage-cashier.php" class="btn btn-outline-secondary">Manage Users</a>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="row">
        <div class="col-12 text-center mt-3">
          <p class="text-muted mb-0">System setup</p>
          <a href="InventoryManagement/setup_tables.php" class="btn btn-sm btn-outline-primary mt-2">Initialize Database Tables</a>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
