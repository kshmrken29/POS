<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Unauthorized Access</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-container {
            max-width: 500px;
            width: 100%;
            padding: 40px;
            text-align: center;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .error-icon {
            font-size: 4rem;
            color: #dc3545;
            margin-bottom: 20px;
        }
        .btn-back {
            margin-top: 20px;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <div class="error-container">
        <i class="bi bi-exclamation-triangle-fill error-icon"></i>
        <h2>Unauthorized Access</h2>
        <p class="lead">You do not have permission to access this page.</p>
        
        <?php if (isset($_SESSION['user_type'])): ?>
            <p>You are logged in as: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong> (<?php echo $_SESSION['user_type']; ?>)</p>
            
            <?php if ($_SESSION['user_type'] == 'admin'): ?>
                <a href="admin/index.php" class="btn btn-primary btn-back">Go to Admin Dashboard</a>
            <?php elseif ($_SESSION['user_type'] == 'cashier'): ?>
                <a href="cashier/index.php" class="btn btn-primary btn-back">Go to Cashier Dashboard</a>
            <?php endif; ?>
            
            <div class="mt-3">
                <a href="logout.php" class="btn btn-outline-secondary">Logout</a>
            </div>
        <?php else: ?>
            <p>Please login to access the system.</p>
            <a href="login.php" class="btn btn-primary btn-back">Go to Login</a>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 