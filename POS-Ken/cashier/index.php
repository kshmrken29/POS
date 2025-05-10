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
  <title>Cashier Dashboard</title>
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
          <a class="nav-link active" href="index.php">
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
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>Cashier Dashboard</h1>
      <div class="user-badge">
        <span class="badge bg-primary">
          <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
        </span>
      </div>
    </div>
    
    <p class="text-muted mb-5">Welcome to the Restaurant Point of Sale Cashier Dashboard. Use the features below to process customer orders.</p>
    
    <!-- User welcome message -->
    <div class="alert alert-info mb-4">
      <i class="bi bi-info-circle"></i> 
      Welcome back, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!
      <?php
        // Get last login time from database
        include '../admin/connection.php';
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
    
    <div class="row">
      <div class="col-md-4">
        <div class="card feature-card text-center">
          <div class="card-body">
            <i class="bi bi-cart-plus card-icon"></i>
            <h5 class="card-title">Take Customer Order</h5>
            <p class="card-text">Create new customer orders and select menu items.</p>
            <a href="take-customer-order.php" class="btn btn-primary">Take Order</a>
          </div>
        </div>
      </div>
      
      <div class="col-md-4">
        <div class="card feature-card text-center">
          <div class="card-body">
            <i class="bi bi-search card-icon"></i>
            <h5 class="card-title">View Transactions</h5>
            <p class="card-text">View all completed transactions and details.</p>
            <a href="view-transactions.php" class="btn btn-primary">View Transactions</a>
          </div>
        </div>
      </div>
      
      <div class="col-md-4">
        <div class="card feature-card text-center">
          <div class="card-body">
            <i class="bi bi-x-circle card-icon"></i>
            <h5 class="card-title">Request Void Transaction</h5>
            <p class="card-text">Request to void a completed transaction.</p>
            <a href="void-transaction.php" class="btn btn-danger">Void Transaction</a>
          </div>
        </div>
      </div>
    </div>
  </div> <!-- End of main-content -->

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 