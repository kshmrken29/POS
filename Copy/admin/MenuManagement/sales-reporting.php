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

  <div class="container">
    <h2 class="mb-4">Sales Reporting</h2>

    <?php
    include '../../connection.php';

    $selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

    $sql = "SELECT * FROM menu_items WHERE date_added = '$selected_date' ORDER BY menu_name";
    $menu_items = mysqli_query($conn, $sql);

    $sql = "SELECT SUM(servings_sold * price_per_serve) as total_sales 
            FROM menu_items 
            WHERE date_added = '$selected_date'";
    $total_result = mysqli_query($conn, $sql);
    $total_row = mysqli_fetch_assoc($total_result);
    $total_daily_sales = $total_row['total_sales'] ?: 0;

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
    <div class="row mb-4">
      <div class="col-md-12">
        <div class="total-box">
          <h3>Total Daily Sales (<?php echo $selected_date; ?>)</h3>
          <div class="total-amount">$<?php echo number_format($total_daily_sales, 2); ?></div>
        </div>
      </div>
    </div>

    <!-- Menu Sales Data -->
    <div class="row">
      <div class="col-lg-12">
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
                    <th>Expected Sales</th>
                    <th>Actual Sales</th>
                    <th>Percentage</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  if (mysqli_num_rows($menu_items) > 0) {
                      mysqli_data_seek($menu_items, 0);
                      while($item = mysqli_fetch_assoc($menu_items)) {
                          $actual_sale = $item['servings_sold'] * $item['price_per_serve'];
                          $expected_sale = $item['expected_sales'];
                          $performance = ($expected_sale > 0) ? ($actual_sale / $expected_sale) * 100 : 0;

                          $performance_class = 'text-danger';
                          if ($performance >= 100) {
                              $performance_class = 'text-success';
                          } elseif ($performance >= 75) {
                              $performance_class = 'text-warning';
                          }

                          echo '<tr>
                                  <td>' . $item['menu_name'] . '</td>
                                  <td>$' . number_format($expected_sale, 2) . '</td>
                                  <td>$' . number_format($actual_sale, 2) . '</td>
                                  <td class="' . $performance_class . '">' . round($performance) . '%</td>
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
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
