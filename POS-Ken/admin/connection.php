<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "restaurantpos";

    // First, create connection without specifying database
    $temp_conn = mysqli_connect($servername, $username, $password);
    
    // Check connection
    if (!$temp_conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    
    // Check if database exists, create if it doesn't
    $db_check = mysqli_query($temp_conn, "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbname'");
    
    if (mysqli_num_rows($db_check) == 0) {
        // Database doesn't exist, create it
        $create_db = mysqli_query($temp_conn, "CREATE DATABASE $dbname");
        
        if (!$create_db) {
            die("Error creating database: " . mysqli_error($temp_conn));
        }
    }
    
    // Close temporary connection
    mysqli_close($temp_conn);
    
    // Create connection to the specific database
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    
    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Check if users table exists, create if it doesn't
    $table_check = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
    
    if (mysqli_num_rows($table_check) == 0) {
        // Create users table
        $create_table = "CREATE TABLE users (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            user_type ENUM('admin', 'cashier') NOT NULL,
            last_login DATETIME,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        if (!mysqli_query($conn, $create_table)) {
            die("Error creating users table: " . mysqli_error($conn));
        }
        
        // Create default admin user (username: admin, password: admin123)
        $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
        $insert_admin = "INSERT INTO users (username, password, user_type) 
                         VALUES ('admin', '$admin_password', 'admin')";
                         
        if (!mysqli_query($conn, $insert_admin)) {
            die("Error creating default admin user: " . mysqli_error($conn));
        }
        
        // Create default cashier user (username: cashier, password: cashier123)
        $cashier_password = password_hash('cashier123', PASSWORD_DEFAULT);
        $insert_cashier = "INSERT INTO users (username, password, user_type) 
                           VALUES ('cashier', '$cashier_password', 'cashier')";
                           
        if (!mysqli_query($conn, $insert_cashier)) {
            die("Error creating default cashier user: " . mysqli_error($conn));
        }
    }
?> 