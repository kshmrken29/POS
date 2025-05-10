<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Transaction Details</title>
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
  <div class="sidebar col-md-3 col-lg-2 d-md-block bg-dark no-print">
    <div class="position-sticky sidebar-sticky">
      <a href="index.php" class="navbar-brand">Restaurant POS - Admin</a>
      <hr class="bg-light">
      <ul class="nav flex-column">
        <li class="nav-item">
          <a class="nav-link" href="index.php">
            <i class="bi bi-speedometer2"></i> Dashboard
          </a>
        </li>
        <li class="sidebar-heading">Menu Management</li>
        <li class="nav-item">
          <a class="nav-link" href="MenuManagement/input-daily-menu.php">
            <i class="bi bi-plus-circle"></i> Input Daily Menu
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="MenuManagement/edit-menu-details.php">
            <i class="bi bi-pencil-square"></i> Edit Menu Details
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="MenuManagement/monitor-menu-sales.php">
            <i class="bi bi-graph-up"></i> Monitor Menu Sales
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="MenuManagement/sales-reporting.php">
            <i class="bi bi-file-earmark-bar-graph"></i> Sales Reporting
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="MenuManagement/manage-cashier.php">
            <i class="bi bi-person-badge"></i> Manage Cashier
          </a>
        </li>
        <li class="sidebar-heading">Inventory</li>
        <li class="nav-item">
          <a class="nav-link" href="InventoryManagement/input-purchase-details.php">
            <i class="bi bi-cart-plus"></i> Purchase Details
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="InventoryManagement/input-daily-usage.php">
            <i class="bi bi-card-checklist"></i> Daily Usage
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="InventoryManagement/remaining-stock-view.php">
            <i class="bi bi-boxes"></i> Remaining Stock
          </a>
        </li>
        <li class="sidebar-heading">Administration</li>
        <li class="nav-item">
          <a class="nav-link active" href="process-void-requests.php">
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
    <?php
    // Include database connection
    include 'connection.php';
    
    // Check if transaction ID is provided
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        echo '<div class="alert alert-danger">Transaction ID not provided.</div>';
        exit;
    }
    
    $transaction_id = $_GET['id'];
    
    // Get transaction details
    $sql = "SELECT * FROM transactions WHERE id = $transaction_id";
    $result = mysqli_query($conn, $sql);
    
    if (!$result || mysqli_num_rows($result) === 0) {
        echo '<div class="alert alert-danger">Transaction not found.</div>';
        exit;
    }
    
    $transaction = mysqli_fetch_assoc($result);
    
    // Get transaction items with menu details
    $sql = "SELECT ti.*, m.menu_name, m.price_per_serve, m.approximate_cost
            FROM transaction_items ti
            JOIN menu_items m ON ti.menu_item_id = m.id
            WHERE ti.transaction_id = $transaction_id";
    $items_result = mysqli_query($conn, $sql);
    
    if (!$items_result) {
        echo '<div class="alert alert-danger">Error fetching transaction items: ' . mysqli_error($conn) . '</div>';
        exit;
    }
    
    // Get status badge class
    $status_class = 'bg-success';
    $status_text = 'Completed';
    
    if ($transaction['status'] == 'void_requested') {
        $status_class = 'bg-warning';
        $status_text = 'Void Requested';
    } else if ($transaction['status'] == 'voided') {
        $status_class = 'bg-danger';
        $status_text = 'Voided';
    }
    ?>
    
    <div class="mb-4 no-print">
      <a href="process-void-requests.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Void Requests
      </a>
      <button onclick="window.print();" class="btn btn-primary ms-2">
        <i class="bi bi-printer"></i> Print
      </button>
    </div>
    
    <div class="receipt">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Transaction #<?php echo str_pad($transaction_id, 6, '0', STR_PAD_LEFT); ?></h2>
        <span class="badge <?php echo $status_class; ?> fs-6"><?php echo $status_text; ?></span>
      </div>
      
      <div class="card mb-4">
        <div class="card-header bg-dark text-white">
          <h5 class="mb-0">Transaction Summary</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <p><strong>Date:</strong> <?php echo date('F j, Y, g:i a', strtotime($transaction['transaction_date'])); ?></p>
              <p><strong>Total Items:</strong> <?php echo mysqli_num_rows($items_result); ?></p>
            </div>
            <div class="col-md-6">
              <p><strong>Total Amount:</strong> $<?php echo number_format($transaction['total_amount'], 2); ?></p>
              <p><strong>Amount Paid:</strong> $<?php echo number_format($transaction['amount_paid'], 2); ?></p>
              <p><strong>Change:</strong> $<?php echo number_format($transaction['change_amount'], 2); ?></p>
            </div>
          </div>
        </div>
      </div>
      
      <div class="card">
        <div class="card-header bg-dark text-white">
          <h5 class="mb-0">Items Purchased</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Item</th>
                  <th class="text-center">Quantity</th>
                  <th class="text-end">Unit Price</th>
                  <th class="text-end">Subtotal</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                $total_items = 0;
                $total_cost = 0;
                $profit = 0;
                
                mysqli_data_seek($items_result, 0); // Reset result pointer
                while ($item = mysqli_fetch_assoc($items_result)): 
                  $total_items += $item['quantity'];
                  $item_cost = $item['approximate_cost'] * $item['quantity'];
                  $total_cost += $item_cost;
                  $item_profit = $item['subtotal'] - $item_cost;
                  $profit += $item_profit;
                ?>
                  <tr>
                    <td><?php echo $item['menu_name']; ?></td>
                    <td class="text-center"><?php echo $item['quantity']; ?></td>
                    <td class="text-end">$<?php echo number_format($item['price_per_item'], 2); ?></td>
                    <td class="text-end">$<?php echo number_format($item['subtotal'], 2); ?></td>
                  </tr>
                <?php endwhile; ?>
                <tr class="table-dark">
                  <td colspan="3" class="text-end fw-bold">Total:</td>
                  <td class="text-end fw-bold">$<?php echo number_format($transaction['total_amount'], 2); ?></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      
      <?php if ($transaction['status'] == 'void_requested'): ?>
      <div class="mt-4 d-flex justify-content-center no-print">
        <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#approveModal">
          <i class="bi bi-check-circle"></i> Approve Void
        </button>
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
          <i class="bi bi-x-circle"></i> Reject Void
        </button>
      </div>
      <?php endif; ?>
    </div>
  </div> <!-- End of main-content -->
  
  <!-- Approve Modal -->
  <div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title">Approve Void Request</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to approve the void request for Transaction #<?php echo $transaction_id; ?>?</p>
          <p><strong>Warning:</strong> This will restore the menu item servings and mark the transaction as voided.</p>
        </div>
        <div class="modal-footer">
          <form method="post" action="process-void-requests.php">
            <input type="hidden" name="transaction_id" value="<?php echo $transaction_id; ?>">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="approve_void" class="btn btn-success">Approve Void</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Reject Modal -->
  <div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title">Reject Void Request</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to reject the void request for Transaction #<?php echo $transaction_id; ?>?</p>
        </div>
        <div class="modal-footer">
          <form method="post" action="process-void-requests.php">
            <input type="hidden" name="transaction_id" value="<?php echo $transaction_id; ?>">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="reject_void" class="btn btn-danger">Reject Void</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>