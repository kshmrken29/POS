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
    return is_logged_in() && ($_SESSION['user_type'] == 'cashier' || $_SESSION['user_type'] == 'admin');
}

// Function to require login for a page
function require_login() {
    if (!is_logged_in()) {
        // Record the original page for potential redirect after login
        if (!empty($_SERVER['REQUEST_URI'])) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        }
        
        header('Location: ' . get_site_url() . 'login.php');
        exit;
    }
}

// Function to require admin access for a page
function require_admin() {
    require_login();
    if (!is_admin()) {
        // Log unauthorized access attempt
        log_activity("attempted unauthorized access to admin area", "IP: {$_SERVER['REMOTE_ADDR']}");
        
        // Redirect to appropriate page
        header('Location: ' . get_site_url() . 'unauthorized.php');
        exit;
    }
}

// Function to require cashier access for a page
function require_cashier() {
    require_login();
    if (!is_cashier()) {
        // Log unauthorized access attempt
        log_activity("attempted unauthorized access to cashier area", "IP: {$_SERVER['REMOTE_ADDR']}");
        
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
        
        // Try to log to database if connection file exists
        $connection_file = dirname(__FILE__) . '/admin/connection.php';
        if (file_exists($connection_file)) {
            try {
                require_once $connection_file;
                
                $user_id = $_SESSION['user_id'];
                $username = $_SESSION['username'];
                $user_type = $_SESSION['user_type'];
                $ip_address = $_SERVER['REMOTE_ADDR'];
                $user_agent = $_SERVER['HTTP_USER_AGENT'];
                
                $action_safe = mysqli_real_escape_string($conn, $action);
                $details_safe = mysqli_real_escape_string($conn, $details);
                
                $query = "INSERT INTO activity_log (user_id, username, user_type, action, details, ip_address, user_agent) 
                         VALUES ('$user_id', '$username', '$user_type', '$action_safe', '$details_safe', '$ip_address', '$user_agent')";
                
                // Execute query but don't throw error if it fails
                @mysqli_query($conn, $query);
            } catch (Exception $e) {
                // Silently fail if database logging doesn't work
                error_log("Failed to log activity to database: " . $e->getMessage());
            }
        }
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

// Function to get a user-friendly date/time format
function format_datetime($datetime, $format = 'full') {
    $timestamp = strtotime($datetime);
    
    switch ($format) {
        case 'date':
            return date('M j, Y', $timestamp);
        case 'time':
            return date('g:i A', $timestamp);
        case 'short':
            return date('M j, g:i A', $timestamp);
        case 'full':
        default:
            return date('F j, Y, g:i A', $timestamp);
    }
}

// Function to get the current user's ID
function get_current_user_id() {
    return is_logged_in() ? $_SESSION['user_id'] : 0;
}

// Function to get the current user's name
function get_current_username() {
    return is_logged_in() ? $_SESSION['username'] : 'Guest';
}

// Function to get the current user's type
function get_current_user_type() {
    return is_logged_in() ? $_SESSION['user_type'] : 'guest';
}
?> 