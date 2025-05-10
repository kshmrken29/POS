<?php
// Include authentication system
require_once '../auth_session.php';
require_admin();

// Log that admin dashboard was accessed
log_activity('accessed admin dashboard');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Remaining Stock View</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <style>
    body {
      background-color: #f8f9fa;
      margin: 0;
      padding: 0;
      overflow-x: hidden;
    }
    .sidebar {
      position: fixed;
      top: 0;
      bottom: 0;
      left: 0;
      z-index: 100;
      padding: 48px 0 0;
      box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
      background-color: #212529;
      width: 250px;
    }
    .sidebar-sticky {
      height: 100vh;
      overflow-x: hidden;
      overflow-y: auto;
    }
    .sidebar .nav-link {
      font-weight: 500;
      color: #adb5bd;
      padding: 0.75rem 1rem;
      margin-bottom: 0.25rem;
    }
    .sidebar .nav-link:hover {
      color: #fff;
    }
    .sidebar .nav-link.active {
      color: #fff;
      background-color: rgba(255, 255, 255, 0.1);
      border-left: 4px solid #0d6efd;
    }
    .sidebar .nav-link .bi {
      margin-right: 0.5rem;
    }
    .sidebar-heading {
      font-size: .75rem;
      text-transform: uppercase;
      padding: 1rem;
      color: #6c757d;
    }
    .navbar-brand {
      padding: 1rem;
      font-weight: bold;
      color: white;
      text-align: center;
      display: block;
    }
    .main-content {
      margin-left: 250px;
      padding: 20px;
      width: calc(100% - 250px);
    }
    @media (max-width: 767.98px) {
      .sidebar {
        width: 100%;
        height: auto;
        position: relative;
        padding-top: 0;
      }
      .main-content {
        margin-left: 0;
        width: 100%;
      }
    }
    .feature-card {
      transition: transform 0.3s ease;
      margin-bottom: 20px;
      height: 100%;
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
    .card-body {
      display: flex;
      flex-direction: column;
    }
    .card-text {
      flex-grow: 1;
    }
    .row {
      width: 100%;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar col-md-3 col-lg-2 d-md-block bg-dark">
    <div class="position-sticky sidebar-sticky">
      <a href="../dashboard.php" class="navbar-brand">Restaurant POS - Admin</a>
      <hr class="bg-light">
      <ul class="nav flex-column">
        <li class="nav-item">
          <a class="nav-link" href="../dashboard.php">
            <i class="bi bi-speedometer2"></i> Dashboard
          </a>
        </li>
        <li class="sidebar-heading">Menu Management</li>
        <li class="nav-item">
          <a class="nav-link" href="../MenuManagement/input-daily-menu.php">
            <i class="bi bi-plus-circle"></i> Input Daily Menu
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../MenuManagement/edit-menu-details.php">
            <i class="bi bi-pencil-square"></i> Edit Menu Details
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../MenuManagement/monitor-menu-sales.php">
            <i class="bi bi-graph-up"></i> Monitor Menu Sales
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../MenuManagement/sales-reporting.php">
            <i class="bi bi-file-earmark-bar-graph"></i> Sales Reporting
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../MenuManagement/manage-cashier.php">
            <i class="bi bi-person-badge"></i> Manage Cashier
          </a>
        </li>
        <li class="sidebar-heading">Inventory</li>
        <li class="nav-item">
          <a class="nav-link" href="input-purchase-details.php">
            <i class="bi bi-cart-plus"></i> Purchase Details
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="input-daily-usage.php">
            <i class="bi bi-card-checklist"></i> Daily Usage
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="remaining-stock-view.php">
            <i class="bi bi-boxes"></i> Remaining Stock
          </a>
        </li>
        <li class="sidebar-heading">Administration</li>
        <li class="nav-item">
          <a class="nav-link" href="../process-void-requests.php">
            <i class="bi bi-exclamation-triangle"></i> Void Requests
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../logout.php">
            <i class="bi bi-box-arrow-right"></i> Logout
          </a>
        </li>
      </ul>
    </div>
  </div>

  <!-- Main content -->
  <div class="main-content">
    <h2 class="mb-4">Remaining Stock View</h2>

    <?php
    // Include database connection
    include '../connection.php';

    // Initialize variables
    $has_items = false;
    $stock_result = null;

    try {
        // Check if tables exist
        $items_check = mysqli_query($conn, "SHOW TABLES LIKE 'inventory_items'");
        $items_exist = mysqli_num_rows($items_check) > 0;
        
        if ($items_exist) {
            // First check if we have any inventory items
            $count_check = mysqli_query($conn, "SELECT COUNT(*) as count FROM inventory_items");
            $item_count = mysqli_fetch_assoc($count_check)['count'];
            
            if ($item_count > 0) {
                // We have inventory items, get them with their current stock
                $stock_sql = "SELECT 
                            id,
                            item_name,
                            current_stock,
                            description
                        FROM 
                            inventory_items
                        ORDER BY 
                            item_name";
                
                $stock_result = mysqli_query($conn, $stock_sql);
                $has_items = mysqli_num_rows($stock_result) > 0;
            } else {
                $has_items = false;
            }
        } else {
            $has_items = false;
            echo '<div class="alert alert-warning" role="alert">Inventory items table not found. Please run <a href="setup_tables.php">Setup Inventory Tables</a> script first.</div>';
        }
    } catch (Exception $e) {
        // Handle any exception
        $has_items = false;
        echo '<div class="alert alert-danger" role="alert">Error: ' . $e->getMessage() . '</div>';
    }
    
    // Low stock threshold
    $low_stock_threshold = 10; // can be adjusted as needed
    ?>

    <!-- Stock Overview -->
    <div class="card mb-4">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Current Inventory Status</h5>
      </div>
      <div class="card-body">
        <?php if ($has_items): ?>
          <div class="mb-3">
            <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="row g-3">
              <div class="col-md-6">
                <div class="input-group">
                  <input type="text" class="form-control" name="search" placeholder="Search items..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                  <button class="btn btn-outline-secondary" type="submit">Search</button>
                  <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                    <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="btn btn-outline-danger">Clear</a>
                  <?php endif; ?>
                </div>
              </div>
              <div class="col-md-6 text-md-end">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" id="show_low_stock" name="show_low_stock" value="1" <?php echo (isset($_GET['show_low_stock'])) ? 'checked' : ''; ?>>
                  <label class="form-check-label" for="show_low_stock">Show Low Stock Only</label>
                </div>
                <button type="submit" class="btn btn-sm btn-secondary">Apply Filter</button>
              </div>
            </form>
          </div>

          <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead>
                <tr>
                  <th>Item Name</th>
                  <th>Description</th>
                  <th>Current Stock</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $has_matching_items = false;
                $search_term = isset($_GET['search']) ? $_GET['search'] : '';
                $show_low_stock = isset($_GET['show_low_stock']);
                
                // Reset the result pointer
                mysqli_data_seek($stock_result, 0);
                
                while ($item = mysqli_fetch_assoc($stock_result)) {
                  $remaining = $item['current_stock'];
                  $is_low_stock = $remaining <= $low_stock_threshold && $remaining > 0;
                  $is_out_of_stock = $remaining <= 0;
                  
                  // Apply search and low stock filters
                  if ((!empty($search_term) && stripos($item['item_name'], $search_term) === false) ||
                      ($show_low_stock && !$is_low_stock && !$is_out_of_stock)) {
                    continue;
                  }
                  
                  $has_matching_items = true;
                  
                  // Determine row class based on stock level
                  $row_class = $is_out_of_stock ? 'table-danger' : ($is_low_stock ? 'table-warning' : '');
                  
                  echo "<tr class='$row_class'>";
                  echo "<td>" . $item['item_name'] . "</td>";
                  echo "<td>" . $item['description'] . "</td>";
                  echo "<td>" . number_format($item['current_stock'], 2) . "</td>";
                  
                  // Status column
                  echo "<td>";
                  if ($is_out_of_stock) {
                    echo "<span class='badge bg-danger'>Out of Stock</span>";
                  } else if ($is_low_stock) {
                    echo "<span class='badge bg-warning text-dark'>Low Stock</span>";
                  } else {
                    echo "<span class='badge bg-success'>In Stock</span>";
                  }
                  echo "</td>";
                  
                  echo "</tr>";
                }
                
                if (!$has_matching_items) {
                  echo "<tr><td colspan='5' class='text-center'>No matching inventory items found</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
          
          <div class="mt-3">
            <div class="d-flex">
              <div class="me-3">
                <span class="badge bg-danger">&nbsp;</span> Out of Stock
              </div>
              <div class="me-3">
                <span class="badge bg-warning">&nbsp;</span> Low Stock (≤ <?php echo $low_stock_threshold; ?> units)
              </div>
              <div>
                <span class="badge bg-success">&nbsp;</span> In Stock
              </div>
            </div>
          </div>
        <?php else: ?>
          <div class="alert alert-warning">
            No inventory items found. Please <a href="input-purchase-details.php">add inventory items</a> first or run <a href="../../create_tables.php">create_tables.php</a> to set up the database.
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Stock Movement History -->
    <div class="card">
      <div class="card-header bg-info text-white">
        <h5 class="mb-0">Recent Stock Movement</h5>
      </div>
      <div class="card-body">
        <?php if ($has_items): ?>
          <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Item Name</th>
                  <th>Type</th>
                  <th>Quantity</th>
                </tr>
              </thead>
              <tbody>
                <?php
                try {
                    // Check if tables exist
                    $items_check = mysqli_query($conn, "SHOW TABLES LIKE 'inventory_items'");
                    $purchases_check = mysqli_query($conn, "SHOW TABLES LIKE 'inventory_purchases'");
                    $usage_check = mysqli_query($conn, "SHOW TABLES LIKE 'inventory_usage'");
                    
                    $items_exist = mysqli_num_rows($items_check) > 0;
                    $purchases_exist = mysqli_num_rows($purchases_check) > 0;
                    $usage_exist = mysqli_num_rows($usage_check) > 0;
                    
                    // First check if inventory items table exists and has items
                    if ($items_exist) {
                        $count_check = mysqli_query($conn, "SELECT COUNT(*) as count FROM inventory_items");
                        $item_count = mysqli_fetch_assoc($count_check)['count'];
                        
                        if ($item_count > 0) {
                            // If all tables exist, show movement history
                            if ($purchases_exist && $usage_exist) {
                                // Check if we have any movement records
                                $purchases_count = mysqli_query($conn, "SELECT COUNT(*) as count FROM inventory_purchases");
                                $usage_count = mysqli_query($conn, "SELECT COUNT(*) as count FROM inventory_usage");
                                
                                $have_purchases = mysqli_fetch_assoc($purchases_count)['count'] > 0;
                                $have_usage = mysqli_fetch_assoc($usage_count)['count'] > 0;
                                
                                if ($have_purchases || $have_usage) {
                                    // We have some movement records, show them
                                    $movement_sql = "(SELECT 
                                                p.date_purchased as date, 
                                                i.item_name, 
                                                'Purchase' as type, 
                                                p.quantity_purchased as quantity
                                              FROM 
                                                inventory_purchases p
                                              JOIN 
                                                inventory_items i ON p.item_id = i.id)
                                            UNION ALL
                                            (SELECT 
                                                u.date_used as date, 
                                                i.item_name, 
                                                'Usage' as type, 
                                                u.quantity_used as quantity
                                              FROM 
                                                inventory_usage u
                                              JOIN 
                                                inventory_items i ON u.item_id = i.id)
                                            ORDER BY 
                                              date DESC
                                            LIMIT 20";
                                                
                                    $movement_result = mysqli_query($conn, $movement_sql);
                                    
                                    if ($movement_result && mysqli_num_rows($movement_result) > 0) {
                                        while ($row = mysqli_fetch_assoc($movement_result)) {
                                            echo "<tr>";
                                            echo "<td>" . $row['date'] . "</td>";
                                            echo "<td>" . $row['item_name'] . "</td>";
                                            
                                            if ($row['type'] == 'Purchase') {
                                                echo "<td><span class='badge bg-success'>Purchase</span></td>";
                                            } else {
                                                echo "<td><span class='badge bg-danger'>Usage</span></td>";
                                            }
                                            
                                            echo "<td>" . $row['quantity'] . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='4' class='text-center'>No stock movement records found</td></tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4' class='text-center'>No inventory movements recorded yet</td></tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center'>Please run the <a href='setup_tables.php'>Setup Inventory Tables</a> script to create purchase and usage tables</td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center'>No inventory items found. Please add items first.</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' class='text-center'>Inventory items table not found. Please run <a href='setup_tables.php'>Setup Inventory Tables</a> script.</td></tr>";
                    }
                } catch (Exception $e) {
                    echo "<tr><td colspan='4' class='text-center'>Error: " . $e->getMessage() . "</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="alert alert-warning">
            No inventory items found. Please <a href="input-purchase-details.php">add inventory items</a> first.
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div> <!-- End of main-content -->

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
