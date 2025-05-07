<?php
// Start session
session_start();

// Get the user type if logged in
$user_type = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : 'guest';
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';

// Get the redirect URL based on user type
$redirect_url = '#';
if ($user_type == 'admin') {
    $redirect_url = 'admin/index.php';
} elseif ($user_type == 'cashier') {
    $redirect_url = 'cashier/index.php';
} else {
    $redirect_url = 'login.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unauthorized Access</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles/main.css">
    <style>
        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .error-code {
            font-size: 8rem;
            font-weight: 700;
            color: var(--primary);
            line-height: 1;
            margin-bottom: 0;
            opacity: 0.2;
        }
        .error-message {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--primary);
        }
        .error-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: var(--danger);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="text-center">
            <div class="error-code position-relative">403</div>
            <i class="bi bi-shield-lock error-icon"></i>
            <h1 class="error-message">Access Denied</h1>
            <p class="mb-4">Sorry, you don't have permission to access this page. Please contact an administrator if you believe this is a mistake.</p>
            
            <div class="mb-4">
                <p><strong>User:</strong> <?php echo htmlspecialchars($username); ?></p>
                <p><strong>Role:</strong> <?php echo ucfirst(htmlspecialchars($user_type)); ?></p>
            </div>
            
            <div class="d-flex justify-content-center gap-3">
                <a href="<?php echo $redirect_url; ?>" class="btn btn-secondary">
                    <i class="bi bi-house-door me-2"></i>Go to Dashboard
                </a>
                <a href="logout.php" class="btn btn-outline-primary">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </a>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 