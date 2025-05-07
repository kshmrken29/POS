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
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-form">
        <h2 class="login-title">Restaurant POS System</h2>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="post" action="">
            <div class="form-group">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
            </div>
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
        
    </div>
</body>
</html> 