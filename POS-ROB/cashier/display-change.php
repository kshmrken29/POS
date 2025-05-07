<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Transaction Complete</title>
  <link rel="stylesheet" href="../style.css">
  <style>
    .receipt {
      max-width: 600px;
      margin: 0 auto;
      background-color: white;
      padding: 30px;
      border-radius: 4px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
      font-size: 1.5rem;
      font-weight: bold;
      color: #28a745;
    }
    .summary-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }
    .summary-table th {
      text-align: left;
      padding: 5px 0;
    }
    .summary-table td {
      text-align: right;
      padding: 5px 0;
    }
    .text-center {
      text-align: center;
    }
    .text-end {
      text-align: right;
    }
    .button-group {
      display: flex;
      justify-content: center;
      margin-top: 20px;
      gap: 10px;
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
</head>
<body>

  <div class="navbar no-print">
    <div class="navbar-container">
      <a class="navbar-brand" href="index.php">Restaurant POS - Cashier</a>
      <ul class="navbar-menu">
        <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="take-customer-order.php">Take Order</a></li>
        <li class="nav-item"><a class="nav-link" href="view-transactions.php">Transactions</a></li>
        <li class="nav-item"><a class="nav-link" href="../admin/index.php">Admin Panel</a></li>
        <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
      </ul>
    </div>
  </div>

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
      
      <h3>Order Items</h3>
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
      
      <div style="margin-left: auto; width: 50%;">
        <table class="summary-table">
          <tr>
            <th>Total:</th>
            <td>$<?php echo number_format($transaction['total_amount'], 2); ?></td>
          </tr>
          <tr>
            <th>Amount Paid:</th>
            <td>$<?php echo number_format($transaction['amount_paid'], 2); ?></td>
          </tr>
          <tr>
            <th>Change:</th>
            <td class="change-highlight">$<?php echo number_format($transaction['change_amount'], 2); ?></td>
          </tr>
        </table>
      </div>
      
      <div class="receipt-footer">
        <p>Thank you for your purchase!</p>
        <p>We appreciate your business.</p>
      </div>
      
      <div class="button-group no-print">
        <button class="btn btn-primary" onclick="window.print();">
          Print Receipt
        </button>
        <a href="take-customer-order.php" class="btn btn-success">
          New Order
        </a>
      </div>
    </div>
  </div>

</body>
</html>
