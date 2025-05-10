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
  <title>Input Purchase Details</title>
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
          <a class="nav-link active" href="input-purchase-details.php">
            <i class="bi bi-cart-plus"></i> Purchase Details
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="input-daily-usage.php">
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
    <h2 class="mb-4">Input Purchase Details</h2>

    <?php
    // Include database connection
    include '../connection.php';

    // Initialize variables
    $has_items = false;
    $items_result = null;

    try {
        // Check if form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // First check if the inventory_items table exists
            $table_check = mysqli_query($conn, "SHOW TABLES LIKE 'inventory_items'");
            $items_table_exists = mysqli_num_rows($table_check) > 0;
            
            $purchases_check = mysqli_query($conn, "SHOW TABLES LIKE 'inventory_purchases'");
            $purchases_table_exists = mysqli_num_rows($purchases_check) > 0;
            
            if (!$items_table_exists || !$purchases_table_exists) {
                echo '<div class="alert alert-danger" role="alert">Inventory tables not set up. Please run <a href="../../create_tables.php">create_tables.php</a> first.</div>';
            } else {
                // Check if a new item is being added
                if (isset($_POST['item_name']) && !empty($_POST['item_name'])) {
                    $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
                    $description = mysqli_real_escape_string($conn, $_POST['description'] ?? '');
                    $quantity = floatval($_POST['quantity']);
                    $total_price = floatval($_POST['total_price']);
                    $date_purchased = $_POST['date_purchased'];

                    // Check if item already exists
                    $check_sql = "SELECT id FROM inventory_items WHERE item_name = '$item_name'";
                    $check_result = mysqli_query($conn, $check_sql);

                    if (mysqli_num_rows($check_result) > 0) {
                        echo '<div class="alert alert-warning" role="alert">Item already exists in inventory!</div>';
                        $item_id = mysqli_fetch_assoc($check_result)['id'];
                    } else {
                        // Insert new inventory item
                        $item_sql = "INSERT INTO inventory_items (item_name, description, current_stock, created_at) VALUES ('$item_name', '$description', 0, NOW())";
                        if (mysqli_query($conn, $item_sql)) {
                            echo '<div class="alert alert-success" role="alert">New inventory item added successfully!</div>';
                            $item_id = mysqli_insert_id($conn);
                        } else {
                            echo '<div class="alert alert-danger" role="alert">Error adding item: ' . mysqli_error($conn) . '</div>';
                        }
                    }

                    // Add purchase details
                    if (isset($item_id)) {
                        // Check if a similar purchase already exists on the same date
                        $check_duplicate = "SELECT * FROM inventory_purchases 
                                          WHERE item_id = $item_id 
                                          AND quantity_purchased = $quantity 
                                          AND date_purchased = '$date_purchased'";
                        $duplicate_result = mysqli_query($conn, $check_duplicate);
                        
                        if (mysqli_num_rows($duplicate_result) > 0) {
                            echo '<div class="alert alert-warning" role="alert">A similar purchase for this item with the same quantity already exists for the selected date. Please verify if this is a duplicate entry.</div>';
                        } else {
                            $purchase_sql = "INSERT INTO inventory_purchases (item_id, quantity_purchased, total_price, date_purchased) 
                                            VALUES ($item_id, $quantity, $total_price, '$date_purchased')";
                            
                            if (mysqli_query($conn, $purchase_sql)) {
                                echo '<div class="alert alert-success" role="alert">Purchase details added successfully!</div>';
                                
                                // Update current_stock in inventory_items
                                $update_stock = "UPDATE inventory_items SET current_stock = current_stock + $quantity WHERE id = $item_id";
                                mysqli_query($conn, $update_stock);
                                
                                // Clear form after successful submission
                                echo '<script>
                                    document.addEventListener("DOMContentLoaded", function() {
                                        document.getElementById("new-purchase-form").reset();
                                    });
                                </script>';
                            } else {
                                echo '<div class="alert alert-danger" role="alert">Error adding purchase details: ' . mysqli_error($conn) . '</div>';
                            }
                        }
                    }
                } else if (isset($_POST['existing_item_id']) && !empty($_POST['existing_item_id'])) {
                    // Add purchase details for existing item
                    $item_id = $_POST['existing_item_id'];
                    $quantity = floatval($_POST['quantity']);
                    $total_price = floatval($_POST['total_price']);
                    $date_purchased = $_POST['date_purchased'];

                    // Check if a similar purchase already exists on the same date
                    $check_duplicate = "SELECT * FROM inventory_purchases 
                                      WHERE item_id = $item_id 
                                      AND quantity_purchased = $quantity 
                                      AND date_purchased = '$date_purchased'";
                    $duplicate_result = mysqli_query($conn, $check_duplicate);
                    
                    if (mysqli_num_rows($duplicate_result) > 0) {
                        echo '<div class="alert alert-warning" role="alert">A similar purchase for this item with the same quantity already exists for the selected date. Please verify if this is a duplicate entry.</div>';
                    } else {
                        $purchase_sql = "INSERT INTO inventory_purchases (item_id, quantity_purchased, total_price, date_purchased) 
                                        VALUES ($item_id, $quantity, $total_price, '$date_purchased')";
                        
                        if (mysqli_query($conn, $purchase_sql)) {
                            echo '<div class="alert alert-success" role="alert">Purchase details added successfully!</div>';
                            
                            // Update current_stock in inventory_items
                            $update_stock = "UPDATE inventory_items SET current_stock = current_stock + $quantity WHERE id = $item_id";
                            mysqli_query($conn, $update_stock);
                            
                            // Clear form after successful submission
                            echo '<script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    document.getElementById("existing-purchase-form").reset();
                                });
                            </script>';
                        } else {
                            echo '<div class="alert alert-danger" role="alert">Error adding purchase details: ' . mysqli_error($conn) . '</div>';
                        }
                    }
                }
            }
        }

        // Check if inventory_items table exists before querying it
        $table_check = mysqli_query($conn, "SHOW TABLES LIKE 'inventory_items'");
        if (mysqli_num_rows($table_check) > 0) {
            // Get existing inventory items
            $items_sql = "SELECT id, item_name FROM inventory_items ORDER BY item_name";
            $items_result = mysqli_query($conn, $items_sql);
            
            // Check if we have any inventory items
            $has_items = $items_result && mysqli_num_rows($items_result) > 0;
        } else {
            echo '<div class="alert alert-warning" role="alert">Inventory tables not found. Please run <a href="../../create_tables.php">create_tables.php</a> first.</div>';
        }
    } catch (Exception $e) {
        echo '<div class="alert alert-danger" role="alert">An error occurred: ' . $e->getMessage() . '</div>';
    }
    ?>

    <div class="card mb-4">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Add New Purchase</h5>
      </div>
      <div class="card-body">
        <?php if (isset($table_check) && mysqli_num_rows($table_check) > 0): ?>
          <ul class="nav nav-tabs" id="purchaseTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="existing-tab" data-bs-toggle="tab" data-bs-target="#existing" type="button" role="tab">Existing Item</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="new-tab" data-bs-toggle="tab" data-bs-target="#new" type="button" role="tab">New Item</button>
            </li>
          </ul>

          <div class="tab-content p-3 border border-top-0 rounded-bottom" id="purchaseTabsContent">
            <!-- Existing Item Form -->
            <div class="tab-pane fade show active" id="existing" role="tabpanel">
              <?php if ($has_items): ?>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="existing-purchase-form">
                  <div class="mb-3">
                    <label for="existing_item_id" class="form-label">Select Item</label>
                    <select class="form-select" id="existing_item_id" name="existing_item_id" required>
                      <option value="">-- Select Item --</option>
                      <?php 
                      // Reset the result pointer
                      if ($items_result) {
                        mysqli_data_seek($items_result, 0);
                        while ($item = mysqli_fetch_assoc($items_result)): 
                      ?>
                        <option value="<?php echo $item['id']; ?>"><?php echo $item['item_name']; ?></option>
                      <?php 
                        endwhile;
                      }
                      ?>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label for="quantity" class="form-label">Quantity Purchased</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" step="0.01" required>
                  </div>
                  <div class="mb-3">
                    <label for="total_price" class="form-label">Total Purchase Price</label>
                    <div class="input-group">
                      <span class="input-group-text">₱</span>
                      <input type="number" class="form-control" id="total_price" name="total_price" step="0.01" required>
                    </div>
                  </div>
                  <div class="mb-3">
                    <label for="date_purchased" class="form-label">Date Purchased</label>
                    <input type="date" class="form-control" id="date_purchased" name="date_purchased" value="<?php echo date('Y-m-d'); ?>" required>
                  </div>
                  <button type="submit" class="btn btn-primary">Save Purchase</button>
                </form>
              <?php else: ?>
                <div class="alert alert-info">
                  No inventory items found. Please add a new item first using the "New Item" tab.
                </div>
              <?php endif; ?>
            </div>

            <!-- New Item Form -->
            <div class="tab-pane fade" id="new" role="tabpanel">
              <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="new-purchase-form">
                <div class="mb-3">
                  <label for="item_name" class="form-label">Item Name</label>
                  <input type="text" class="form-control" id="item_name" name="item_name" required>
                </div>
                <div class="mb-3">
                  <label for="description" class="form-label">Description (Optional)</label>
                  <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                </div>
                <div class="mb-3">
                  <label for="quantity" class="form-label">Quantity Purchased</label>
                  <input type="number" class="form-control" id="quantity" name="quantity" step="0.01" required>
                </div>
                <div class="mb-3">
                  <label for="total_price" class="form-label">Total Purchase Price</label>
                  <div class="input-group">
                    <span class="input-group-text">₱</span>
                    <input type="number" class="form-control" id="total_price" name="total_price" step="0.01" required>
                  </div>
                </div>
                <div class="mb-3">
                  <label for="date_purchased" class="form-label">Date Purchased</label>
                  <input type="date" class="form-control" id="date_purchased" name="date_purchased" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Save Item & Purchase</button>
              </form>
            </div>
          </div>
        <?php else: ?>
          <div class="alert alert-warning">
            Inventory tables not found. Please run <a href="../../create_tables.php" class="alert-link">create_tables.php</a> to set up the database.
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Recent Purchases Table -->
    <div class="card">
      <div class="card-header bg-info text-white">
        <h5 class="mb-0">Recent Purchases</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Date Purchased</th>
              </tr>
            </thead>
            <tbody>
              <?php
              try {
                // Check if both tables exist and have proper structure
                $items_exist = mysqli_query($conn, "SHOW TABLES LIKE 'inventory_items'");
                $purchases_exist = mysqli_query($conn, "SHOW TABLES LIKE 'inventory_purchases'");
                
                if (mysqli_num_rows($items_exist) > 0 && mysqli_num_rows($purchases_exist) > 0) {
                  // Check if we have any records in both tables
                  $count_items = mysqli_query($conn, "SELECT COUNT(*) as count FROM inventory_items");
                  $count_purchases = mysqli_query($conn, "SELECT COUNT(*) as count FROM inventory_purchases");
                  
                  $have_items = mysqli_fetch_assoc($count_items)['count'] > 0;
                  $have_purchases = mysqli_fetch_assoc($count_purchases)['count'] > 0;
                  
                  if ($have_items && $have_purchases) {
                    // Safe to run the full query
                    $recent_purchases_sql = "SELECT i.item_name, p.quantity_purchased, p.total_price, p.date_purchased 
                                          FROM inventory_purchases p
                                          JOIN inventory_items i ON p.item_id = i.id
                                          ORDER BY p.date_purchased DESC
                                          LIMIT 10";
                    $recent_purchases_result = mysqli_query($conn, $recent_purchases_sql);
                    
                    if ($recent_purchases_result && mysqli_num_rows($recent_purchases_result) > 0) {
                      while ($row = mysqli_fetch_assoc($recent_purchases_result)) {
                        echo "<tr>";
                        echo "<td>" . $row['item_name'] . "</td>";
                        echo "<td>" . $row['quantity_purchased'] . "</td>";
                        echo "<td>₱" . number_format($row['total_price'], 2) . "</td>";
                        echo "<td>" . $row['date_purchased'] . "</td>";
                        echo "</tr>";
                      }
                    } else {
                      echo "<tr><td colspan='4' class='text-center'>No recent purchases found</td></tr>";
                    }
                  } else {
                    echo "<tr><td colspan='4' class='text-center'>No purchase records found. Please add some inventory purchases.</td></tr>";
                  }
                } else {
                  echo "<tr><td colspan='4' class='text-center'>Please run the <a href='setup_tables.php'>Setup Inventory Tables</a> script first.</td></tr>";
                }
              } catch (Exception $e) {
                echo "<tr><td colspan='4' class='text-center'>Error: " . $e->getMessage() . "</td></tr>";
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
