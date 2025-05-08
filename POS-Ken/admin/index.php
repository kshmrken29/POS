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
  <title>Admin Dashboard</title>
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
    .feature-card {
      transition: transform 0.3s ease;
      margin-bottom: 20px;
    }
    .feature-card:hover {
      transform: translateY(-5px);
    }
    .card-icon {
      font-size: 3rem;
      margin-bottom: 15px;
      color: #0d6efd;
    }
    .user-info {
      color: white;
      margin-right: 15px;
    }
  </style>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
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
            <a class="nav-link active" href="index.php">Dashboard</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Menu Management</a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="MenuManagement/input-daily-menu.php">Input Daily Menu</a></li>
              <li><a class="dropdown-item" href="MenuManagement/edit-menu-details.php">Edit Menu Details</a></li>
              <li><a class="dropdown-item" href="MenuManagement/monitor-menu-sales.php">Monitor Menu Sales</a></li>
            </ul>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Inventory Management</a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="InventoryManagement/input-purchase-details.php">Input Purchase Details</a></li>
              <li><a class="dropdown-item" href="InventoryManagement/input-daily-usage.php">Input Daily Usage</a></li>
              <li><a class="dropdown-item" href="InventoryManagement/remaining-stock-view.php">Remaining Stock View</a></li>
            </ul>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="process-void-requests.php">Void Requests</a>
          </li>
          <!-- User account dropdown -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
              <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
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

  <div class="container">
    <h1 class="mb-4 text-center">Restaurant POS Admin Dashboard</h1>
    <p class="text-center mb-5">Welcome to the Restaurant Point of Sale Admin Dashboard. Select a feature below to manage your restaurant.</p>
    
    <!-- User welcome message -->
    <div class="alert alert-info mb-4">
      <i class="bi bi-info-circle"></i> 
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
    
    <div class="row">
      <!-- Menu Management Section -->
      <div class="col-12">
        <h3 class="mb-3">Menu Management</h3>
      </div>
      
      <div class="col-md-4">
        <div class="card feature-card text-center">
          <div class="card-body">
            <i class="bi bi-plus-circle card-icon"></i>
            <h5 class="card-title">Input Daily Menu</h5>
            <p class="card-text">Add new menu items with cost, servings, and pricing information.</p>
            <a href="MenuManagement/input-daily-menu.php" class="btn btn-primary">Go to Input Daily Menu</a>
          </div>
        </div>
      </div>
      
      <div class="col-md-4">
        <div class="card feature-card text-center">
          <div class="card-body">
            <i class="bi bi-pencil-square card-icon"></i>
            <h5 class="card-title">Edit Menu Details</h5>
            <p class="card-text">Modify existing menu items including pricing and servings.</p>
            <a href="MenuManagement/edit-menu-details.php" class="btn btn-primary">Go to Edit Menu</a>
          </div>
        </div>
      </div>
      
      <div class="col-md-4">
        <div class="card feature-card text-center">
          <div class="card-body">
            <i class="bi bi-graph-up card-icon"></i>
            <h5 class="card-title">Monitor Menu Sales</h5>
            <p class="card-text">Track menu item performance and update sales data.</p>
            <a href="MenuManagement/monitor-menu-sales.php" class="btn btn-primary">Go to Monitor Sales</a>
          </div>
        </div>
      </div>
      
      <!-- Inventory Management Section -->
      <div class="col-12 mt-5">
        <h3 class="mb-3">Inventory Management</h3>
      </div>
      
      <div class="col-md-4">
        <div class="card feature-card text-center">
          <div class="card-body">
            <i class="bi bi-cart-plus card-icon"></i>
            <h5 class="card-title">Input Purchase Details</h5>
            <p class="card-text">Record new inventory purchases with quantities and costs.</p>
            <a href="InventoryManagement/input-purchase-details.php" class="btn btn-success">Go to Purchase Details</a>
          </div>
        </div>
      </div>
      
      <div class="col-md-4">
        <div class="card feature-card text-center">
          <div class="card-body">
            <i class="bi bi-clipboard-data card-icon"></i>
            <h5 class="card-title">Input Daily Usage</h5>
            <p class="card-text">Track daily inventory usage for accurate stock management.</p>
            <a href="InventoryManagement/input-daily-usage.php" class="btn btn-success">Go to Daily Usage</a>
          </div>
        </div>
      </div>
      
      <div class="col-md-4">
        <div class="card feature-card text-center">
          <div class="card-body">
            <i class="bi bi-boxes card-icon"></i>
            <h5 class="card-title">Remaining Stock View</h5>
            <p class="card-text">View current inventory levels and track stock movement.</p>
            <a href="InventoryManagement/remaining-stock-view.php" class="btn btn-success">Go to Stock View</a>
          </div>
        </div>
      </div>
      
    
      
      <!-- Additional features -->
      <div class="col-12 mt-5">
        <h3 class="mb-3">Additional Features</h3>
      </div>
      
      <div class="col-md-6">
        <div class="card feature-card text-center">
          <div class="card-body">
            <i class="bi bi-file-earmark-bar-graph card-icon"></i>
            <h5 class="card-title">Sales Reporting</h5>
            <p class="card-text">View detailed sales reports and analytics for your restaurant.</p>
            <a href="./MenuManagement/sales-reporting.php" class="btn btn-primary">Go to Sales Reports</a>
          </div>
        </div>
      </div>
      
      <div class="col-md-6">
        <div class="card feature-card text-center">
          <div class="card-body">
            <i class="bi bi-people card-icon"></i>
            <h5 class="card-title">Manage Cashiers</h5>
            <p class="card-text">Add, edit, or remove cashier accounts for your POS system.</p>
            <a href="./MenuManagement/manage-cashier.php" class="btn btn-primary">Go to Manage Cashiers</a>
          </div>
        </div>
      </div>
    </div>
    
    <?php
    // Include database connection to show quick stats
    include '../connection.php';
    
    // Get today's date
    $today = date('Y-m-d');
    
    // Get quick stats
    $menu_count_query = "SELECT COUNT(*) as count FROM menu_items";
    $menu_count_result = mysqli_query($conn, $menu_count_query);
    $menu_count = mysqli_fetch_assoc($menu_count_result)['count'];
    
    $today_menu_query = "SELECT COUNT(*) as count FROM menu_items WHERE date_added = '$today'";
    $today_menu_result = mysqli_query($conn, $today_menu_query);
    $today_menu_count = mysqli_fetch_assoc($today_menu_result)['count'];
    
    $sales_query = "SELECT SUM(servings_sold * price_per_serve) as total FROM menu_items";
    $sales_result = mysqli_query($conn, $sales_query);
    $total_sales = mysqli_fetch_assoc($sales_result)['total'] ?: 0;
    
    $cashier_count_query = "SELECT COUNT(*) as count FROM cashiers";
    $cashier_count_result = mysqli_query($conn, $cashier_count_query);
    $cashier_count = mysqli_fetch_assoc($cashier_count_result)['count'];
    
    // Get inventory stats
    $inventory_count = 0;
    $low_stock_count = 0;
    
    // Check if tables exist by trying to describe them (safer method)
    $tables_exist = true;
    
    try {
        // Try to get inventory item count
        $inventory_count_query = "SELECT COUNT(*) as count FROM inventory_items";
        $inventory_count_result = mysqli_query($conn, $inventory_count_query);
        if ($inventory_count_result) {
            $inventory_count = mysqli_fetch_assoc($inventory_count_result)['count'];
        } else {
            $tables_exist = false;
        }
    } catch (Exception $e) {
        $tables_exist = false;
    }
    
    // Only try to calculate low stock if tables exist
    if ($tables_exist) {
        try {
            // Use a simpler, safer query to get low stock count
            $low_stock_query = "SELECT COUNT(*) as count FROM inventory_items 
                               WHERE id IN (
                                  SELECT item_id FROM (
                                    SELECT 
                                      item_id,
                                      SUM(quantity) as total_purchased
                                    FROM 
                                      inventory_purchases
                                    GROUP BY 
                                      item_id
                                  ) as purchases
                                  JOIN (
                                    SELECT 
                                      item_id,
                                      SUM(quantity) as total_used
                                    FROM 
                                      inventory_usage
                                    GROUP BY 
                                      item_id
                                  ) as usage_table
                                  ON purchases.item_id = usage_table.item_id
                                  WHERE (purchases.total_purchased - usage_table.total_used) < 10
                               )";
            $low_stock_result = mysqli_query($conn, $low_stock_query);
            if ($low_stock_result) {
                $low_stock_count = mysqli_fetch_assoc($low_stock_result)['count'];
            }
        } catch (Exception $e) {
            // Silently fail if there's an error
        }
    }
    ?>
    
    <div class="row mt-5">
      <div class="col-12">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Quick Statistics</h4>
          </div>
          <div class="card-body">
            <div class="row text-center">
              <div class="col-md-2">
                <h3><?php echo $menu_count; ?></h3>
                <p>Total Menu Items</p>
              </div>
              <div class="col-md-2">
                <h3><?php echo $today_menu_count; ?></h3>
                <p>Today's Menu Items</p>
              </div>
              <div class="col-md-2">
                <h3>$<?php echo number_format($total_sales, 2); ?></h3>
                <p>Total Sales</p>
              </div>
              <div class="col-md-2">
                <h3><?php echo $cashier_count; ?></h3>
                <p>Cashiers</p>
              </div>
              <div class="col-md-2">
                <h3><?php echo $inventory_count; ?></h3>
                <p>Inventory Items</p>
              </div>
              <div class="col-md-2">
                <h3><?php echo $low_stock_count; ?></h3>
                <p>Low Stock Items</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Activity log section -->
    <div class="row mt-5">
      <div class="col-12">
        <h3 class="mb-3">Recent User Activity</h3>
        <div class="card">
          <div class="card-body">
            <?php
            // Function to get recent user logins
            function get_recent_logins($conn, $limit = 5) {
              $query = "SELECT username, user_type, last_login FROM users ORDER BY last_login DESC LIMIT $limit";
              $result = mysqli_query($conn, $query);
              
              if ($result && mysqli_num_rows($result) > 0) {
                echo '<h5>Recent Logins</h5>';
                echo '<ul class="list-group">';
                while ($row = mysqli_fetch_assoc($result)) {
                  echo '<li class="list-group-item">';
                  echo '<i class="bi bi-person-check"></i> ';
                  echo '<strong>' . htmlspecialchars($row['username']) . '</strong> ';
                  echo '(' . $row['user_type'] . ') logged in at ';
                  echo date('F j, Y, g:i a', strtotime($row['last_login']));
                  echo '</li>';
                }
                echo '</ul>';
              } else {
                echo '<p>No recent login activity to display.</p>';
              }
            }
            
            // Display recent logins
            get_recent_logins($conn);
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
