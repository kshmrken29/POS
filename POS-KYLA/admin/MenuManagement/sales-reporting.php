<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sales Reporting</title>
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
    .total-amount {
      font-size: 36px; /* Make the total sales number bigger */
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

  <div class="container py-4">
    <div class="main-container">
      <h2 class="page-header">Sales Reporting</h2>
      
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
          <div class="card-header">
              <h4>Select Date for Report</h4>
          </div>
          <div class="card-body">
              <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="row g-3 align-items-end">
                  <div class="col-md-6">
                      <label for="date" class="form-label">Select Date</label>
                      <select name="date" id="date" class="form-select" onchange="this.form.submit()">
                          <?php
                          while($date_row = mysqli_fetch_assoc($dates_result)) {
                              $selected = ($date_row['date_added'] == $selected_date) ? 'selected' : '';
                              echo '<option value="' . $date_row['date_added'] . '" ' . $selected . '>' . 
                                   $date_row['date_added'] . '</option>';
                          }
                          ?>
                      </select>
                  </div>
                  <div class="col-md-2">
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
          <div class="card-header">
              <h4>Menu Sales Summary</h4>
          </div>
          <div class="card-body">
              <div class="table-responsive">
                  <table class="table table-striped">
                      <thead>
                          <tr>
                              <th>Menu Item</th>
                              <th>Price Per Serving</th>
                              <th>Servings Sold</th>
                              <th>Total Sales</th>
                          </tr>
                      </thead>
                      <tbody>
                          <?php
                          if (mysqli_num_rows($menu_items) > 0) {
                              // Reset the pointer
                              mysqli_data_seek($menu_items, 0);
                              
                              while($item = mysqli_fetch_assoc($menu_items)) {
                                  $actual_sale = $item['servings_sold'] * $item['price_per_serve'];
                                  
                                  echo '<tr>
                                          <td>' . $item['menu_name'] . '</td>
                                          <td>$' . number_format($item['price_per_serve'], 2) . '</td>
                                          <td>' . $item['servings_sold'] . '</td>
                                          <td>$' . number_format($actual_sale, 2) . '</td>
                                        </tr>';
                              }
                          } else {
                              echo '<tr><td colspan="4" class="text-center">No menu items found for this date</td></tr>';
                          }
                          ?>
                      </tbody>
                  </table>
              </div>
          </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
