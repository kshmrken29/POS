<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sales Reporting</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    .card {
      margin-bottom: 20px;
    }
    .total-box {
      background-color: #f8f9fa;
      padding: 20px;
      border-radius: 5px;
      text-align: center;
      border: 1px solid #dee2e6;
    }
    .total-box h3 {
      margin-bottom: 5px;
      font-size: 1.5rem;
    }
    .total-box .total-amount {
      font-size: 2rem;
      font-weight: bold;
      color: #198754;
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
            <a class="nav-link" href="./index.php">Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="input-daily-menu.php">Input Daily Menu</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="edit-menu-details.php">Edit Menu Details</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="monitor-menu-sales.php">Monitor Menu Sales</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="sales-reporting.php">Sales Reporting</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="manage-cashier.php">Manage Cashier</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container">
    <h2 class="mb-4">Sales Reporting</h2>
    
    <?php
    // Include database connection
    include '../../connection.php';
    
    // Get the selected date, default to today
    $selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
    
    // Get menu items and their sales data for the selected date
    $sql = "SELECT * FROM menu_items WHERE date_added = '$selected_date' ORDER BY menu_name";
    $menu_items = mysqli_query($conn, $sql);
    
    // Calculate total sales for the day
    $sql = "SELECT SUM(servings_sold * price_per_serve) as total_sales 
            FROM menu_items 
            WHERE date_added = '$selected_date'";
    $total_result = mysqli_query($conn, $sql);
    $total_row = mysqli_fetch_assoc($total_result);
    $total_daily_sales = $total_row['total_sales'] ?: 0;
    
    // Get dates that have menu items
    $sql = "SELECT DISTINCT date_added FROM menu_items ORDER BY date_added DESC";
    $dates_result = mysqli_query($conn, $sql);
    ?>
    
    <!-- Date Selection Form -->
    <div class="card mb-4">
      <h3 class="card-title">Select Date for Report</h3>
      <div>
        <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" style="display: flex; flex-wrap: wrap; gap: 20px;">
          <div style="flex: 1; min-width: 200px;">
            <label for="date" class="form-label">Select Date</label>
            <select name="date" id="date" class="form-control" onchange="this.form.submit()">
              <?php
              while($date_row = mysqli_fetch_assoc($dates_result)) {
                  $selected = ($date_row['date_added'] == $selected_date) ? 'selected' : '';
                  echo '<option value="' . $date_row['date_added'] . '" ' . $selected . '>' . 
                       $date_row['date_added'] . '</option>';
              }
              ?>
            </select>
          </div>
          <div style="flex: 0 0 auto; display: flex; align-items: flex-end;">
            <button type="submit" class="btn btn-primary">View Report</button>
          </div>
        </form>
      </div>
    </div>
    
    <!-- Daily Sales Summary -->
    <div class="total-box">
      <h3>Total Daily Sales (<?php echo $selected_date; ?>)</h3>
      <div class="total-amount">$<?php echo number_format($total_daily_sales, 2); ?></div>
    </div>
    
    <!-- Menu Sales Data -->
    <div class="card">
      <h3 class="card-title">Menu Sales Summary</h3>
      <div>
        <?php if (mysqli_num_rows($menu_items) > 0): ?>
          <table class="table">
            <thead>
              <tr>
                <th>Menu Item</th>
                <th>Price Per Serving</th>
                <th>Servings Sold</th>
                <th>Total Servings</th>
                <th>Expected Sales</th>
                <th>Actual Sales</th>
                <th>Performance</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // Reset the pointer
              mysqli_data_seek($menu_items, 0);
              
              while($item = mysqli_fetch_assoc($menu_items)) {
                  $actual_sale = $item['servings_sold'] * $item['price_per_serve'];
                  $expected_sale = $item['expected_sales'];
                  $performance = ($expected_sale > 0) ? ($actual_sale / $expected_sale) * 100 : 0;
                  
                  $performance_style = 'color: #dc3545;'; // red for bad performance
                  if ($performance >= 100) {
                      $performance_style = 'color: #28a745;'; // green for good performance
                  } elseif ($performance >= 75) {
                      $performance_style = 'color: #ffc107;'; // orange for ok performance
                  }
                  
                  echo '<tr>
                          <td>' . $item['menu_name'] . '</td>
                          <td>$' . number_format($item['price_per_serve'], 2) . '</td>
                          <td>' . $item['servings_sold'] . '</td>
                          <td>' . $item['number_of_servings'] . '</td>
                          <td>$' . number_format($expected_sale, 2) . '</td>
                          <td>$' . number_format($actual_sale, 2) . '</td>
                          <td style="' . $performance_style . '">' . round($performance) . '%</td>
                        </tr>';
              }
              ?>
            </tbody>
          </table>
        <?php else: ?>
          <div class="alert alert-info">No menu items found for this date.</div>
        <?php endif; ?>
      </div>
    </div>
    
    <!-- Sales Summary Cards -->
    <h3 class="mb-4 mt-4">Sales Analysis</h3>
    <div class="card-grid">
      <?php
      // Reset the pointer again
      mysqli_data_seek($menu_items, 0);
      
      // Calculate some statistics
      $total_expected = 0;
      $total_actual = 0;
      $best_performer = null;
      $best_performance = 0;
      $worst_performer = null;
      $worst_performance = 100;
      
      if (mysqli_num_rows($menu_items) > 0) {
          while($item = mysqli_fetch_assoc($menu_items)) {
              $actual_sale = $item['servings_sold'] * $item['price_per_serve'];
              $expected_sale = $item['expected_sales'];
              $total_expected += $expected_sale;
              $total_actual += $actual_sale;
              
              if ($expected_sale > 0) {
                  $performance = ($actual_sale / $expected_sale) * 100;
                  
                  if ($performance > $best_performance) {
                      $best_performance = $performance;
                      $best_performer = $item;
                  }
                  
                  if ($performance < $worst_performance) {
                      $worst_performance = $performance;
                      $worst_performer = $item;
                  }
              }
          }
      }
      
      // Overall Performance
      $overall_performance = ($total_expected > 0) ? ($total_actual / $total_expected) * 100 : 0;
      ?>
      
      <div class="card feature-card">
        <h3 class="card-title">Overall Performance</h3>
        <p>Expected Sales: $<?php echo number_format($total_expected, 2); ?></p>
        <p>Actual Sales: $<?php echo number_format($total_actual, 2); ?></p>
        <p style="font-weight: bold; <?php echo ($overall_performance >= 75) ? 'color: #28a745;' : 'color: #dc3545;'; ?>">
          Performance: <?php echo round($overall_performance); ?>%
        </p>
      </div>
      
      <?php if ($best_performer): ?>
      <div class="card feature-card">
        <h3 class="card-title">Best Performing Item</h3>
        <p><?php echo $best_performer['menu_name']; ?></p>
        <p>Expected: $<?php echo number_format($best_performer['expected_sales'], 2); ?></p>
        <p>Actual: $<?php echo number_format($best_performer['servings_sold'] * $best_performer['price_per_serve'], 2); ?></p>
        <p style="font-weight: bold; color: #28a745;">Performance: <?php echo round($best_performance); ?>%</p>
      </div>
      <?php endif; ?>
      
      <?php if ($worst_performer): ?>
      <div class="card feature-card">
        <h3 class="card-title">Lowest Performing Item</h3>
        <p><?php echo $worst_performer['menu_name']; ?></p>
        <p>Expected: $<?php echo number_format($worst_performer['expected_sales'], 2); ?></p>
        <p>Actual: $<?php echo number_format($worst_performer['servings_sold'] * $worst_performer['price_per_serve'], 2); ?></p>
        <p style="font-weight: bold; color: #dc3545;">Performance: <?php echo round($worst_performance); ?>%</p>
      </div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
