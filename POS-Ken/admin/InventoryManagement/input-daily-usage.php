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
  <title>Input Daily Usage</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <style>
    body {
      background-color: #f8f9fa;
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
    }
    .sidebar-sticky {
      height: calc(100vh - 48px);
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
      margin-left: 300px;
      padding: 20px;
    }
    @media (max-width: 767.98px) {
      .sidebar {
        width: 100%;
        position: relative;
        padding-top: 0;
      }
      .main-content {
        margin-left: 0;
      }
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
          <a class="nav-link active" href="input-daily-usage.php">
            <i class="bi bi-card-checklist"></i> Daily Usage
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="remaining-stock-view.php">
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
    <h2 class="mb-4">Input Daily Usage</h2>

    <?php
    // Include database connection
    include '../connection.php';

    // Initialize variables
    $has_items = false;
    $items_result = null;

    try {
        // Check if tables exist
        $tables_check = mysqli_query($conn, "SHOW TABLES LIKE 'inventory_items'");
        $tables_exist = mysqli_num_rows($tables_check) > 0;
        
        // Check if form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // First verify tables exist
            if ($tables_exist) {
                $usage_check = mysqli_query($conn, "SHOW TABLES LIKE 'inventory_usage'");
                $usage_exists = mysqli_num_rows($usage_check) > 0;
                
                if ($usage_exists) {
                    $item_id = $_POST['item_id'];
                    $quantity = $_POST['quantity'];
                    $date_used = $_POST['date_used'];
                    
                    // Insert usage record
                    $sql = "INSERT INTO inventory_usage (item_id, quantity_used, date_used) 
                            VALUES ($item_id, $quantity, '$date_used')";
                    
                    if (mysqli_query($conn, $sql)) {
                        echo '<div class="alert alert-success" role="alert">Usage recorded successfully!</div>';
                        
                        // Update current_stock in inventory_items
                        $update_stock = "UPDATE inventory_items SET current_stock = current_stock - $quantity WHERE id = $item_id";
                        mysqli_query($conn, $update_stock);
                    } else {
                        echo '<div class="alert alert-danger" role="alert">Error recording usage: ' . mysqli_error($conn) . '</div>';
                    }
                } else {
                    echo '<div class="alert alert-danger" role="alert">Database tables not set up correctly. Please run <a href="../../create_tables.php">create_tables.php</a> first.</div>';
                }
            } else {
                echo '<div class="alert alert-danger" role="alert">Database tables not set up correctly. Please run <a href="../../create_tables.php">create_tables.php</a> first.</div>';
            }
        }

        // Get inventory items if tables exist
        if ($tables_exist) {
            $items_sql = "SELECT id, item_name FROM inventory_items ORDER BY item_name";
            $items_result = mysqli_query($conn, $items_sql);
            
            // Check if we have any inventory items
            $has_items = mysqli_num_rows($items_result) > 0;
        }
    } catch (Exception $e) {
        echo '<div class="alert alert-danger" role="alert">An error occurred: ' . $e->getMessage() . '</div>';
    }
    ?>

    <div class="card mb-4">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Record Daily Usage</h5>
      </div>
      <div class="card-body">
        <?php if ($has_items): ?>
          <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="mb-3">
              <label for="item_id" class="form-label">Select Item</label>
              <select class="form-select" id="item_id" name="item_id" required>
                <option value="">-- Select Item --</option>
                <?php 
                // Reset the result pointer
                mysqli_data_seek($items_result, 0);
                while ($item = mysqli_fetch_assoc($items_result)): 
                ?>
                  <option value="<?php echo $item['id']; ?>"><?php echo $item['item_name']; ?></option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="mb-3">
              <label for="quantity" class="form-label">Quantity Used</label>
              <input type="number" class="form-control" id="quantity" name="quantity" step="0.01" required>
            </div>
            <div class="mb-3">
              <label for="date_used" class="form-label">Date Used</label>
              <input type="date" class="form-control" id="date_used" name="date_used" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Record Usage</button>
          </form>
        <?php else: ?>
          <div class="alert alert-warning">
            No inventory items found. Please <a href="input-purchase-details.php">add inventory items</a> first or run <a href="../../create_tables.php">create_tables.php</a> to set up the database.
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Recent Usage Table -->
    <div class="card">
      <div class="card-header bg-info text-white">
        <h5 class="mb-0">Recent Usage Records</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>Item Name</th>
                <th>Quantity Used</th>
                <th>Date Used</th>
              </tr>
            </thead>
            <tbody>
              <?php
              try {
                  // Check if both tables exist and have proper structure
                  $items_exist = mysqli_query($conn, "SHOW TABLES LIKE 'inventory_items'");
                  $usage_exist = mysqli_query($conn, "SHOW TABLES LIKE 'inventory_usage'");
                  
                  if (mysqli_num_rows($items_exist) > 0 && mysqli_num_rows($usage_exist) > 0) {
                      // Check if we have any records in both tables
                      $count_items = mysqli_query($conn, "SELECT COUNT(*) as count FROM inventory_items");
                      $count_usage = mysqli_query($conn, "SELECT COUNT(*) as count FROM inventory_usage");
                      
                      $have_items = mysqli_fetch_assoc($count_items)['count'] > 0;
                      $have_usage = mysqli_fetch_assoc($count_usage)['count'] > 0;
                      
                      if ($have_items && $have_usage) {
                          // Safe to run the full query
                          $recent_usage_sql = "SELECT i.item_name, u.quantity_used, u.date_used 
                                              FROM inventory_usage u
                                              JOIN inventory_items i ON u.item_id = i.id
                                              ORDER BY u.date_used DESC, u.id DESC
                                              LIMIT 10";
                          $recent_usage_result = mysqli_query($conn, $recent_usage_sql);
                          
                          if ($recent_usage_result && mysqli_num_rows($recent_usage_result) > 0) {
                              while ($row = mysqli_fetch_assoc($recent_usage_result)) {
                                  echo "<tr>";
                                  echo "<td>" . $row['item_name'] . "</td>";
                                  echo "<td>" . $row['quantity_used'] . "</td>";
                                  echo "<td>" . $row['date_used'] . "</td>";
                                  echo "</tr>";
                              }
                          } else {
                              echo "<tr><td colspan='3' class='text-center'>No usage records found</td></tr>";
                          }
                      } else {
                          echo "<tr><td colspan='3' class='text-center'>No usage records found. Please record some inventory usage.</td></tr>";
                      }
                  } else {
                      echo "<tr><td colspan='3' class='text-center'>Please run the <a href='setup_tables.php'>Setup Inventory Tables</a> script first.</td></tr>";
                  }
              } catch (Exception $e) {
                  echo "<tr><td colspan='3' class='text-center'>Error: " . $e->getMessage() . "</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Usage Summary -->
    <div class="card mt-4">
      <div class="card-header bg-success text-white">
        <h5 class="mb-0">Usage Summary (Last 7 Days)</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>Item Name</th>
                <th>Total Quantity Used</th>
              </tr>
            </thead>
            <tbody>
              <?php
              try {
                  // Check if both tables exist and have proper structure
                  $items_exist = mysqli_query($conn, "SHOW TABLES LIKE 'inventory_items'");
                  $usage_exist = mysqli_query($conn, "SHOW TABLES LIKE 'inventory_usage'");
                  
                  if (mysqli_num_rows($items_exist) > 0 && mysqli_num_rows($usage_exist) > 0) {
                      // Check if we have any records in both tables
                      $count_items = mysqli_query($conn, "SELECT COUNT(*) as count FROM inventory_items");
                      $count_usage = mysqli_query($conn, "SELECT COUNT(*) as count FROM inventory_usage");
                      
                      $have_items = mysqli_fetch_assoc($count_items)['count'] > 0;
                      $have_usage = mysqli_fetch_assoc($count_usage)['count'] > 0;
                      
                      if ($have_items && $have_usage) {
                          $today = date('Y-m-d');
                          $week_ago = date('Y-m-d', strtotime('-7 days'));
                          
                          $summary_sql = "SELECT i.item_name, SUM(u.quantity_used) as total_used 
                                      FROM inventory_usage u
                                      JOIN inventory_items i ON u.item_id = i.id
                                      WHERE u.date_used BETWEEN '$week_ago' AND '$today'
                                      GROUP BY u.item_id
                                      ORDER BY total_used DESC";
                          $summary_result = mysqli_query($conn, $summary_sql);
                          
                          if ($summary_result && mysqli_num_rows($summary_result) > 0) {
                              while ($row = mysqli_fetch_assoc($summary_result)) {
                                  echo "<tr>";
                                  echo "<td>" . $row['item_name'] . "</td>";
                                  echo "<td>" . $row['total_used'] . "</td>";
                                  echo "</tr>";
                              }
                          } else {
                              echo "<tr><td colspan='2' class='text-center'>No usage in the last 7 days</td></tr>";
                          }
                      } else {
                          echo "<tr><td colspan='2' class='text-center'>No usage records found. Please record some inventory usage.</td></tr>";
                      }
                  } else {
                      echo "<tr><td colspan='2' class='text-center'>Please run the <a href='setup_tables.php'>Setup Inventory Tables</a> script first.</td></tr>";
                  }
              } catch (Exception $e) {
                  echo "<tr><td colspan='2' class='text-center'>Error: " . $e->getMessage() . "</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div> <!-- End of main-content -->

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
