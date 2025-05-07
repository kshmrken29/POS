<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Transactions</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>

  <div class="navbar">
    <div class="navbar-container">
      <a class="navbar-brand" href="index.php">Restaurant POS - Cashier</a>
      <ul class="navbar-menu">
        <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="take-customer-order.php">Take Order</a></li>
        <li class="nav-item"><a class="nav-link active" href="view-transactions.php">Transactions</a></li>
        <li class="nav-item"><a class="nav-link" href="../admin/index.php">Admin Panel</a></li>
        <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
      </ul>
    </div>
  </div>

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
      <h3 class="card-title">Filter Transactions</h3>
      <div>
        <form method="get" style="display: flex; flex-wrap: wrap; gap: 20px;">
          <div style="flex: 1; min-width: 200px;">
            <label for="date" class="form-label">Date</label>
            <select class="form-control" id="date" name="date">
              <option value="">All Dates</option>
              <?php while ($date_row = mysqli_fetch_assoc($dates_result)): ?>
                <option value="<?php echo $date_row['date']; ?>" <?php echo ($date_filter == $date_row['date']) ? 'selected' : ''; ?>>
                  <?php echo date('F j, Y', strtotime($date_row['date'])); ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>
          <div style="flex: 1; min-width: 200px;">
            <label for="status" class="form-label">Status</label>
            <select class="form-control" id="status" name="status">
              <option value="">All Statuses</option>
              <option value="completed" <?php echo ($status_filter == 'completed') ? 'selected' : ''; ?>>Completed</option>
              <option value="void_requested" <?php echo ($status_filter == 'void_requested') ? 'selected' : ''; ?>>Void Requested</option>
              <option value="voided" <?php echo ($status_filter == 'voided') ? 'selected' : ''; ?>>Voided</option>
            </select>
          </div>
          <div style="flex: 1; min-width: 200px; display: flex; align-items: flex-end;">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Apply Filters</button>
          </div>
        </form>
      </div>
    </div>
    
    <!-- Transaction List -->
    <div class="card">
      <h3 class="card-title">Transactions</h3>
      <div>
        <?php if (mysqli_num_rows($result) > 0): ?>
          <div>
            <table class="table">
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
                  $status_style = '';
                  if ($transaction['status'] == 'completed') {
                      $status_style = 'background-color: #d4edda; padding: 3px 8px; border-radius: 4px;';
                  } else if ($transaction['status'] == 'void_requested') {
                      $status_style = 'background-color: #fff3cd; padding: 3px 8px; border-radius: 4px;';
                  } else if ($transaction['status'] == 'voided') {
                      $status_style = 'background-color: #f8d7da; padding: 3px 8px; border-radius: 4px;';
                  }
                ?>
                  <tr>
                    <td><?php echo $transaction['id']; ?></td>
                    <td><?php echo date('M d, Y h:i A', strtotime($transaction['transaction_date'])); ?></td>
                    <td>$<?php echo number_format($transaction['total_amount'], 2); ?></td>
                    <td>$<?php echo number_format($transaction['amount_paid'], 2); ?></td>
                    <td>$<?php echo number_format($transaction['change_amount'], 2); ?></td>
                    <td><?php echo $transaction['total_items'] ?: 0; ?> items</td>
                    <td><span style="<?php echo $status_style; ?>"><?php echo ucfirst($transaction['status']); ?></span></td>
                    <td>
                      <a href="display-change.php?id=<?php echo $transaction['id']; ?>" class="btn btn-primary">
                        View
                      </a>
                      <?php if ($transaction['status'] == 'completed'): ?>
                        <a href="void-transaction.php?id=<?php echo $transaction['id']; ?>" class="btn btn-danger">
                          Void
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

</body>
</html> 