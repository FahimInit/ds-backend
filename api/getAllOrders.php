<?php
// PHP Script: getAllOrders.php - Fetches ALL orders (Pending & Approved)
require_once 'config.php';
setHeaders();

$conn = connectDB();

// Fetch ALL orders, ordered by newest first
$sql = "SELECT id, order_id_ref, product_name, price, status, created_at, buyer_name, buyer_email, buyer_phone 
        FROM orders 
        ORDER BY created_at DESC";
        
$result = $conn->query($sql);

$orders = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    echo json_encode(["success" => true, "orders" => $orders]);
} else {
    // Return empty array if no orders found
    if (!$result) {
        error_log("Error fetching all orders: " . $conn->error);
    }
    echo json_encode(["success" => true, "orders" => []]); 
}

$conn->close();
?>