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
    .feature-card {
      transition: transform 0.3s ease;
      margin-bottom: 20px;
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
  </style>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>

  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Restaurant POS - Cashier</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#cashierNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="cashierNavbar">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link active" href="index.php">Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="take-customer-order.php">Take Order</a>
          </li>
          <?php if (is_admin()): ?>
          <li class="nav-item">
            <a class="nav-link" href="../admin/index.php">Admin Panel</a>
          </li>
          <?php endif; ?>
          <!-- User account dropdown -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
              <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <?php if (is_admin()): ?>
              <li><a class="dropdown-item" href="../admin/index.php">Switch to Admin</a></li>
              <li><hr class="dropdown-divider"></li>
              <?php endif; ?>
              <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container">
    <h1 class="mb-4 text-center">Restaurant POS Cashier Dashboard</h1>
    <p class="text-center mb-5">Welcome to the Restaurant Point of Sale Cashier Dashboard. Use the features below to process customer orders.</p>
    
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
    
    <div class="row mt-4">
      <div class="col-12 text-center">
        <a href="setup_tables.php" class="btn btn-outline-secondary">Setup Database Tables</a>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 