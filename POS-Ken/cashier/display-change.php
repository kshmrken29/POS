<?php
// Include authentication system
require_once '../auth_session.php';
require_cashier();

// Log that cashier page was accessed
log_activity('accessed display change page');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Transaction Complete</title>
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
  <div class="sidebar col-md-3 col-lg-2 d-md-block no-print">
    <div class="position-sticky sidebar-sticky">
      <a href="index.php" class="navbar-brand">Restaurant POS - Cashier</a>
      <hr class="bg-light">
      <ul class="nav flex-column">
        <li class="nav-item">
          <a class="nav-link" href="index.php">
            <i class="bi bi-speedometer2"></i> Dashboard
          </a>
        </li>
        <li class="sidebar-heading">Transactions</li>
        <li class="nav-item">
          <a class="nav-link" href="take-customer-order.php">
            <i class="bi bi-cart-plus"></i> Take Order
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="view-transactions.php">
            <i class="bi bi-search"></i> View Transactions
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="void-transaction.php">
            <i class="bi bi-x-circle"></i> Void Transaction
          </a>
        </li>
        <?php if (is_admin()): ?>
        <li class="sidebar-heading">Administration</li>
        <li class="nav-item">
          <a class="nav-link" href="../admin/dashboard.php">
            <i class="bi bi-gear"></i> Admin Panel
          </a>
        </li>
        <?php endif; ?>
        <li class="sidebar-heading">Account</li>
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
    include '../admin/connection.php';
    
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
    
    // Get transaction items
    $sql = "SELECT ti.*, m.menu_name 
            FROM transaction_items ti
            JOIN menu_items m ON ti.menu_item_id = m.id
            WHERE ti.transaction_id = $transaction_id";
    $items_result = mysqli_query($conn, $sql);
    
    if (!$items_result) {
        echo '<div class="alert alert-danger">Error fetching transaction items: ' . mysqli_error($conn) . '</div>';
        exit;
    }
    ?>
    
    <div class="receipt">
      <div class="receipt-header">
        <h1>Restaurant POS</h1>
        <p>123 Main Street, City, Country</p>
        <p>Tel: (123) 456-7890</p>
        <p>Receipt #<?php echo str_pad($transaction_id, 6, '0', STR_PAD_LEFT); ?></p>
        <p><?php echo date('F j, Y, g:i a', strtotime($transaction['transaction_date'])); ?></p>
      </div>
      
      <h4>Order Items</h4>
      <table class="table">
        <thead>
          <tr>
            <th>Item</th>
            <th class="text-center">Qty</th>
            <th class="text-end">Price</th>
            <th class="text-end">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
            <tr>
              <td><?php echo $item['menu_name']; ?></td>
              <td class="text-center"><?php echo $item['quantity']; ?></td>
              <td class="text-end">₱<?php echo number_format($item['price_per_item'], 2); ?></td>
              <td class="text-end">₱<?php echo number_format($item['subtotal'], 2); ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
      
      <div class="row">
        <div class="col-md-6 offset-md-6">
          <table class="table table-borderless">
            <tr>
              <th>Total:</th>
              <td class="text-end">₱<?php echo number_format($transaction['total_amount'], 2); ?></td>
            </tr>
            <tr>
              <th>Amount Paid:</th>
              <td class="text-end">₱<?php echo number_format($transaction['amount_paid'], 2); ?></td>
            </tr>
            <tr>
              <th>Change:</th>
              <td class="text-end change-highlight">₱<?php echo number_format($transaction['change_amount'], 2); ?></td>
            </tr>
          </table>
        </div>
      </div>
      
      <div class="receipt-footer">
        <p>Thank you for your purchase!</p>
        <p>We appreciate your business.</p>
      </div>
      
      <div class="d-flex justify-content-center mt-4 no-print">
        <button class="btn btn-primary me-2" onclick="window.print();">
          <i class="bi bi-printer"></i> Print Receipt
        </button>
        <a href="take-customer-order.php" class="btn btn-success">
          <i class="bi bi-cart"></i> New Order
        </a>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
