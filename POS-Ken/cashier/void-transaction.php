<?php
// Include authentication system
require_once '../auth_session.php';
require_cashier();

// Log that cashier page was accessed
log_activity('accessed void transaction page');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Void Transaction</title>
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
          <a class="nav-link active" href="void-transaction.php">
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
    <h2 class="mb-4">Void Transaction</h2>
    
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-warning">
          <i class="bi bi-exclamation-triangle-fill"></i>
          <strong>Note:</strong> Voiding a transaction requires admin approval. This will restore menu item servings.
        </div>
      </div>
    </div>
    
    <?php
    // Include database connection
    include '../admin/connection.php';
    
    // Handle void request
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['void_transaction'])) {
        $transaction_id = $_POST['transaction_id'];
        $reason = $_POST['void_reason'];
        
        // Update transaction status to 'void_requested'
        $sql = "UPDATE transactions SET status = 'void_requested', void_processed = FALSE WHERE id = $transaction_id";
        
        if (mysqli_query($conn, $sql)) {
            // Add void reason to a separate table or log for admin review
            echo '<div class="alert alert-success">
                    <i class="bi bi-check-circle-fill"></i>
                    Void request submitted successfully. An administrator will review this request.
                  </div>';
        } else {
            echo '<div class="alert alert-danger">
                    <i class="bi bi-x-circle-fill"></i>
                    Error submitting void request: ' . mysqli_error($conn) . '
                  </div>';
        }
    }
    
    // Get recent transactions
    $sql = "SELECT t.*, COUNT(ti.id) as item_count 
            FROM transactions t
            LEFT JOIN transaction_items ti ON t.id = ti.transaction_id
            WHERE t.status = 'completed' AND (t.void_processed IS NULL OR t.void_processed = FALSE)
            GROUP BY t.id
            ORDER BY t.transaction_date DESC
            LIMIT 20";
            
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        echo '<div class="alert alert-danger">Error: ' . mysqli_error($conn) . '</div>';
    }
    ?>
    
    <div class="card">
      <div class="card-header bg-primary text-white">
        <h4>Recent Transactions</h4>
      </div>
      <div class="card-body">
        <?php if (mysqli_num_rows($result) > 0): ?>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Date</th>
                  <th>Total Amount</th>
                  <th>Items</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($transaction = mysqli_fetch_assoc($result)): ?>
                  <tr>
                    <td><?php echo $transaction['id']; ?></td>
                    <td><?php echo date('M d, Y H:i', strtotime($transaction['transaction_date'])); ?></td>
                    <td>₱<?php echo number_format($transaction['total_amount'], 2); ?></td>
                    <td><?php echo $transaction['item_count']; ?> items</td>
                    <td><?php echo ucfirst($transaction['status']); ?></td>
                    <td>
                      <button type="button" class="btn btn-sm btn-danger" 
                              data-bs-toggle="modal" 
                              data-bs-target="#voidModal" 
                              data-id="<?php echo $transaction['id']; ?>"
                              data-date="<?php echo date('M d, Y H:i', strtotime($transaction['transaction_date'])); ?>"
                              data-amount="₱<?php echo number_format($transaction['total_amount'], 2); ?>">
                        <i class="bi bi-x-circle"></i> Void
                      </button>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="alert alert-info">No completed transactions found.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  
  <!-- Void Confirmation Modal -->
  <div class="modal fade" id="voidModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title">Void Transaction</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="post">
          <div class="modal-body">
            <p>Are you sure you want to request to void this transaction?</p>
            <div class="mb-3">
              <label class="form-label">Transaction ID:</label>
              <input type="text" class="form-control" id="modal-transaction-id" readonly>
              <input type="hidden" name="transaction_id" id="hidden-transaction-id">
            </div>
            <div class="mb-3">
              <label class="form-label">Date:</label>
              <input type="text" class="form-control" id="modal-transaction-date" readonly>
            </div>
            <div class="mb-3">
              <label class="form-label">Amount:</label>
              <input type="text" class="form-control" id="modal-transaction-amount" readonly>
            </div>
            <div class="mb-3">
              <label for="void_reason" class="form-label">Reason for Void:</label>
              <textarea class="form-control" id="void_reason" name="void_reason" rows="3" required></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="void_transaction" class="btn btn-danger">Submit Void Request</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Update modal with transaction details
    document.addEventListener('DOMContentLoaded', function() {
      const voidModal = document.getElementById('voidModal');
      
      voidModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const date = button.getAttribute('data-date');
        const amount = button.getAttribute('data-amount');
        
        document.getElementById('modal-transaction-id').value = id;
        document.getElementById('hidden-transaction-id').value = id;
        document.getElementById('modal-transaction-date').value = date;
        document.getElementById('modal-transaction-amount').value = amount;
      });
    });
  </script>
</body>
</html>
