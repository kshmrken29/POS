<?php
// Include authentication system
require_once '../auth_session.php';
require_cashier();

// Log that cashier page was accessed
log_activity('accessed accept payment page');

// Include database connection
include '../admin/connection.php';

// Check if transaction ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: view-transactions.php');
    exit;
}

$transaction_id = $_GET['id'];

// Get transaction details
$sql = "SELECT * FROM transactions WHERE id = $transaction_id";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    header('Location: view-transactions.php');
    exit;
}

$transaction = mysqli_fetch_assoc($result);

// Process payment form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['complete_payment'])) {
    $amount_paid = $_POST['amount_paid'];
    $total_amount = $transaction['total_amount'];
    $change_amount = $amount_paid - $total_amount;
    
    // Update transaction with payment details
    $update_sql = "UPDATE transactions SET 
                  amount_paid = '$amount_paid', 
                  change_amount = '$change_amount', 
                  status = 'completed' 
                  WHERE id = $transaction_id";
                  
    if (mysqli_query($conn, $update_sql)) {
        // Redirect to receipt/change display
        header('Location: display-change.php?id=' . $transaction_id);
        exit;
    } else {
        $error_message = 'Error updating transaction: ' . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Accept Payment</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <style>
    body {
      background-color: #f8f9fa;
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
    }
    .sidebar-sticky {
      height: calc(100vh - 48px);
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
      margin-left: 300px;
      padding: 20px;
    }
    @media (max-width: 767.98px) {
      .sidebar {
        width: 100%;
        position: relative;
        padding-top: 0;
      }
      .main-content {
        margin-left: 0;
      }
    }
    .payment-card {
      max-width: 600px;
      margin: 0 auto;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar col-md-3 col-lg-2 d-md-block">
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
    <h2 class="mb-4">Accept Payment</h2>
    
    <?php if (isset($error_message)): ?>
      <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>
    
    <div class="card payment-card">
      <div class="card-header bg-primary text-white">
        <h4>Process Payment for Transaction #<?php echo $transaction_id; ?></h4>
      </div>
      <div class="card-body">
        <div class="mb-4">
          <h5>Transaction Details</h5>
          <p><strong>Date:</strong> <?php echo date('F j, Y, g:i a', strtotime($transaction['transaction_date'])); ?></p>
          <p><strong>Total Amount:</strong> ₱<?php echo number_format($transaction['total_amount'], 2); ?></p>
        </div>
        
        <form method="post" id="payment-form">
          <div class="mb-3">
            <label for="amount_paid" class="form-label">Amount Paid</label>
            <div class="input-group">
              <span class="input-group-text">₱</span>
              <input type="number" step="0.01" min="<?php echo $transaction['total_amount']; ?>" class="form-control" id="amount_paid" name="amount_paid" required>
            </div>
          </div>
          
          <div class="mb-3">
            <label for="change_amount" class="form-label">Change</label>
            <div class="input-group">
              <span class="input-group-text">₱</span>
              <input type="text" class="form-control" id="change_amount" readonly>
            </div>
          </div>
          
          <div class="d-grid">
            <button type="submit" class="btn btn-primary" name="complete_payment" id="complete-payment-btn" disabled>Complete Payment</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const amountPaidInput = document.getElementById('amount_paid');
      const changeAmountInput = document.getElementById('change_amount');
      const completePaymentBtn = document.getElementById('complete-payment-btn');
      const totalAmount = <?php echo $transaction['total_amount']; ?>;
      
      // Calculate change and validate payment
      amountPaidInput.addEventListener('input', function() {
        const amountPaid = parseFloat(this.value) || 0;
        const change = amountPaid - totalAmount;
        
        if (change >= 0) {
          changeAmountInput.value = change.toFixed(2);
          completePaymentBtn.disabled = false;
        } else {
          changeAmountInput.value = '0.00';
          completePaymentBtn.disabled = true;
        }
      });
      
      // Set initial value if needed
      if (amountPaidInput.value) {
        amountPaidInput.dispatchEvent(new Event('input'));
      }
    });
  </script>
</body>
</html>
