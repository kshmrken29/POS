<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Void Transaction</title>
  <link rel="stylesheet" href="../style.css">
  <style>
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.5);
    }
    .modal-content {
      background-color: white;
      margin: 15% auto;
      padding: 20px;
      border: 1px solid #ddd;
      width: 80%;
      max-width: 500px;
    }
    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid #ddd;
      padding-bottom: 10px;
      margin-bottom: 15px;
    }
    .modal-footer {
      border-top: 1px solid #ddd;
      padding-top: 15px;
      margin-top: 20px;
      text-align: right;
    }
  </style>
</head>
<body>

  <div class="navbar">
    <div class="navbar-container">
      <a class="navbar-brand" href="index.php">Restaurant POS - Cashier</a>
      <ul class="navbar-menu">
        <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="take-customer-order.php">Take Order</a></li>
        <li class="nav-item"><a class="nav-link" href="view-transactions.php">Transactions</a></li>
        <li class="nav-item"><a class="nav-link active" href="void-transaction.php">Void Transaction</a></li>
        <li class="nav-item"><a class="nav-link" href="../admin/index.php">Admin Panel</a></li>
        <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
      </ul>
    </div>
  </div>

  <div class="container">
    <h2 class="mb-4">Void Transaction</h2>
    
    <div class="alert alert-warning">
      <strong>Note:</strong> Voiding a transaction requires admin approval. This will restore menu item servings.
    </div>
    
    <?php
    // Include database connection
    include '../admin/connection.php';
    
    // Handle void request
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['void_transaction'])) {
        $transaction_id = $_POST['transaction_id'];
        $reason = $_POST['void_reason'];
        
        // Update transaction status to 'void_requested'
        $sql = "UPDATE transactions SET status = 'void_requested' WHERE id = $transaction_id";
        
        if (mysqli_query($conn, $sql)) {
            // Add void reason to a separate table or log for admin review
            echo '<div class="alert alert-success">
                    Void request submitted successfully. An administrator will review this request.
                  </div>';
        } else {
            echo '<div class="alert alert-danger">
                    Error submitting void request: ' . mysqli_error($conn) . '
                  </div>';
        }
    }
    
    // Get recent transactions
    $sql = "SELECT t.*, COUNT(ti.id) as item_count 
            FROM transactions t
            LEFT JOIN transaction_items ti ON t.id = ti.transaction_id
            WHERE t.status = 'completed'
            GROUP BY t.id
            ORDER BY t.transaction_date DESC
            LIMIT 20";
            
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        echo '<div class="alert alert-danger">Error: ' . mysqli_error($conn) . '</div>';
    }
    ?>
    
    <div class="card">
      <h3 class="card-title">Recent Transactions</h3>
      <div>
        <?php if (mysqli_num_rows($result) > 0): ?>
          <div>
            <table class="table">
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
                    <td>$<?php echo number_format($transaction['total_amount'], 2); ?></td>
                    <td><?php echo $transaction['item_count']; ?> items</td>
                    <td><?php echo ucfirst($transaction['status']); ?></td>
                    <td>
                      <button type="button" class="btn btn-danger" 
                              onclick="showVoidModal(<?php echo $transaction['id']; ?>, 
                                '<?php echo date('M d, Y H:i', strtotime($transaction['transaction_date'])); ?>', 
                                '$<?php echo number_format($transaction['total_amount'], 2); ?>')">
                        Void
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
  <div class="modal" id="voidModal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Void Transaction</h3>
        <button type="button" class="btn-close" onclick="closeModal()">×</button>
      </div>
      <form method="post">
        <div>
          <p>Are you sure you want to request to void this transaction?</p>
          <div class="form-group">
            <label class="form-label">Transaction ID:</label>
            <input type="text" class="form-control" id="modal-transaction-id" readonly>
            <input type="hidden" name="transaction_id" id="hidden-transaction-id">
          </div>
          <div class="form-group">
            <label class="form-label">Date:</label>
            <input type="text" class="form-control" id="modal-transaction-date" readonly>
          </div>
          <div class="form-group">
            <label class="form-label">Amount:</label>
            <input type="text" class="form-control" id="modal-transaction-amount" readonly>
          </div>
          <div class="form-group">
            <label for="void_reason" class="form-label">Reason for Void:</label>
            <textarea class="form-control" id="void_reason" name="void_reason" rows="3" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
          <button type="submit" name="void_transaction" class="btn btn-danger">Submit Void Request</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Modal functions
    function showVoidModal(id, date, amount) {
      document.getElementById('modal-transaction-id').value = id;
      document.getElementById('hidden-transaction-id').value = id;
      document.getElementById('modal-transaction-date').value = date;
      document.getElementById('modal-transaction-amount').value = amount;
      document.getElementById('voidModal').style.display = 'block';
    }
    
    function closeModal() {
      document.getElementById('voidModal').style.display = 'none';
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
      if (event.target === document.getElementById('voidModal')) {
        closeModal();
      }
    }
  </script>
</body>
</html>
