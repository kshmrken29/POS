<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Input Daily Menu</title>
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
    .inventory-item-row {
      margin-bottom: 10px;
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
            <a class="nav-link active" href="input-daily-menu.php">Input Daily Menu</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="edit-menu-details.php">Edit Menu Details</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="monitor-menu-sales.php">Monitor Menu Sales</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="sales-reporting.php">Sales Reporting</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="manage-cashier.php">Manage Cashier</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container">
    <h2 class="mb-4">Input Daily Menu</h2>

    <?php
    // Include database connection
    include '../../connection.php';

    // Get inventory items
    $inventory_items = [];
    $inventory_query = "SELECT id, item_name, current_stock FROM inventory_items ORDER BY item_name";
    $inventory_result = mysqli_query($conn, $inventory_query);
    
    if ($inventory_result && mysqli_num_rows($inventory_result) > 0) {
        while ($item = mysqli_fetch_assoc($inventory_result)) {
            $inventory_items[] = $item;
        }
    }

    // Check if the form has been submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $menu_name = $_POST['menu_name'];
        $approximate_cost = $_POST['approximate_cost'];
        $number_of_servings = $_POST['number_of_servings'];
        $price_per_serve = $_POST['price_per_serve'];
        $expected_sales = $number_of_servings * $price_per_serve;
        $date_added = date('Y-m-d');

        // Begin transaction for menu item and its inventory mappings
        mysqli_begin_transaction($conn);
        
        try {
            // Prepare SQL statement for menu item
            $sql = "INSERT INTO menu_items (menu_name, approximate_cost, number_of_servings, price_per_serve, expected_sales, servings_sold, date_added) 
                    VALUES ('$menu_name', '$approximate_cost', '$number_of_servings', '$price_per_serve', '$expected_sales', 0, '$date_added')";

            // Execute SQL and check if successful
            if (mysqli_query($conn, $sql)) {
                $menu_item_id = mysqli_insert_id($conn);
                
                // Process inventory items if any were selected
                if (isset($_POST['inventory_item']) && !empty($_POST['inventory_item'])) {
                    foreach ($_POST['inventory_item'] as $index => $item_id) {
                        if (!empty($item_id) && isset($_POST['quantity_per_serving'][$index]) && !empty($_POST['quantity_per_serving'][$index])) {
                            $qty_per_serving = $_POST['quantity_per_serving'][$index];
                            
                            // Insert mapping to connect menu item with inventory item
                            $mapping_sql = "INSERT INTO menu_inventory_mapping (menu_item_id, inventory_item_id, quantity_per_serving) 
                                           VALUES ($menu_item_id, $item_id, $qty_per_serving)";
                            
                            if (!mysqli_query($conn, $mapping_sql)) {
                                throw new Exception("Error adding inventory mapping: " . mysqli_error($conn));
                            }
                        }
                    }
                }
                
                // Commit the transaction
                mysqli_commit($conn);
                echo '<div class="alert alert-success" role="alert">Menu item added successfully!</div>';
            } else {
                throw new Exception("Error adding menu item: " . mysqli_error($conn));
            }
        } catch (Exception $e) {
            // Roll back the transaction in case of error
            mysqli_rollback($conn);
            echo '<div class="alert alert-danger" role="alert">Error: ' . $e->getMessage() . '</div>';
        }
    }
    ?>

    <div class="card">
      <div class="card-body">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
          <div class="mb-3">
            <label for="menu_name" class="form-label">Menu Name</label>
            <input type="text" class="form-control" id="menu_name" name="menu_name" required>
          </div>
          <div class="mb-3">
            <label for="approximate_cost" class="form-label">Approximate Cost</label>
            <input type="number" class="form-control" id="approximate_cost" name="approximate_cost" step="0.01" required>
          </div>
          <div class="mb-3">
            <label for="number_of_servings" class="form-label">Approximate Number of Servings</label>
            <input type="number" class="form-control" id="number_of_servings" name="number_of_servings" required>
          </div>
          <div class="mb-3">
            <label for="price_per_serve" class="form-label">Price Per Serve</label>
            <input type="number" class="form-control" id="price_per_serve" name="price_per_serve" step="0.01" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Expected Sales</label>
            <div class="input-group">
              <input type="text" class="form-control" id="expected_sales" disabled>
              <span class="input-group-text">Will be calculated automatically</span>
            </div>
          </div>
          
          <!-- Inventory Items Section -->
          <div class="card mb-3">
            <div class="card-header bg-light">
                <h5 class="mb-0">Inventory Items Used</h5>
                <small class="text-muted">Specify the inventory items used per serving of this menu item</small>
            </div>
            <div class="card-body">
              <?php if (count($inventory_items) > 0): ?>
                <div id="inventory-items-container">
                  <div class="row mb-2">
                    <div class="col-md-6"><strong>Inventory Item</strong></div>
                    <div class="col-md-5"><strong>Quantity Per Serving</strong></div>
                    <div class="col-md-1"></div>
                  </div>
                  
                  <div class="inventory-item-row row">
                    <div class="col-md-6">
                      <select class="form-select" name="inventory_item[0]">
                        <option value="">-- Select Inventory Item --</option>
                        <?php foreach ($inventory_items as $item): ?>
                          <option value="<?php echo $item['id']; ?>">
                            <?php echo $item['item_name']; ?> (Current Stock: <?php echo $item['current_stock']; ?>)
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="col-md-5">
                      <input type="number" class="form-control" name="quantity_per_serving[0]" step="0.01" placeholder="Quantity per serving">
                    </div>
                    <div class="col-md-1">
                      <button type="button" class="btn btn-danger btn-sm remove-item" style="display:none;"><i class="bi bi-trash"></i> Remove</button>
                    </div>
                  </div>
                </div>
                
                <div class="mt-2">
                  <button type="button" id="add-item" class="btn btn-sm btn-secondary">Add Another Item</button>
                </div>
              <?php else: ?>
                <div class="alert alert-warning">
                  No inventory items available. Please <a href="../InventoryManagement/input-purchase-details.php">add inventory items</a> first.
                </div>
              <?php endif; ?>
            </div>
          </div>
          
          <button type="submit" class="btn btn-primary">Save Menu Item</button>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Calculate expected sales when input changes
    document.getElementById('number_of_servings').addEventListener('input', calculateExpectedSales);
    document.getElementById('price_per_serve').addEventListener('input', calculateExpectedSales);

    function calculateExpectedSales() {
      const servings = document.getElementById('number_of_servings').value || 0;
      const price = document.getElementById('price_per_serve').value || 0;
      const expected = servings * price;
      document.getElementById('expected_sales').value = expected.toFixed(2);
    }
    
    // Handle adding and removing inventory items
    let itemIndex = 0;
    
    document.getElementById('add-item').addEventListener('click', function() {
      itemIndex++;
      const container = document.getElementById('inventory-items-container');
      
      // Clone the first row
      const firstRow = container.querySelector('.inventory-item-row');
      const newRow = firstRow.cloneNode(true);
      
      // Update input names to have the correct index
      const select = newRow.querySelector('select');
      select.name = `inventory_item[${itemIndex}]`;
      select.selectedIndex = 0;
      
      const input = newRow.querySelector('input');
      input.name = `quantity_per_serving[${itemIndex}]`;
      input.value = '';
      
      // Show the remove button
      const removeBtn = newRow.querySelector('.remove-item');
      removeBtn.style.display = 'block';
      
      // Add the new row
      container.appendChild(newRow);
      
      // Add event listener to the remove button
      removeBtn.addEventListener('click', function() {
        container.removeChild(newRow);
      });
    });
    
    // Add event listener to the first row's remove button
    const removeButtons = document.querySelectorAll('.remove-item');
    removeButtons.forEach(btn => {
      btn.addEventListener('click', function() {
        const row = this.closest('.inventory-item-row');
        const container = document.getElementById('inventory-items-container');
        
        // Only remove if there's more than one row
        if (container.querySelectorAll('.inventory-item-row').length > 1) {
          container.removeChild(row);
        }
      });
    });
  </script>
</body>
</html>
