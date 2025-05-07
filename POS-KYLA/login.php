<?php
session_start();
include 'admin/connection.php';

// Initialize variables
$username = $password = '';
$error = '';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    // Redirect based on user type
    if ($_SESSION['user_type'] == 'admin') {
        header('Location: admin/index.php');
    } else {
        header('Location: cashier/index.php');
    }
    exit;
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    
    // Validate form data
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        // Check if user exists
        $query = "SELECT id, username, password, user_type FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Password is correct, create session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_type'] = $user['user_type'];
                
                // Update last login time
                $update_query = "UPDATE users SET last_login = NOW() WHERE id = " . $user['id'];
                mysqli_query($conn, $update_query);
                
                // Log the successful login
                $log_message = "User {$user['username']} ({$user['user_type']}) logged in";
                error_log($log_message);
                
                // Redirect based on user type
                if ($user['user_type'] == 'admin') {
                    header('Location: admin/index.php');
                } else {
                    header('Location: cashier/index.php');
                }
                exit;
            } else {
                $error = 'Invalid password';
            }
        } else {
            $error = 'Invalid username';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant POS - Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles/main.css">
</head>
<body>
    <div class="login-container">
        <div class="login-form">
            <div class="login-header">
                <div class="login-logo">
                    <img src="styles/logo.svg" alt="POS System Logo" width="80" height="80">
                </div>
                <h1 class="login-title">Restaurant POS</h1>
                <p class="login-subtitle">Sign in to continue to your dashboard</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="post" action="">
                <div class="mb-4">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" placeholder="Enter your username" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-secondary w-100 mb-3">Sign In</button>
            </form>
            
            <div class="mt-4 text-center">
                <p class="text-muted mb-0" style="font-size: 0.85rem;">Default login credentials</p>
                <div class="d-flex justify-content-center mt-2">
                    <div class="me-4">
                        <span class="badge bg-primary mb-1">Admin</span><br>
                        <small>admin / admin123</small>
                    </div>
                    <div>
                        <span class="badge bg-info mb-1">Cashier</span><br>
                        <small>cashier / cashier123</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 