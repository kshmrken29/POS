<?php
// Start the session
session_start();

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    // Log the logout activity if possible
    if (function_exists('log_activity')) {
        log_activity('logged out');
    }
    
    // Unset all session variables
    $_SESSION = array();
    
    // Delete the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy the session
    session_destroy();
}

// Redirect to login page with a logout message
header("Location: login.php?status=logout");
exit;
?> 