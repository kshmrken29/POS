<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Input Daily Usage</title>
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
            <a class="nav-link" href="../index.php">Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="input-purchase-details.php">Input Purchase Details</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="input-daily-usage.php">Input Daily Usage</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="remaining-stock-view.php">Remaining Stock View</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container">
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
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
