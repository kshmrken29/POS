<?php
// Include authentication system
require_once 'auth_session.php';
require_admin();

// Log that admin dashboard was accessed
log_activity('accessed admin dashboard');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Process Void Requests</title>
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
  <div class="sidebar col-md-3 col-lg-2 d-md-block bg-dark">
    <div class="position-sticky sidebar-sticky">
      <a href="dashboard.php" class="navbar-brand">Restaurant POS - Admin</a>
      <hr class="bg-light">
      <ul class="nav flex-column">
        <li class="nav-item">
          <a class="nav-link" href="dashboard.php">
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
                $update_transaction = "UPDATE transactions SET status = 'voided', void_processed = TRUE WHERE id = $transaction_id";
                
                if (!mysqli_query($conn, $update_transaction)) {
                    throw new Exception("Error updating transaction: " . mysqli_error($conn));
                }
                
                // Commit transaction
                mysqli_commit($conn);
                
                echo '<div class="alert alert-success">
                        <i class="bi bi-check-circle-fill"></i>
                        Transaction #' . $transaction_id . ' has been voided successfully.
                      </div>';
                
            } catch (Exception $e) {
                // Rollback transaction on error
                mysqli_rollback($conn);
                
                echo '<div class="alert alert-danger">
                        <i class="bi bi-x-circle-fill"></i>
                        Error: ' . $e->getMessage() . '
                      </div>';
            }
        } else if (isset($_POST['reject_void'])) {
            $transaction_id = $_POST['transaction_id'];
            
            // Update transaction status back to completed and mark as processed
            $update_sql = "UPDATE transactions SET status = 'completed', void_processed = TRUE WHERE id = $transaction_id";
            
            if (mysqli_query($conn, $update_sql)) {
                echo '<div class="alert alert-info">
                        <i class="bi bi-info-circle-fill"></i>
                        Void request for Transaction #' . $transaction_id . ' has been rejected.
                      </div>';
            } else {
                echo '<div class="alert alert-danger">
                        <i class="bi bi-x-circle-fill"></i>
                        Error: ' . mysqli_error($conn) . '
                      </div>';
            }
        }
    }
    
    // Get pending void requests - only show unprocessed void requests
    $sql = "SELECT t.*, COUNT(ti.id) as item_count, SUM(ti.subtotal) as total_value
            FROM transactions t
            LEFT JOIN transaction_items ti ON t.id = ti.transaction_id
            WHERE t.status = 'void_requested' AND (t.void_processed IS NULL OR t.void_processed = FALSE)
            GROUP BY t.id
            ORDER BY t.transaction_date DESC";
            
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        echo '<div class="alert alert-danger">Error: ' . mysqli_error($conn) . '</div>';
    }
    ?>
    
    <div class="card">
      <div class="card-header bg-warning text-dark">
        <h4>Pending Void Requests</h4>
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
                      <a href="view-transaction-details.php?id=<?php echo $transaction['id']; ?>" class="btn btn-sm btn-info">
                        <i class="bi bi-eye"></i> View Details
                      </a>
                      <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal<?php echo $transaction['id']; ?>">
                        <i class="bi bi-check-circle"></i> Approve
                      </button>
                      <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal<?php echo $transaction['id']; ?>">
                        <i class="bi bi-x-circle"></i> Reject
                      </button>
                      
                      <!-- Approve Modal -->
                      <div class="modal fade" id="approveModal<?php echo $transaction['id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                          <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                              <h5 class="modal-title">Approve Void Request</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                              <p>Are you sure you want to approve the void request for Transaction #<?php echo $transaction['id']; ?>?</p>
                              <p><strong>Warning:</strong> This will restore the menu item servings and mark the transaction as voided.</p>
                            </div>
                            <div class="modal-footer">
                              <form method="post">
                                <input type="hidden" name="transaction_id" value="<?php echo $transaction['id']; ?>">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" name="approve_void" class="btn btn-success">Approve Void</button>
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <!-- Reject Modal -->
                      <div class="modal fade" id="rejectModal<?php echo $transaction['id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                          <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                              <h5 class="modal-title">Reject Void Request</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                              <p>Are you sure you want to reject the void request for Transaction #<?php echo $transaction['id']; ?>?</p>
                            </div>
                            <div class="modal-footer">
                              <form method="post">
                                <input type="hidden" name="transaction_id" value="<?php echo $transaction['id']; ?>">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" name="reject_void" class="btn btn-danger">Reject Void</button>
                              </form>
                            </div>
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
          <div class="alert alert-info">No pending void requests.</div>
        <?php endif; ?>
      </div>
    </div>
  </div> <!-- End of main-content -->

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 