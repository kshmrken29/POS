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
  <title>Monitor Menu Sales</title>
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
          <a class="nav-link" href="edit-menu-details.php">
            <i class="bi bi-pencil-square"></i> Edit Menu Details
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="monitor-menu-sales.php">
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
    <h2 class="mb-4">Monitor Menu Sales</h2>
    
    <?php
    // Include database connection
    include '../../connection.php';
    
    // Handle form submission for updating servings sold
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_sales'])) {
        $menu_id = $_POST['menu_id'];
        $servings_sold = $_POST['servings_sold'];
        
        // Update the servings sold
        $sql = "UPDATE menu_items SET servings_sold = '$servings_sold' WHERE id = $menu_id";
                
        if (mysqli_query($conn, $sql)) {
            echo '<div class="alert alert-success" role="alert">Sales data updated successfully!</div>';
        } else {
            echo '<div class="alert alert-danger" role="alert">Error updating sales data: ' . mysqli_error($conn) . '</div>';
        }
    }
    
    // Get details of a specific menu item if requested
    $selectedMenuItem = null;
    if (isset($_GET['menu_id'])) {
        $menu_id = $_GET['menu_id'];
        $sql = "SELECT * FROM menu_items WHERE id = $menu_id";
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) > 0) {
            $selectedMenuItem = mysqli_fetch_assoc($result);
        }
    }
    
    // Get all menu items for the summary table
    $sql = "SELECT * FROM menu_items ORDER BY date_added DESC";
    $allMenuItems = mysqli_query($conn, $sql);
    ?>
    
    <!-- Summary Table Section -->
    <div class="card mb-4">
        <div class="card-header">
            <h4>Daily Menu Sales Summary</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Menu Name</th>
                            <th>Date Added</th>
                            <th>Servings Available</th>
                            <th>Servings Sold</th>
                            <th>Sales</th>
                            <th>Percentage</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($allMenuItems) > 0) {
                            while($item = mysqli_fetch_assoc($allMenuItems)) {
                                $percentSold = ($item['number_of_servings'] > 0) ? 
                                    ($item['servings_sold'] / $item['number_of_servings']) * 100 : 0;
                                $actualSales = $item['servings_sold'] * $item['price_per_serve'];
                                $servingsAvailable = $item['number_of_servings'] - $item['servings_sold'];
                                
                                // Determine progress bar color
                                $progressClass = "bg-primary";
                                if ($percentSold >= 90) {
                                    $progressClass = "bg-success";
                                } elseif ($percentSold >= 60) {
                                    $progressClass = "bg-info";
                                } elseif ($percentSold >= 30) {
                                    $progressClass = "bg-warning";
                                } elseif ($percentSold > 0) {
                                    $progressClass = "bg-danger";
                                }
                                
                                echo '<tr>
                                        <td>' . $item['menu_name'] . '</td>
                                        <td>' . $item['date_added'] . '</td>
                                        <td>' . $servingsAvailable . '</td>
                                        <td>' . $item['servings_sold'] . '</td>
                                        <td>₱' . number_format($actualSales, 2) . '</td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar ' . $progressClass . '" role="progressbar" 
                                                    style="width: ' . $percentSold . '%"
                                                    aria-valuenow="' . $percentSold . '" aria-valuemin="0" aria-valuemax="100">
                                                    ' . round($percentSold) . '%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="?menu_id=' . $item['id'] . '" class="btn btn-sm btn-primary">Details</a>
                                        </td>
                                    </tr>';
                            }
                        } else {
                            echo '<tr><td colspan="7" class="text-center">No menu items found</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Individual Menu Item Details Section -->
    <?php if ($selectedMenuItem): ?>
        <div class="card">
            <div class="card-header">
                <h4>Menu Item Details: <?php echo $selectedMenuItem['menu_name']; ?></h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Menu Information</h5>
                        <table class="table">
                            <tr>
                                <th>Date Added:</th>
                                <td><?php echo $selectedMenuItem['date_added']; ?></td>
                            </tr>
                            <tr>
                                <th>Approximate Cost:</th>
                                <td>₱<?php echo number_format($selectedMenuItem['approximate_cost'], 2); ?></td>
                            </tr>
                            <tr>
                                <th>Price Per Serve:</th>
                                <td>₱<?php echo number_format($selectedMenuItem['price_per_serve'], 2); ?></td>
                            </tr>
                            <tr>
                                <th>Total Servings:</th>
                                <td><?php echo $selectedMenuItem['number_of_servings']; ?></td>
                            </tr>
                            <tr>
                                <th>Servings Sold:</th>
                                <td><?php echo $selectedMenuItem['servings_sold']; ?></td>
                            </tr>
                            <tr>
                                <th>Servings Available:</th>
                                <td><?php echo $selectedMenuItem['number_of_servings'] - $selectedMenuItem['servings_sold']; ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Sales Information</h5>
                        <table class="table">
                            <tr>
                                <th>Expected Sales:</th>
                                <td>₱<?php echo number_format($selectedMenuItem['expected_sales'], 2); ?></td>
                            </tr>
                            <tr>
                                <th>Actual Sales:</th>
                                <td>₱<?php echo number_format($selectedMenuItem['servings_sold'] * $selectedMenuItem['price_per_serve'], 2); ?></td>
                            </tr>
                            <tr>
                                <th>Sales Progress:</th>
                                <td>
                                    <?php 
                                    $salesProgress = ($selectedMenuItem['expected_sales'] > 0) ? 
                                        (($selectedMenuItem['servings_sold'] * $selectedMenuItem['price_per_serve']) / $selectedMenuItem['expected_sales']) * 100 : 0;
                                    echo round($salesProgress) . '%';
                                    ?>
                                </td>
                            </tr>
                        </table>
                        
                        <h5 class="mt-4">Update Sales Data</h5>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?menu_id=' . $selectedMenuItem['id']); ?>">
                            <input type="hidden" name="menu_id" value="<?php echo $selectedMenuItem['id']; ?>">
                            <div class="mb-3">
                                <label for="servings_sold" class="form-label">Servings Sold</label>
                                <input type="number" class="form-control" id="servings_sold" name="servings_sold" 
                                       value="<?php echo $selectedMenuItem['servings_sold']; ?>" 
                                       min="0" max="<?php echo $selectedMenuItem['number_of_servings']; ?>" required>
                            </div>
                            <button type="submit" name="update_sales" class="btn btn-primary">Update Sales</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
  </div> <!-- End of main-content -->

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
