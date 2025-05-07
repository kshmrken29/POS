<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Transactions</title>
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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>

  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
      <a class="navbar-brand" href="index.php">Restaurant POS - Cashier</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#cashierNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="cashierNavbar">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="index.php">Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="take-customer-order.php">Take Order</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="view-transactions.php">Transactions</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="../admin/index.php">Admin Panel</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container">
    <h2 class="mb-4">Transaction History</h2>
    
    <?php
    // Include database connection
    include '../admin/connection.php';
    
    // Get transaction filter options
    $date_filter = isset($_GET['date']) ? $_GET['date'] : '';
    $status_filter = isset($_GET['status']) ? $_GET['status'] : '';
    
    // Build query based on filters
    $where_clauses = [];
    $params = [];
    
    if (!empty($date_filter)) {
        $where_clauses[] = "DATE(t.transaction_date) = '$date_filter'";
        $params[] = "date=$date_filter";
    }
    
    if (!empty($status_filter)) {
        $where_clauses[] = "t.status = '$status_filter'";
        $params[] = "status=$status_filter";
    }
    
    $where_sql = empty($where_clauses) ? "" : "WHERE " . implode(" AND ", $where_clauses);
    
    // Get list of transactions with filter
    $sql = "SELECT t.*, COUNT(ti.id) as item_count, SUM(ti.quantity) as total_items
            FROM transactions t
            LEFT JOIN transaction_items ti ON t.id = ti.transaction_id
            $where_sql
            GROUP BY t.id
            ORDER BY t.transaction_date DESC
            LIMIT 100";
            
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        echo '<div class="alert alert-danger">Error: ' . mysqli_error($conn) . '</div>';
    }
    
    // Get unique dates for filter
    $dates_sql = "SELECT DISTINCT DATE(transaction_date) as date FROM transactions ORDER BY date DESC";
    $dates_result = mysqli_query($conn, $dates_sql);
    
    ?>
    
    <!-- Filter options -->
    <div class="card mb-4">
      <div class="card-header bg-primary text-white">
        <h5>Filter Transactions</h5>
      </div>
      <div class="card-body">
        <form method="get" class="row g-3">
          <div class="col-md-4">
            <label for="date" class="form-label">Date</label>
            <select class="form-select" id="date" name="date">
              <option value="">All Dates</option>
              <?php while ($date_row = mysqli_fetch_assoc($dates_result)): ?>
                <option value="<?php echo $date_row['date']; ?>" <?php echo ($date_filter == $date_row['date']) ? 'selected' : ''; ?>>
                  <?php echo date('F j, Y', strtotime($date_row['date'])); ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status">
              <option value="">All Statuses</option>
              <option value="completed" <?php echo ($status_filter == 'completed') ? 'selected' : ''; ?>>Completed</option>
              <option value="void_requested" <?php echo ($status_filter == 'void_requested') ? 'selected' : ''; ?>>Void Requested</option>
              <option value="voided" <?php echo ($status_filter == 'voided') ? 'selected' : ''; ?>>Voided</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">&nbsp;</label>
            <div class="d-grid">
              <button type="submit" class="btn btn-primary">Apply Filters</button>
            </div>
          </div>
        </form>
      </div>
    </div>
    
    <!-- Transaction List -->
    <div class="card">
      <div class="card-header bg-primary text-white">
        <h5>Transactions</h5>
      </div>
      <div class="card-body">
        <?php if (mysqli_num_rows($result) > 0): ?>
          <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Date & Time</th>
                  <th>Total Amount</th>
                  <th>Amount Paid</th>
                  <th>Change</th>
                  <th>Items</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($transaction = mysqli_fetch_assoc($result)): 
                  $status_class = 'bg-success';
                  if ($transaction['status'] == 'void_requested') {
                      $status_class = 'bg-warning';
                  } else if ($transaction['status'] == 'voided') {
                      $status_class = 'bg-danger';
                  }
                ?>
                  <tr>
                    <td><?php echo $transaction['id']; ?></td>
                    <td><?php echo date('M d, Y h:i A', strtotime($transaction['transaction_date'])); ?></td>
                    <td>$<?php echo number_format($transaction['total_amount'], 2); ?></td>
                    <td>$<?php echo number_format($transaction['amount_paid'], 2); ?></td>
                    <td>$<?php echo number_format($transaction['change_amount'], 2); ?></td>
                    <td><?php echo $transaction['total_items'] ?: 0; ?> items</td>
                    <td><span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($transaction['status']); ?></span></td>
                    <td>
                      <a href="display-change.php?id=<?php echo $transaction['id']; ?>" class="btn btn-sm btn-info">
                        <i class="bi bi-eye"></i> View
                      </a>
                      <?php if ($transaction['status'] == 'completed'): ?>
                        <a href="void-transaction.php" class="btn btn-sm btn-danger">
                          <i class="bi bi-x-circle"></i> Void
                        </a>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="alert alert-info">No transactions found matching your criteria.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 