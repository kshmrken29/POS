<?php
// Include database connection
include '../admin/connection.php';

// Create transactions table
$sql_transactions = "CREATE TABLE IF NOT EXISTS transactions (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    transaction_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10,2) NOT NULL,
    amount_paid DECIMAL(10,2) NOT NULL,
    change_amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) DEFAULT 'completed'
)";

// Create transaction_items table for storing items in each transaction
$sql_transaction_items = "CREATE TABLE IF NOT EXISTS transaction_items (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT(11) NOT NULL,
    menu_item_id INT(11) NOT NULL,
    quantity INT(11) NOT NULL,
    price_per_item DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id)
)";

// Execute the queries
if (mysqli_query($conn, $sql_transactions)) {
    echo "Transactions table created successfully<br>";
} else {
    echo "Error creating transactions table: " . mysqli_error($conn) . "<br>";
}

if (mysqli_query($conn, $sql_transaction_items)) {
    echo "Transaction items table created successfully<br>";
} else {
    echo "Error creating transaction items table: " . mysqli_error($conn) . "<br>";
}

// Create an index on transaction_id for faster lookups
$sql_index = "CREATE INDEX IF NOT EXISTS idx_transaction_id ON transaction_items(transaction_id)";
if (mysqli_query($conn, $sql_index)) {
    echo "Index on transaction_items created successfully<br>";
} else {
    echo "Error creating index: " . mysqli_error($conn) . "<br>";
}

echo "<br><a href='index.php' class='btn btn-primary'>Go to Cashier Dashboard</a>";
?> 