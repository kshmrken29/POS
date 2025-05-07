<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Monitor Menu Sales</title>
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
    .progress {
      height: 25px;
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
            <a class="nav-link" href="edit-menu-details.php">Edit Menu Details</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="monitor-menu-sales.php">Monitor Menu Sales</a>
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
                            <th>Progress</th>
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
                                        <td>' . $item['number_of_servings'] . '</td>
                                        <td>' . $item['servings_sold'] . '</td>
                                        <td>$' . number_format($actualSales, 2) . '</td>
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
                                <td>$<?php echo number_format($selectedMenuItem['approximate_cost'], 2); ?></td>
                            </tr>
                            <tr>
                                <th>Price Per Serve:</th>
                                <td>$<?php echo number_format($selectedMenuItem['price_per_serve'], 2); ?></td>
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
                                <td>$<?php echo number_format($selectedMenuItem['expected_sales'], 2); ?></td>
                            </tr>
                            <tr>
                                <th>Actual Sales:</th>
                                <td>$<?php echo number_format($selectedMenuItem['servings_sold'] * $selectedMenuItem['price_per_serve'], 2); ?></td>
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
    
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
