<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Transaction Complete</title>
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
    .receipt {
      max-width: 600px;
      margin: 0 auto;
      background-color: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .receipt-header {
      text-align: center;
      margin-bottom: 20px;
      padding-bottom: 20px;
      border-bottom: 1px dashed #ddd;
    }
    .receipt-footer {
      text-align: center;
      margin-top: 20px;
      padding-top: 20px;
      border-top: 1px dashed #ddd;
    }
    .change-highlight {
      font-size: 2rem;
      font-weight: bold;
      color: #198754;
    }
    @media print {
      .no-print {
        display: none !important;
      }
      body {
        background-color: white;
      }
      .receipt {
        box-shadow: none;
        max-width: 100%;
      }
    }
  </style>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>

  <nav class="navbar navbar-expand-lg navbar-dark bg-primary no-print">
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
            <a class="nav-link" href="../admin/index.php">Admin Panel</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container">
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
              <td class="text-end">$<?php echo number_format($item['price_per_item'], 2); ?></td>
              <td class="text-end">$<?php echo number_format($item['subtotal'], 2); ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
      
      <div class="row">
        <div class="col-md-6 offset-md-6">
          <table class="table table-borderless">
            <tr>
              <th>Total:</th>
              <td class="text-end">$<?php echo number_format($transaction['total_amount'], 2); ?></td>
            </tr>
            <tr>
              <th>Amount Paid:</th>
              <td class="text-end">$<?php echo number_format($transaction['amount_paid'], 2); ?></td>
            </tr>
            <tr>
              <th>Change:</th>
              <td class="text-end change-highlight">$<?php echo number_format($transaction['change_amount'], 2); ?></td>
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
