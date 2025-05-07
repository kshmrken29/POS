<?php
// Include authentication system
require_once '../auth_session.php';
require_cashier();

// Log that cashier dashboard was accessed
log_activity('accessed cashier dashboard');

// Include connection for database queries
include '../admin/connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cashier Dashboard</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>

  <div class="navbar">
    <div class="navbar-container">
      <a class="navbar-brand" href="index.php">Restaurant POS - Cashier</a>
      <ul class="navbar-menu">
        <li class="nav-item"><a class="nav-link active" href="index.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="take-customer-order.php">Take Order</a></li>
        <li class="nav-item"><a class="nav-link" href="view-transactions.php">Transactions</a></li>
        <?php if (is_admin()): ?>
        <li class="nav-item"><a class="nav-link" href="../admin/index.php">Admin Panel</a></li>
        <?php endif; ?>
        <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
      </ul>
    </div>
  </div>

  <div class="container">
    <h1 class="text-center mb-4">Cashier Dashboard</h1>
    
    <!-- User welcome message -->
    <div class="alert alert-info">
      Welcome back, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!
      <?php
        // Get last login time from database
        $user_id = $_SESSION['user_id'];
        $last_login_query = "SELECT last_login FROM users WHERE id = $user_id";
        $last_login_result = mysqli_query($conn, $last_login_query);
        if ($last_login_result && mysqli_num_rows($last_login_result) > 0) {
          $last_login = mysqli_fetch_assoc($last_login_result)['last_login'];
          if ($last_login) {
            echo ' Your last login was: ' . date('F j, Y, g:i a', strtotime($last_login));
          } else {
            echo ' This is your first login.';
          }
        }
      ?>
    </div>
    
    <div class="card-grid">
      <div class="card feature-card">
        <h3 class="card-title">Take Customer Order</h3>
        <p>Create new customer orders and select menu items.</p>
        <a href="take-customer-order.php" class="btn btn-primary">Take Order</a>
      </div>
      
      <div class="card feature-card">
        <h3 class="card-title">View Transactions</h3>
        <p>View all completed transactions and details.</p>
        <a href="view-transactions.php" class="btn btn-primary">View Transactions</a>
      </div>
      
      <div class="card feature-card">
        <h3 class="card-title">Request Void Transaction</h3>
        <p>Request to void a completed transaction.</p>
        <a href="void-transaction.php" class="btn btn-danger">Void Transaction</a>
      </div>
    </div>
    
    <div class="mt-4 text-center">
      <a href="setup_tables.php" class="btn btn-secondary">Setup Database Tables</a>
    </div>
  </div>

</body>
</html> 