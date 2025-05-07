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
?>