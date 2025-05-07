<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_type']);
}

// Function to check if user has admin access
function is_admin() {
    return is_logged_in() && $_SESSION['user_type'] == 'admin';
}

// Function to check if user has cashier access
function is_cashier() {
    return is_logged_in() && $_SESSION['user_type'] == 'cashier';
}

// Function to require login for a page
function require_login() {
    if (!is_logged_in()) {
        header('Location: ' . get_site_url() . 'login.php');
        exit;
    }
}

// Function to require admin access for a page
function require_admin() {
    require_login();
    if (!is_admin()) {
        // Log unauthorized access attempt
        $log_message = "Unauthorized access attempt by {$_SESSION['username']} (not an admin) to " . $_SERVER['REQUEST_URI'];
        error_log($log_message);
        
        // Redirect to appropriate page
        header('Location: ' . get_site_url() . 'unauthorized.php');
        exit;
    }
}

// Function to require cashier access for a page
function require_cashier() {
    require_login();
    if (!is_cashier() && !is_admin()) {
        // Log unauthorized access attempt
        $log_message = "Unauthorized access attempt by {$_SESSION['username']} (not a cashier) to " . $_SERVER['REQUEST_URI'];
        error_log($log_message);
        
        // Redirect to appropriate page
        header('Location: ' . get_site_url() . 'unauthorized.php');
        exit;
    }
}

// Function to log user activity
function log_activity($action, $details = '') {
    if (is_logged_in()) {
        $log_message = "User {$_SESSION['username']} ({$_SESSION['user_type']}): $action";
        if (!empty($details)) {
            $log_message .= " - $details";
        }
        error_log($log_message);
    }
}

// Function to get site URL
function get_site_url() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $domain = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['PHP_SELF']);
    
    // Remove admin or cashier from path if present
    $path = str_replace(['/admin', '/cashier'], '', $path);
    
    return $protocol . $domain . $path . (substr($path, -1) != '/' ? '/' : '');
}
?> 