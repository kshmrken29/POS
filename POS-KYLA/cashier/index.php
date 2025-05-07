<?php
// Include authentication system
require_once '../auth_session.php';
require_cashier();

// Log that cashier dashboard was accessed
log_activity('accessed cashier dashboard');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cashier Dashboard</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../styles/main.css">
</head>
<body>

  <nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">
        <img src="../styles/logo.svg" alt="Logo" width="30" height="30" class="d-inline-block align-text-top me-2">
        Restaurant POS
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#cashierNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="cashierNavbar">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link active" href="index.php">
              <i class="bi bi-speedometer2 me-1"></i> Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="take-customer-order.php">
              <i class="bi bi-cart-plus me-1"></i> Take Order
            </a>
          </li>
          <?php if (is_admin()): ?>
          <li class="nav-item">
            <a class="nav-link" href="../admin/index.php">
              <i class="bi bi-gear me-1"></i> Admin Panel
            </a>
          </li>
          <?php endif; ?>
          <!-- User account dropdown -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
              <div class="user-info">
                <div class="user-avatar">
                  <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                </div>
                <span class="d-none d-sm-inline-block"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
              </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <?php if (is_admin()): ?>
              <li><a class="dropdown-item" href="../admin/index.php"><i class="bi bi-gear me-2"></i>Switch to Admin</a></li>
              <li><hr class="dropdown-divider"></li>
              <?php endif; ?>
              <li><a class="dropdown-item" href="../logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container-fluid py-4 px-4">
    <div class="main-container">
      <div class="row mb-4">
        <div class="col-12">
          <h2 class="page-header">Cashier Dashboard</h2>
          <p class="text-muted">Welcome to the Restaurant Point of Sale system. Use the features below to process customer orders.</p>
        </div>
      </div>
      
      <!-- User welcome message -->
      <div class="alert alert-info mb-4">
        <div class="d-flex align-items-center">
          <i class="bi bi-info-circle fs-4 me-3"></i>
          <div>
            <p class="mb-0"><strong>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</strong></p>
            <?php
              // Get last login time from database
              include '../admin/connection.php';
              $user_id = $_SESSION['user_id'];
              $last_login_query = "SELECT last_login FROM users WHERE id = $user_id";
              $last_login_result = mysqli_query($conn, $last_login_query);
              if ($last_login_result && mysqli_num_rows($last_login_result) > 0) {
                $last_login = mysqli_fetch_assoc($last_login_result)['last_login'];
                if ($last_login) {
                  echo '<small>Your last login was: ' . date('F j, Y, g:i a', strtotime($last_login)) . '</small>';
                } else {
                  echo '<small>This is your first login.</small>';
                }
              }
            ?>
          </div>
        </div>
      </div>

      <!-- Main features -->
      <div class="row">
        <div class="col-md-4 mb-4">
          <div class="card feature-card h-100">
            <div class="card-body d-flex flex-column">
              <div class="mb-3">
                <i class="bi bi-cart-plus card-icon"></i>
                <h5 class="card-title">Take Customer Order</h5>
                <p class="card-text">Create new customer orders and select menu items for the current day.</p>
              </div>
              <div class="mt-auto">
                <a href="take-customer-order.php" class="btn btn-secondary">Take Order</a>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-md-4 mb-4">
          <div class="card feature-card h-100">
            <div class="card-body d-flex flex-column">
              <div class="mb-3">
                <i class="bi bi-search card-icon"></i>
                <h5 class="card-title">View Transactions</h5>
                <p class="card-text">View all completed transactions and their details for reference.</p>
              </div>
              <div class="mt-auto">
                <a href="view-transactions.php" class="btn btn-outline-secondary">View Transactions</a>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-md-4 mb-4">
          <div class="card feature-card h-100">
            <div class="card-body d-flex flex-column">
              <div class="mb-3">
                <i class="bi bi-x-circle card-icon"></i>
                <h5 class="card-title">Request Void Transaction</h5>
                <p class="card-text">Request to void a completed transaction that needs cancellation.</p>
              </div>
              <div class="mt-auto">
                <a href="void-transaction.php" class="btn btn-outline-danger">Void Transaction</a>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Quick Access Section -->
      <div class="row">
        <div class="col-12 mb-3">
          <h4 class="border-bottom pb-2"><i class="bi bi-lightning-charge text-warning me-2"></i>Quick Actions</h4>
        </div>
        
        <div class="col-md-6 mb-4">
          <div class="d-grid gap-2">
            <a href="take-customer-order.php" class="btn btn-outline-secondary d-flex align-items-center justify-content-start p-3">
              <i class="bi bi-cart-plus fs-4 me-3"></i>
              <span>
                <strong>New Order</strong><br>
                <small class="text-muted">Create a new customer order</small>
              </span>
            </a>
            <a href="view-transactions.php" class="btn btn-outline-secondary d-flex align-items-center justify-content-start p-3">
              <i class="bi bi-search fs-4 me-3"></i>
              <span>
                <strong>Today's Transactions</strong><br>
                <small class="text-muted">View transactions from today</small>
              </span>
            </a>
          </div>
        </div>
        
        <div class="col-md-6 mb-4">
          <div class="d-grid gap-2">
            <a href="void-transaction.php" class="btn btn-outline-secondary d-flex align-items-center justify-content-start p-3">
              <i class="bi bi-x-circle fs-4 me-3"></i>
              <span>
                <strong>Request Void</strong><br>
                <small class="text-muted">Request a transaction void</small>
              </span>
            </a>
            <?php if (is_admin()): ?>
            <a href="../admin/index.php" class="btn btn-outline-primary d-flex align-items-center justify-content-start p-3">
              <i class="bi bi-gear fs-4 me-3"></i>
              <span>
                <strong>Admin Dashboard</strong><br>
                <small class="text-muted">Switch to admin panel</small>
              </span>
            </a>
            <?php else: ?>
            <a href="setup_tables.php" class="btn btn-outline-secondary d-flex align-items-center justify-content-start p-3">
              <i class="bi bi-database-gear fs-4 me-3"></i>
              <span>
                <strong>Setup Tables</strong><br>
                <small class="text-muted">Initialize database tables</small>
              </span>
            </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
      
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 