<?php
// Include database connection
include '../admin/connection.php';

// Set content type to JSON
header('Content-Type: application/json');

// Get the raw POST data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Check if data is valid
if (!$data || !isset($data['total']) || !isset($data['amountPaid']) || !isset($data['change']) || !isset($data['items']) || empty($data['items'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data received']);
    exit;
}

// Start transaction to ensure data integrity
mysqli_begin_transaction($conn);

try {
    // Insert transaction record
    $total = $data['total'];
    $amountPaid = $data['amountPaid'];
    $change = $data['change'];
    
    $sql = "INSERT INTO transactions (total_amount, amount_paid, change_amount) 
            VALUES ('$total', '$amountPaid', '$change')";
    
    if (!mysqli_query($conn, $sql)) {
        throw new Exception("Error saving transaction: " . mysqli_error($conn));
    }
    
    // Get the inserted transaction ID
    $transaction_id = mysqli_insert_id($conn);
    
    // Process each item in the order
    foreach ($data['items'] as $item) {
        $menu_item_id = $item['id'];
        $quantity = $item['qty'];
        $price = $item['price'];
        $subtotal = $item['subtotal'];
        
        // Insert transaction item
        $sql = "INSERT INTO transaction_items (transaction_id, menu_item_id, quantity, price_per_item, subtotal) 
                VALUES ('$transaction_id', '$menu_item_id', '$quantity', '$price', '$subtotal')";
        
        if (!mysqli_query($conn, $sql)) {
            throw new Exception("Error saving transaction item: " . mysqli_error($conn));
        }
        
        // Update menu item servings_sold
        $sql = "UPDATE menu_items 
                SET servings_sold = servings_sold + $quantity 
                WHERE id = $menu_item_id";
        
        if (!mysqli_query($conn, $sql)) {
            throw new Exception("Error updating menu item: " . mysqli_error($conn));
        }
    }
    
    // Commit the transaction
    mysqli_commit($conn);
    
    // Return success response
    echo json_encode([
        'success' => true, 
        'message' => 'Transaction saved successfully', 
        'transaction_id' => $transaction_id
    ]);
    
} catch (Exception $e) {
    // Rollback the transaction if an error occurred
    mysqli_rollback($conn);
    
    // Return error response
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}
?>
