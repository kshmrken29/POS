<?php
// Include authentication system
require_once '../auth_session.php';
require_admin();

// Log that admin dashboard was accessed
log_activity('accessed admin dashboard');
// Include database connection
include '../../connection.php';

// Initialize variable to hold selected menu item
$selectedMenuItem = null;

// Handle form submission for update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_menu'])) {
    $menu_id = $_POST['menu_id'];
    $menu_name = $_POST['menu_name'];
    $approximate_cost = $_POST['approximate_cost'];
    $number_of_servings = $_POST['number_of_servings'];
    $price_per_serve = $_POST['price_per_serve'];
    $expected_sales = $number_of_servings * $price_per_serve;
    
    // Update the menu item
    $sql = "UPDATE menu_items SET 
            menu_name = '$menu_name',
            approximate_cost = '$approximate_cost',
            number_of_servings = '$number_of_servings',
            price_per_serve = '$price_per_serve',
            expected_sales = '$expected_sales'
            WHERE id = $menu_id";
            
    if (mysqli_query($conn, $sql)) {
        echo '<div class="alert alert-success" role="alert">Menu item updated successfully!</div>';
    } else {
        echo '<div class="alert alert-danger" role="alert">Error updating menu item: ' . mysqli_error($conn) . '</div>';
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
    }
}

// Get all menu items
$sql = "SELECT id, menu_name, date_added FROM menu_items ORDER BY date_added DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Menu Details</title>
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
          <a class="nav-link" href="input-daily-menu.php">
            <i class="bi bi-plus-circle"></i> Input Daily Menu
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="edit-menu-details.php">
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
      
  <!-- Main content -->
  <div class="main-content">
    <h2 class="mb-4">Edit Menu Details</h2>
    
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
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <input type="hidden" name="menu_id" value="<?php echo $selectedMenuItem['id']; ?>">
                            
                            <div class="mb-3">
                                <label for="menu_name" class="form-label">Menu Name</label>
                                <input type="text" class="form-control" id="menu_name" name="menu_name" 
                                    value="<?php echo $selectedMenuItem['menu_name']; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="approximate_cost" class="form-label">Approximate Cost</label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" class="form-control" id="approximate_cost" name="approximate_cost" 
                                        step="0.01" value="<?php echo $selectedMenuItem['approximate_cost']; ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="number_of_servings" class="form-label">Approximate Number of Servings</label>
                                <input type="number" class="form-control" id="number_of_servings" name="number_of_servings" 
                                    value="<?php echo $selectedMenuItem['number_of_servings']; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="price_per_serve" class="form-label">Price Per Serve</label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" class="form-control" id="price_per_serve" name="price_per_serve" 
                                        step="0.01" value="<?php echo $selectedMenuItem['price_per_serve']; ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Expected Sales</label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="text" class="form-control" id="expected_sales" 
                                        value="<?php echo $selectedMenuItem['expected_sales']; ?>" disabled>
                                    <span class="input-group-text">Will be calculated automatically</span>
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
  </div> <!-- End of main-content -->

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
  </script>
</body>
</html> 