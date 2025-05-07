<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Remaining Stock View</title>
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
    .low-stock {
      background-color: #ffcccc;
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
            <a class="nav-link" href="input-daily-usage.php">Input Daily Usage</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="remaining-stock-view.php">Remaining Stock View</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container">
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

    <!-- Beginner-friendly introduction -->
    <div class="card mb-4 bg-light">
      <div class="card-body">
        <h5><i class="bi bi-info-circle-fill text-primary me-2"></i>What is this page?</h5>
        <p class="fs-5">This page shows your current inventory levels. You can see which items are in stock, running low, or out of stock.</p>
        <p class="mb-0">Use the search box below to find specific items or check the "Show Low Stock Only" box to see items that need to be restocked.</p>
      </div>
    </div>

    <!-- Stock Overview -->
    <div class="card mb-4 shadow">
      <div class="card-header bg-primary text-white py-3">
        <h4 class="mb-0"><i class="bi bi-boxes me-2"></i>Current Inventory Status</h4>
      </div>
      <div class="card-body p-4">
        <?php if ($has_items): ?>
          <div class="mb-4 p-3 bg-light rounded">
            <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="row g-3">
              <div class="col-md-6">
                <label for="searchInput" class="form-label fw-bold">Search for an item:</label>
                <div class="input-group input-group-lg">
                  <input type="text" class="form-control" id="searchInput" name="search" placeholder="Type item name here..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                  <button class="btn btn-primary" type="submit"><i class="bi bi-search me-1"></i> Search</button>
                  <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                    <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="btn btn-outline-secondary"><i class="bi bi-x-circle me-1"></i> Clear</a>
                  <?php endif; ?>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold">Filter options:</label>
                <div class="d-flex align-items-center mt-2">
                  <div class="form-check form-check-inline form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="show_low_stock" name="show_low_stock" value="1" style="transform: scale(1.5); margin-right: 10px;" <?php echo (isset($_GET['show_low_stock'])) ? 'checked' : ''; ?>>
                    <label class="form-check-label fs-5" for="show_low_stock">Show Low Stock Items Only</label>
                  </div>
                  <button type="submit" class="btn btn-success btn-lg ms-3"><i class="bi bi-funnel me-1"></i> Apply Filter</button>
                </div>
              </div>
            </form>
          </div>

          <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered fs-5">
              <thead class="table-dark">
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
                  echo "<td class='fw-bold'>" . $item['item_name'] . "</td>";
                  echo "<td>" . $item['description'] . "</td>";
                  echo "<td>" . number_format($item['current_stock'], 2) . "</td>";
                  
                  // Status column
                  echo "<td>";
                  if ($is_out_of_stock) {
                    echo "<span class='badge bg-danger fs-6 p-2'><i class='bi bi-exclamation-triangle-fill me-1'></i> Out of Stock</span>";
                  } else if ($is_low_stock) {
                    echo "<span class='badge bg-warning text-dark fs-6 p-2'><i class='bi bi-exclamation-circle-fill me-1'></i> Low Stock</span>";
                  } else {
                    echo "<span class='badge bg-success fs-6 p-2'><i class='bi bi-check-circle-fill me-1'></i> In Stock</span>";
                  }
                  echo "</td>";
                  
                  echo "</tr>";
                }
                
                if (!$has_matching_items) {
                  echo "<tr><td colspan='4' class='text-center p-4 fs-4'>No matching inventory items found</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
          
          <div class="mt-4 p-3 bg-light rounded">
            <h5 class="mb-3">What do the colors mean?</h5>
            <div class="d-flex flex-wrap gap-4 fs-5">
              <div class="d-flex align-items-center">
                <span class="badge bg-danger p-2 me-2"><i class="bi bi-exclamation-triangle-fill"></i></span> 
                <span>Out of Stock - Item needs to be purchased immediately</span>
              </div>
              <div class="d-flex align-items-center">
                <span class="badge bg-warning text-dark p-2 me-2"><i class="bi bi-exclamation-circle-fill"></i></span> 
                <span>Low Stock - Less than <?php echo $low_stock_threshold; ?> units remaining</span>
              </div>
              <div class="d-flex align-items-center">
                <span class="badge bg-success p-2 me-2"><i class="bi bi-check-circle-fill"></i></span> 
                <span>In Stock - Sufficient quantity available</span>
              </div>
            </div>
          </div>
        <?php else: ?>
          <div class="alert alert-warning p-4 fs-5">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> No inventory items found. Please <a href="input-purchase-details.php" class="alert-link">add inventory items</a> first or run <a href="../../create_tables.php" class="alert-link">create_tables.php</a> to set up the database.
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Stock Movement History -->
    <div class="card shadow">
      <div class="card-header bg-info text-white py-3">
        <h4 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Stock Movement</h4>
      </div>
      <div class="card-body p-4">
        <div class="mb-3 bg-light p-3 rounded">
          <p class="fs-5 mb-0">This section shows your most recent inventory changes - both purchases and usage. The 20 most recent transactions are displayed.</p>
        </div>
        
        <?php if ($has_items): ?>
          <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered fs-5">
              <thead class="table-dark">
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
                                            echo "<td class='fw-bold'>" . $row['item_name'] . "</td>";
                                            
                                            if ($row['type'] == 'Purchase') {
                                                echo "<td><span class='badge bg-success fs-6 p-2'><i class='bi bi-plus-circle-fill me-1'></i> Purchase</span></td>";
                                            } else {
                                                echo "<td><span class='badge bg-danger fs-6 p-2'><i class='bi bi-dash-circle-fill me-1'></i> Usage</span></td>";
                                            }
                                            
                                            echo "<td>" . $row['quantity'] . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='4' class='text-center p-4 fs-4'>No stock movement records found</td></tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4' class='text-center p-4 fs-4'>No inventory movements recorded yet</td></tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center p-4 fs-4'>Please run the <a href='setup_tables.php'>Setup Inventory Tables</a> script to create purchase and usage tables</td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center p-4 fs-4'>No inventory items found. Please add items first.</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' class='text-center p-4 fs-4'>Inventory items table not found. Please run <a href='setup_tables.php'>Setup Inventory Tables</a> script.</td></tr>";
                    }
                } catch (Exception $e) {
                    echo "<tr><td colspan='4' class='text-center p-4 fs-4'>Error: " . $e->getMessage() . "</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
          
          <div class="mt-4 p-3 bg-light rounded">
            <h5 class="mb-3">Understanding the transactions:</h5>
            <div class="d-flex flex-wrap gap-4 fs-5">
              <div class="d-flex align-items-center">
                <span class="badge bg-success p-2 me-2"><i class="bi bi-plus-circle-fill"></i></span> 
                <span>Purchase - Items added to inventory</span>
              </div>
              <div class="d-flex align-items-center">
                <span class="badge bg-danger p-2 me-2"><i class="bi bi-dash-circle-fill"></i></span> 
                <span>Usage - Items used/consumed from inventory</span>
              </div>
            </div>
          </div>
        <?php else: ?>
          <div class="alert alert-warning p-4 fs-5">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> No inventory items found. Please <a href="input-purchase-details.php" class="alert-link">add inventory items</a> first.
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</body>
</html>
