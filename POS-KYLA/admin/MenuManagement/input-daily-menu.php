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

    // Check if the form has been submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $menu_name = $_POST['menu_name'];
        $approximate_cost = $_POST['approximate_cost'];
        $number_of_servings = $_POST['number_of_servings'];
        $price_per_serve = $_POST['price_per_serve'];
        $expected_sales = $number_of_servings * $price_per_serve;
        $date_added = date('Y-m-d');

        // Prepare SQL statement
        $sql = "INSERT INTO menu_items (menu_name, approximate_cost, number_of_servings, price_per_serve, expected_sales, servings_sold, date_added) 
                VALUES ('$menu_name', '$approximate_cost', '$number_of_servings', '$price_per_serve', '$expected_sales', 0, '$date_added')";

        // Execute SQL and check if successful
        if (mysqli_query($conn, $sql)) {
            echo '<div class="alert alert-success" role="alert">Menu item added successfully!</div>';
        } else {
            echo '<div class="alert alert-danger" role="alert">Error: ' . mysqli_error($conn) . '</div>';
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
  </script>
</body>
</html>
