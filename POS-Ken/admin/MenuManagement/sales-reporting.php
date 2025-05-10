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
  <title>Sales Reporting</title>
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
          <a class="nav-link" href="monitor-menu-sales.php">
            <i class="bi bi-graph-up"></i> Monitor Menu Sales
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="sales-reporting.php">
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
          <div class="total-amount">₱<?php echo number_format($total_daily_sales, 2); ?></div>
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
                                  <td>₱' . number_format($expected_sale, 2) . '</td>
                                  <td>₱' . number_format($actual_sale, 2) . '</td>
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
  </div> <!-- End of main-content -->

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
