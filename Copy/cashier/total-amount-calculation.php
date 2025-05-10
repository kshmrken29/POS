<?php
// This file handles any server-side calculations for total amount
// Currently not used directly as calculations are done client-side in take-customer-order.php
// But maintained for future use if needed

// Include database connection
include '../admin/connection.php';

// Function to calculate total amount based on items
function calculateTotal($items) {
    $total = 0;
    
    foreach ($items as $item) {
        $total += $item['qty'] * $item['price'];
    }
    
    return $total;
}

// Handle AJAX requests for total calculation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'calculate') {
    // Get the items from the request
    $items = json_decode($_POST['items'], true);
    
    // Calculate the total
    $total = calculateTotal($items);
    
    // Return the response
    header('Content-Type: application/json');
    echo json_encode(['total' => $total]);
    exit;
}
?>
