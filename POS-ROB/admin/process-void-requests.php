<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Process Void Requests</title>
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
      <a class="navbar-brand" href="index.php">Restaurant POS - Admin</a>
      <ul class="navbar-menu">
        <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link active" href="process-void-requests.php">Process Void Requests</a></li>
        <li class="nav-item"><a class="nav-link" href="../cashier/index.php">Cashier Panel</a></li>
        <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
      </ul>
    </div>
  </div>

  <div class="container">
    <h2 class="mb-4">Process Void Requests</h2>
    
    <?php
    // Include database connection
    include 'connection.php';
    
    // Handle approval/rejection
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['approve_void'])) {
            $transaction_id = $_POST['transaction_id'];
            
            // Start transaction to ensure data integrity
            mysqli_begin_transaction($conn);
            
            try {
                // Get transaction items
                $items_sql = "SELECT menu_item_id, quantity FROM transaction_items WHERE transaction_id = $transaction_id";
                $items_result = mysqli_query($conn, $items_sql);
                
                if (!$items_result) {
                    throw new Exception("Error getting transaction items: " . mysqli_error($conn));
                }
                
                // Update each menu item to return servings
                while ($item = mysqli_fetch_assoc($items_result)) {
                    $menu_item_id = $item['menu_item_id'];
                    $quantity = $item['quantity'];
                    
                    $update_sql = "UPDATE menu_items 
                                  SET servings_sold = servings_sold - $quantity 
                                  WHERE id = $menu_item_id";
                    
                    if (!mysqli_query($conn, $update_sql)) {
                        throw new Exception("Error updating menu item: " . mysqli_error($conn));
                    }
                }
                
                // Update transaction status
                $update_transaction = "UPDATE transactions SET status = 'voided' WHERE id = $transaction_id";
                
                if (!mysqli_query($conn, $update_transaction)) {
                    throw new Exception("Error updating transaction: " . mysqli_error($conn));
                }
                
                // Commit transaction
                mysqli_commit($conn);
                
                echo '<div class="alert alert-success">
                        Transaction #' . $transaction_id . ' has been voided successfully.
                      </div>';
                
            } catch (Exception $e) {
                // Rollback transaction on error
                mysqli_rollback($conn);
                
                echo '<div class="alert alert-danger">
                        Error: ' . $e->getMessage() . '
                      </div>';
            }
        } else if (isset($_POST['reject_void'])) {
            $transaction_id = $_POST['transaction_id'];
            
            // Update transaction status back to completed
            $update_sql = "UPDATE transactions SET status = 'completed' WHERE id = $transaction_id";
            
            if (mysqli_query($conn, $update_sql)) {
                echo '<div class="alert alert-info">
                        Void request for Transaction #' . $transaction_id . ' has been rejected.
                      </div>';
            } else {
                echo '<div class="alert alert-danger">
                        Error: ' . mysqli_error($conn) . '
                      </div>';
            }
        }
    }
    
    // Get pending void requests
    $sql = "SELECT t.*, COUNT(ti.id) as item_count, SUM(ti.subtotal) as total_value
            FROM transactions t
            LEFT JOIN transaction_items ti ON t.id = ti.transaction_id
            WHERE t.status = 'void_requested'
            GROUP BY t.id
            ORDER BY t.transaction_date DESC";
            
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        echo '<div class="alert alert-danger">Error: ' . mysqli_error($conn) . '</div>';
    }
    ?>
    
    <div class="card">
      <h3 class="card-title">Pending Void Requests</h3>
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
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($transaction = mysqli_fetch_assoc($result)): ?>
                  <tr>
                    <td><?php echo $transaction['id']; ?></td>
                    <td><?php echo date('M d, Y H:i', strtotime($transaction['transaction_date'])); ?></td>
                    <td>$<?php echo number_format($transaction['total_amount'], 2); ?></td>
                    <td><?php echo $transaction['item_count']; ?> items</td>
                    <td>
                      <a href="view-transaction-details.php?id=<?php echo $transaction['id']; ?>" class="btn btn-primary">
                        View Details
                      </a>
                      <button type="button" class="btn btn-success" onclick="showApproveModal(<?php echo $transaction['id']; ?>)">
                        Approve
                      </button>
                      <button type="button" class="btn btn-danger" onclick="showRejectModal(<?php echo $transaction['id']; ?>)">
                        Reject
                      </button>
                      
                      <!-- Approve Modal -->
                      <div class="modal" id="approveModal<?php echo $transaction['id']; ?>">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h3>Approve Void Request</h3>
                            <button type="button" class="btn-close" onclick="closeModal('approveModal<?php echo $transaction['id']; ?>')">×</button>
                          </div>
                          <div>
                            <p>Are you sure you want to approve the void request for Transaction #<?php echo $transaction['id']; ?>?</p>
                            <p><strong>Warning:</strong> This will restore the menu item servings and mark the transaction as voided.</p>
                          </div>
                          <div class="modal-footer">
                            <form method="post">
                              <input type="hidden" name="transaction_id" value="<?php echo $transaction['id']; ?>">
                              <button type="button" class="btn btn-secondary" onclick="closeModal('approveModal<?php echo $transaction['id']; ?>')">Cancel</button>
                              <button type="submit" name="approve_void" class="btn btn-success">Approve Void</button>
                            </form>
                          </div>
                        </div>
                      </div>
                      
                      <!-- Reject Modal -->
                      <div class="modal" id="rejectModal<?php echo $transaction['id']; ?>">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h3>Reject Void Request</h3>
                            <button type="button" class="btn-close" onclick="closeModal('rejectModal<?php echo $transaction['id']; ?>')">×</button>
                          </div>
                          <div>
                            <p>Are you sure you want to reject the void request for Transaction #<?php echo $transaction['id']; ?>?</p>
                            <p>The transaction will remain as completed and no changes will be made to menu item servings.</p>
                          </div>
                          <div class="modal-footer">
                            <form method="post">
                              <input type="hidden" name="transaction_id" value="<?php echo $transaction['id']; ?>">
                              <button type="button" class="btn btn-secondary" onclick="closeModal('rejectModal<?php echo $transaction['id']; ?>')">Cancel</button>
                              <button type="submit" name="reject_void" class="btn btn-danger">Reject Void</button>
                            </form>
                          </div>
                        </div>
                      </div>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="alert alert-info">No pending void requests to process.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <script>
    function showApproveModal(id) {
      document.getElementById('approveModal' + id).style.display = 'block';
    }
    
    function showRejectModal(id) {
      document.getElementById('rejectModal' + id).style.display = 'block';
    }
    
    function closeModal(modalId) {
      document.getElementById(modalId).style.display = 'none';
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
      if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
      }
    }
  </script>
</body>
</html> 