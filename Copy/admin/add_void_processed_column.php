<?php
// Script to add void_processed column to transactions table
include 'connection.php';

// Check if column exists first
$result = mysqli_query($conn, "SHOW COLUMNS FROM transactions LIKE 'void_processed'");
$exists = mysqli_num_rows($result) > 0;

if (!$exists) {
    $sql = "ALTER TABLE transactions ADD COLUMN void_processed BOOLEAN DEFAULT FALSE";
    
    if (mysqli_query($conn, $sql)) {
        echo "Column 'void_processed' added successfully to transactions table.";
    } else {
        echo "Error adding column: " . mysqli_error($conn);
    }
} else {
    echo "Column 'void_processed' already exists in transactions table.";
}

mysqli_close($conn);
?> 