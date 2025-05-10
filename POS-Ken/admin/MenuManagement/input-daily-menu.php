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
  <title>Input Daily Menu</title>
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
          <a class="nav-link active" href="input-daily-menu.php">
            <i class="bi bi-plus-circle"></i> Input Daily Menu
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="edit-menu-details.php">
            <i class="bi bi-pencil-square"></i> Edit Menu Details
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="monitor-menu-sales.php">
            <i class="bi bi-graph-up"></i> Monitor Menu Sales
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="sales-reporting.php">
            <i class="bi bi-file-earmark-bar-graph"></i> Sales Reporting
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="manage-cashier.php">
            <i class="bi bi-person-badge"></i> Manage Cashier
          </a>
        </li>
        <li class="sidebar-heading">Inventory</li>
        <li class="nav-item">
          <a class="nav-link" href="../InventoryManagement/input-purchase-details.php">
            <i class="bi bi-cart-plus"></i> Purchase Details
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../InventoryManagement/input-daily-usage.php">
            <i class="bi bi-card-checklist"></i> Daily Usage
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../InventoryManagement/remaining-stock-view.php">
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
      </ul>
    </div>
  </div>

  <!-- Main content -->
  <div class="main-content">
    <h2 class="mb-4">Input Daily Menu</h2>

    <?php
    // Include database connection
    include '../../connection.php';

    // Debug variable to track submission process
    $debug_message = "";
    
    // Check if the form has been submitted for a new item
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add') {
        $menu_name = mysqli_real_escape_string($conn, $_POST['menu_name']);
        $approximate_cost = $_POST['approximate_cost'];
        $number_of_servings = $_POST['number_of_servings'];
        $price_per_serve = $_POST['price_per_serve'];
        $expected_sales = $number_of_servings * $price_per_serve;
        $date_added = date('Y-m-d');

        // Check connection first
        if (!$conn) {
            echo '<div class="alert alert-danger" role="alert">Database connection failed: ' . mysqli_connect_error() . '</div>';
        } else {
            // Check if menu name already exists for today's date (case-insensitive comparison)
            $check_query = "SELECT * FROM menu_items WHERE LOWER(menu_name) = LOWER('$menu_name') AND date_added = '$date_added'";
            $result = mysqli_query($conn, $check_query);
            
            if (!$result) {
                echo '<div class="alert alert-danger" role="alert">Error checking for duplicates: ' . mysqli_error($conn) . '</div>';
            } 
            else if (mysqli_num_rows($result) > 0) {
                // Instead of warning, update the existing menu item
                $existing_item = mysqli_fetch_assoc($result);
                $existing_id = $existing_item['id'];
                $servings_sold = $existing_item['servings_sold']; // Preserve servings sold
                
                // Update the existing item
                $update_sql = "UPDATE menu_items SET 
                            approximate_cost = '$approximate_cost',
                            number_of_servings = '$number_of_servings',
                            price_per_serve = '$price_per_serve',
                            expected_sales = '$expected_sales'
                            WHERE id = $existing_id";
                            
                if (mysqli_query($conn, $update_sql)) {
                    echo '<div class="alert alert-success" role="alert">Existing menu item "' . $menu_name . '" has been updated!</div>';
                    // Clear form after successful submission with immediate effect
                    echo '<script>
                        document.addEventListener("DOMContentLoaded", function() {
                            document.getElementById("new-menu-form").reset();
                            document.getElementById("new_expected_sales").value = "";
                        });
                    </script>';
                } else {
                    echo '<div class="alert alert-danger" role="alert">Error updating existing item: ' . mysqli_error($conn) . '</div>';
                }
            } 
            else {
                // Prepare SQL statement
                $sql = "INSERT INTO menu_items (menu_name, approximate_cost, number_of_servings, price_per_serve, expected_sales, servings_sold, date_added) 
                        VALUES ('$menu_name', '$approximate_cost', '$number_of_servings', '$price_per_serve', '$expected_sales', 0, '$date_added')";

                // Execute SQL and check if successful
                if (mysqli_query($conn, $sql)) {
                    echo '<div class="alert alert-success" role="alert">Menu item added successfully!</div>';
                    // Clear form after successful submission with immediate effect
                    echo '<script>
                        document.addEventListener("DOMContentLoaded", function() {
                            document.getElementById("new-menu-form").reset();
                            document.getElementById("new_expected_sales").value = "";
                        });
                    </script>';
                } else {
                    echo '<div class="alert alert-danger" role="alert">Error: ' . mysqli_error($conn) . '</div>';
                }
            }
        }
    }
    
    // Handle form submission for update
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update') {
        $menu_id = $_POST['menu_id'];
        $menu_name = mysqli_real_escape_string($conn, $_POST['menu_name']);
        $approximate_cost = $_POST['approximate_cost'];
        $current_servings = $_POST['current_servings'];
        $additional_servings = $_POST['additional_servings'];
        $new_total_servings = $current_servings + $additional_servings;
        $price_per_serve = $_POST['price_per_serve'];
        $expected_sales = $new_total_servings * $price_per_serve;
        
        // Update the menu item
        $sql = "UPDATE menu_items SET 
                menu_name = '$menu_name',
                approximate_cost = '$approximate_cost',
                number_of_servings = '$new_total_servings',
                price_per_serve = '$price_per_serve',
                expected_sales = '$expected_sales'
                WHERE id = $menu_id";
                
        if (mysqli_query($conn, $sql)) {
            $message = "Menu item updated successfully!";
            if ($additional_servings > 0) {
                $message .= " Added $additional_servings new servings.";
            }
            echo '<div class="alert alert-success" role="alert">' . $message . '</div>';
        } else {
            echo '<div class="alert alert-danger" role="alert">Error updating menu item: ' . mysqli_error($conn) . '</div>';
        }
    }
    
    // Get all menu items for selection dropdown
    $menu_items_sql = "SELECT id, menu_name, date_added FROM menu_items ORDER BY date_added DESC";
    $menu_items_result = mysqli_query($conn, $menu_items_sql);
    $has_menu_items = $menu_items_result && mysqli_num_rows($menu_items_result) > 0;
    
    // Initialize variable for selected menu item details
    $selected_item = null;
    
    // If menu item is selected, get its details
    if (isset($_GET['menu_id'])) {
        $menu_id = $_GET['menu_id'];
        $item_sql = "SELECT * FROM menu_items WHERE id = $menu_id";
        $item_result = mysqli_query($conn, $item_sql);
        
        if ($item_result && mysqli_num_rows($item_result) > 0) {
            $selected_item = mysqli_fetch_assoc($item_result);
        }
    }
    ?>

    <div class="card">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Manage Menu Items</h5>
      </div>
      <div class="card-body">
        <ul class="nav nav-tabs" id="menuTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link <?php echo !isset($_GET['menu_id']) ? 'active' : ''; ?>" id="new-tab" data-bs-toggle="tab" data-bs-target="#new" type="button" role="tab">Add New Menu Item</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link <?php echo isset($_GET['menu_id']) ? 'active' : ''; ?>" id="existing-tab" data-bs-toggle="tab" data-bs-target="#existing" type="button" role="tab">Edit Existing Menu Item</button>
          </li>
        </ul>

        <div class="tab-content p-3 border border-top-0 rounded-bottom" id="menuTabsContent">
          <!-- Add New Menu Item -->
          <div class="tab-pane fade <?php echo !isset($_GET['menu_id']) ? 'show active' : ''; ?>" id="new" role="tabpanel">
            <div class="alert alert-info mb-3">
              <i class="bi bi-info-circle"></i> Adding a menu item with a name that already exists for today will update that existing item instead of creating a duplicate.
            </div>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="new-menu-form" onsubmit="return validateNewForm()">
              <input type="hidden" name="action" value="add">
              <div class="mb-3">
                <label for="menu_name" class="form-label">Menu Name</label>
                <input type="text" class="form-control" id="menu_name" name="menu_name" required>
              </div>
              <div class="mb-3">
                <label for="approximate_cost" class="form-label">Approximate Cost</label>
                <div class="input-group">
                  <span class="input-group-text">₱</span>
                  <input type="number" class="form-control" id="approximate_cost" name="approximate_cost" step="0.01" min="0.01" required>
                </div>
              </div>
              <div class="mb-3">
                <label for="number_of_servings" class="form-label">Approximate Number of Servings</label>
                <input type="number" class="form-control" id="number_of_servings" name="number_of_servings" min="1" required>
              </div>
              <div class="mb-3">
                <label for="price_per_serve" class="form-label">Price Per Serve</label>
                <div class="input-group">
                  <span class="input-group-text">₱</span>
                  <input type="number" class="form-control" id="price_per_serve" name="price_per_serve" step="0.01" min="0.01" required>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Expected Sales</label>
                <div class="input-group">
                  <span class="input-group-text">₱</span>
                  <input type="text" class="form-control" id="new_expected_sales" disabled>
                  <span class="input-group-text">Will be calculated automatically</span>
                </div>
              </div>
              <button type="submit" class="btn btn-primary">Save Menu Item</button>
            </form>
          </div>

          <!-- Edit Existing Menu Item -->
          <div class="tab-pane fade <?php echo isset($_GET['menu_id']) ? 'show active' : ''; ?>" id="existing" role="tabpanel">
            <?php if ($has_menu_items): ?>
              <div class="row mb-4">
                <div class="col-md-6">
                  <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="select-menu-form">
                    <div class="mb-3">
                      <label for="menu_id" class="form-label">Select Menu Item to Edit</label>
                      <select class="form-select" id="menu_id" name="menu_id" onchange="this.form.submit()">
                        <option value="">-- Select Menu Item --</option>
                        <?php 
                          mysqli_data_seek($menu_items_result, 0);
                          while ($item = mysqli_fetch_assoc($menu_items_result)):
                        ?>
                          <option value="<?php echo $item['id']; ?>" <?php echo (isset($_GET['menu_id']) && $_GET['menu_id'] == $item['id']) ? 'selected' : ''; ?>>
                            <?php echo $item['menu_name']; ?> (<?php echo $item['date_added']; ?>)
                          </option>
                        <?php endwhile; ?>
                      </select>
                    </div>
                  </form>
                </div>
              </div>

              <?php if ($selected_item): ?>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?menu_id=<?php echo $selected_item['id']; ?>" id="edit-menu-form">
                  <input type="hidden" name="action" value="update">
                  <input type="hidden" name="menu_id" value="<?php echo $selected_item['id']; ?>">
                  
                  <div class="mb-3">
                    <label for="menu_name" class="form-label">Menu Name</label>
                    <input type="text" class="form-control" id="edit_menu_name" name="menu_name" value="<?php echo $selected_item['menu_name']; ?>" required>
                  </div>
                  <div class="mb-3">
                    <label for="approximate_cost" class="form-label">Approximate Cost</label>
                    <div class="input-group">
                      <span class="input-group-text">₱</span>
                      <input type="number" class="form-control" id="edit_approximate_cost" name="approximate_cost" step="0.01" min="0.01" value="<?php echo $selected_item['approximate_cost']; ?>" required>
                    </div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Current Number of Servings</label>
                    <input type="text" class="form-control" value="<?php echo $selected_item['number_of_servings']; ?>" disabled>
                    <input type="hidden" name="current_servings" value="<?php echo $selected_item['number_of_servings']; ?>">
                  </div>
                  <div class="mb-3">
                    <label for="additional_servings" class="form-label">Add Additional Servings</label>
                    <div class="input-group">
                      <input type="number" class="form-control" id="additional_servings" name="additional_servings" min="0" value="0">
                      <span class="input-group-text">servings</span>
                    </div>
                    <small class="text-muted">This will be added to the current number of servings</small>
                  </div>
                  <div class="mb-3">
                    <label for="price_per_serve" class="form-label">Price Per Serve</label>
                    <div class="input-group">
                      <span class="input-group-text">₱</span>
                      <input type="number" class="form-control" id="edit_price_per_serve" name="price_per_serve" step="0.01" min="0.01" value="<?php echo $selected_item['price_per_serve']; ?>" required>
                    </div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Expected Sales</label>
                    <div class="input-group">
                      <span class="input-group-text">₱</span>
                      <input type="text" class="form-control" id="edit_expected_sales" value="<?php echo $selected_item['expected_sales']; ?>" disabled>
                      <span class="input-group-text">Will be calculated automatically</span>
                    </div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Current Servings Sold</label>
                    <input type="text" class="form-control" value="<?php echo $selected_item['servings_sold']; ?>" disabled>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Date Added</label>
                    <input type="text" class="form-control" value="<?php echo $selected_item['date_added']; ?>" disabled>
                  </div>
                  <button type="submit" class="btn btn-primary">Update Menu Item</button>
                </form>
              <?php elseif (isset($_GET['menu_id'])): ?>
                <div class="alert alert-warning">Selected menu item not found.</div>
              <?php else: ?>
                <div class="alert alert-info">Please select a menu item to edit.</div>
              <?php endif; ?>
            <?php else: ?>
              <div class="alert alert-info">No menu items found. Please add a new menu item first.</div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div> <!-- End of main-content -->

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Calculate expected sales for new item form
    document.getElementById('number_of_servings').addEventListener('input', calculateNewExpectedSales);
    document.getElementById('price_per_serve').addEventListener('input', calculateNewExpectedSales);

    function calculateNewExpectedSales() {
      const servings = document.getElementById('number_of_servings').value || 0;
      const price = document.getElementById('price_per_serve').value || 0;
      const expected = servings * price;
      document.getElementById('new_expected_sales').value = expected.toFixed(2);
    }
    
    // Calculate expected sales for edit form
    const additionalServingsField = document.getElementById('additional_servings');
    const editPriceField = document.getElementById('edit_price_per_serve');
    
    if (additionalServingsField && editPriceField) {
      additionalServingsField.addEventListener('input', calculateEditExpectedSales);
      editPriceField.addEventListener('input', calculateEditExpectedSales);
      
      function calculateEditExpectedSales() {
        const currentServings = parseInt(document.querySelector('input[name="current_servings"]').value) || 0;
        const additionalServings = parseInt(additionalServingsField.value) || 0;
        const totalServings = currentServings + additionalServings;
        const price = parseFloat(editPriceField.value) || 0;
        const expected = totalServings * price;
        document.getElementById('edit_expected_sales').value = expected.toFixed(2);
      }
      
      // Calculate initial expected sales
      calculateEditExpectedSales();
    }
    
    // Check for duplicate menu items for new form
    function validateNewForm() {
      const menuName = document.getElementById('menu_name').value;
      
      if (menuName.trim() === '') {
        alert('Menu name cannot be empty');
        return false;
      }
      
      return true; // Allow form submission if validation passes
    }
  </script>
</body>
</html>
