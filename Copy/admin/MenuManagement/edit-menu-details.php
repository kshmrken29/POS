<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Menu Details</title>
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
            <a class="nav-link" href="input-daily-menu.php">Input Daily Menu</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="edit-menu-details.php">Edit Menu Details</a>
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
    <h2 class="mb-4">Edit Menu Details</h2>
    
    <?php
    // Include database connection
    include '../../connection.php';
    
    // Initialize variable to hold selected menu item
    $selectedMenuItem = null;
    $menuInventoryItems = [];
    
    // Get inventory items for dropdown
    $inventory_items = [];
    $inventory_query = "SELECT id, item_name, current_stock FROM inventory_items ORDER BY item_name";
    $inventory_result = mysqli_query($conn, $inventory_query);
    
    if ($inventory_result && mysqli_num_rows($inventory_result) > 0) {
        while ($item = mysqli_fetch_assoc($inventory_result)) {
            $inventory_items[$item['id']] = $item;
        }
    }
    
    // Handle form submission for update
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_menu'])) {
        $menu_id = $_POST['menu_id'];
        $menu_name = $_POST['menu_name'];
        $approximate_cost = $_POST['approximate_cost'];
        $number_of_servings = $_POST['number_of_servings'];
        $price_per_serve = $_POST['price_per_serve'];
        $expected_sales = $number_of_servings * $price_per_serve;
        
        // Begin transaction for updating menu and inventory mappings
        mysqli_begin_transaction($conn);
        
        try {
            // Update the menu item
            $sql = "UPDATE menu_items SET 
                    menu_name = '$menu_name',
                    approximate_cost = '$approximate_cost',
                    number_of_servings = '$number_of_servings',
                    price_per_serve = '$price_per_serve',
                    expected_sales = '$expected_sales'
                    WHERE id = $menu_id";
                    
            if (mysqli_query($conn, $sql)) {
                // First delete all existing inventory mappings for this menu item
                $delete_mappings = "DELETE FROM menu_inventory_mapping WHERE menu_item_id = $menu_id";
                if (!mysqli_query($conn, $delete_mappings)) {
                    throw new Exception("Error deleting existing inventory mappings: " . mysqli_error($conn));
                }
                
                // Process inventory items if any were selected
                if (isset($_POST['inventory_item']) && !empty($_POST['inventory_item'])) {
                    foreach ($_POST['inventory_item'] as $index => $item_id) {
                        if (!empty($item_id) && isset($_POST['quantity_per_serving'][$index]) && !empty($_POST['quantity_per_serving'][$index])) {
                            $qty_per_serving = $_POST['quantity_per_serving'][$index];
                            
                            // Insert mapping to connect menu item with inventory item
                            $mapping_sql = "INSERT INTO menu_inventory_mapping (menu_item_id, inventory_item_id, quantity_per_serving) 
                                           VALUES ($menu_id, $item_id, $qty_per_serving)";
                            
                            if (!mysqli_query($conn, $mapping_sql)) {
                                throw new Exception("Error adding inventory mapping: " . mysqli_error($conn));
                            }
                        }
                    }
                }
                
                // Commit the transaction
                mysqli_commit($conn);
                echo '<div class="alert alert-success" role="alert">Menu item updated successfully!</div>';
            } else {
                throw new Exception("Error updating menu item: " . mysqli_error($conn));
            }
        } catch (Exception $e) {
            // Roll back the transaction in case of error
            mysqli_rollback($conn);
            echo '<div class="alert alert-danger" role="alert">Error: ' . $e->getMessage() . '</div>';
        }
    }
    
    // Handle selection of menu item to edit
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['menu_id'])) {
        $menu_id = $_GET['menu_id'];
        
        // Get the menu item details
        $sql = "SELECT * FROM menu_items WHERE id = $menu_id";
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) > 0) {
            $selectedMenuItem = mysqli_fetch_assoc($result);
            
            // Get the inventory items associated with this menu item
            $inventory_sql = "SELECT mim.* 
                             FROM menu_inventory_mapping mim 
                             WHERE mim.menu_item_id = $menu_id";
            $inventory_result = mysqli_query($conn, $inventory_sql);
            
            if ($inventory_result && mysqli_num_rows($inventory_result) > 0) {
                while ($row = mysqli_fetch_assoc($inventory_result)) {
                    $menuInventoryItems[] = $row;
                }
            }
        }
    }
    
    // Get all menu items
    $sql = "SELECT id, menu_name, date_added FROM menu_items ORDER BY date_added DESC";
    $result = mysqli_query($conn, $sql);
    ?>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    Select Menu Item to Edit
                </div>
                <div class="card-body">
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        echo '<ul class="list-group">';
                        while($row = mysqli_fetch_assoc($result)) {
                            $activeClass = (isset($_GET['menu_id']) && $_GET['menu_id'] == $row['id']) ? "active" : "";
                            echo '<li class="list-group-item ' . $activeClass . '">
                                    <a href="?menu_id=' . $row['id'] . '" class="text-decoration-none d-block">
                                        ' . $row['menu_name'] . '
                                        <small class="d-block text-muted">Added: ' . $row['date_added'] . '</small>
                                    </a>
                                  </li>';
                        }
                        echo '</ul>';
                    } else {
                        echo '<p>No menu items found. <a href="input-daily-menu.php">Add some</a> first.</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <?php if ($selectedMenuItem): ?>
                <div class="card">
                    <div class="card-header">
                        Edit: <?php echo $selectedMenuItem['menu_name']; ?>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?menu_id=' . $selectedMenuItem['id']); ?>">
                            <input type="hidden" name="menu_id" value="<?php echo $selectedMenuItem['id']; ?>">
                            
                            <div class="mb-3">
                                <label for="menu_name" class="form-label">Menu Name</label>
                                <input type="text" class="form-control" id="menu_name" name="menu_name" 
                                    value="<?php echo $selectedMenuItem['menu_name']; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="approximate_cost" class="form-label">Approximate Cost</label>
                                <input type="number" class="form-control" id="approximate_cost" name="approximate_cost" 
                                    step="0.01" value="<?php echo $selectedMenuItem['approximate_cost']; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="number_of_servings" class="form-label">Approximate Number of Servings</label>
                                <input type="number" class="form-control" id="number_of_servings" name="number_of_servings" 
                                    value="<?php echo $selectedMenuItem['number_of_servings']; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="price_per_serve" class="form-label">Price Per Serve</label>
                                <input type="number" class="form-control" id="price_per_serve" name="price_per_serve" 
                                    step="0.01" value="<?php echo $selectedMenuItem['price_per_serve']; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Expected Sales</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="expected_sales" 
                                        value="<?php echo $selectedMenuItem['expected_sales']; ?>" disabled>
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
                                            
                                            <?php if (count($menuInventoryItems) > 0): ?>
                                                <?php foreach ($menuInventoryItems as $index => $item): ?>
                                                    <div class="inventory-item-row row">
                                                        <div class="col-md-6">
                                                            <select class="form-select" name="inventory_item[<?php echo $index; ?>]">
                                                                <option value="">-- Select Inventory Item --</option>
                                                                <?php foreach ($inventory_items as $inv_item): ?>
                                                                    <option value="<?php echo $inv_item['id']; ?>" <?php echo ($inv_item['id'] == $item['inventory_item_id']) ? 'selected' : ''; ?>>
                                                                        <?php echo $inv_item['item_name']; ?> (Current Stock: <?php echo $inv_item['current_stock']; ?>)
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <input type="number" class="form-control" name="quantity_per_serving[<?php echo $index; ?>]" 
                                                                step="0.01" value="<?php echo $item['quantity_per_serving']; ?>" placeholder="Quantity per serving">
                                                        </div>
                                                        <div class="col-md-1">
                                                            <button type="button" class="btn btn-danger btn-sm remove-item">Remove</button>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
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
                                                        <input type="number" class="form-control" name="quantity_per_serving[0]" 
                                                            step="0.01" placeholder="Quantity per serving">
                                                    </div>
                                                    <div class="col-md-1">
                                                        <button type="button" class="btn btn-danger btn-sm remove-item" style="display:none;">Remove</button>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
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
                            
                            <button type="submit" name="update_menu" class="btn btn-primary">Update Menu Item</button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body">
                        <p class="text-center mb-0">Select a menu item from the list to edit its details.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
      // Calculate expected sales when input changes
      if (document.getElementById('number_of_servings') && document.getElementById('price_per_serve')) {
          document.getElementById('number_of_servings').addEventListener('input', calculateExpectedSales);
          document.getElementById('price_per_serve').addEventListener('input', calculateExpectedSales);
          
          function calculateExpectedSales() {
              const servings = document.getElementById('number_of_servings').value || 0;
              const price = document.getElementById('price_per_serve').value || 0;
              const expected = servings * price;
              document.getElementById('expected_sales').value = expected.toFixed(2);
          }
      }
      
      // Handle adding and removing inventory items
      let itemIndex = <?php echo max(count($menuInventoryItems) - 1, 0); ?>;
      
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
      
      // Add event listener to the existing remove buttons
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