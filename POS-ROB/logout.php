<?php
session_start();

// Log the logout action if user is logged in
if (isset($_SESSION['username']) && isset($_SESSION['user_type'])) {
    $log_message = "User {$_SESSION['username']} ({$_SESSION['user_type']}) logged out";
    error_log($log_message);
}

// Unset all session variables
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session
session_destroy();

// Redirect to login page
header('Location: login.php');
exit;
?> 