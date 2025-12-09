<?php
// PHP Script: approveOrder.php - Now updates analytics!
require_once 'config.php';
setHeaders();

$conn = connectDB();
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['order_id'])) {
    http_response_code(400); 
    die(json_encode(["success" => false, "message" => "Order ID is missing."]));
}

$order_id = $data['order_id'];

// Start Transaction: Ensures both updates happen or neither does
$conn->begin_transaction();

try {
    // 1. Get order details (specifically the price)
    $stmt_select = $conn->prepare("SELECT price FROM orders WHERE order_id_ref = ? AND status = 'pending'");
    $stmt_select->bind_param("s", $order_id);
    $stmt_select->execute();
    $result = $stmt_select->get_result();
    $order = $result->fetch_assoc();
    $stmt_select->close();

    if (!$order) {
        throw new Exception("Order not found or already approved.", 404);
    }
    
    $order_price = $order['price'];

    // 2. Update the order status to 'approved'
    $stmt_update_order = $conn->prepare("UPDATE orders SET status = 'approved' WHERE order_id_ref = ?");
    $stmt_update_order->bind_param("s", $order_id);
    $stmt_update_order->execute();
    
    if ($stmt_update_order->affected_rows === 0) {
        throw new Exception("Order status update failed.", 500);
    }
    $stmt_update_order->close();

    // 3. Update Total Sales (Increment by 1)
    $stmt_sales = $conn->prepare("UPDATE settings SET key_value = key_value + 1 WHERE key_name = 'total_sales'");
    $stmt_sales->execute();
    $stmt_sales->close();
    
    // 4. Update Total Revenue (Add the order price)
    $stmt_revenue = $conn->prepare("UPDATE settings SET key_value = key_value + ? WHERE key_name = 'total_revenue'");
    $stmt_revenue->bind_param("d", $order_price); // 'd' for double/decimal
    $stmt_revenue->execute();
    $stmt_revenue->close();
    
    // Commit transaction if all steps succeeded
    $conn->commit();
    
    http_response_code(200);
    echo json_encode(["success" => true, "message" => "Order approved, analytics updated."]);

} catch (Exception $e) {
    // Rollback transaction on failure
    $conn->rollback();
    
    http_response_code($e->getCode() == 404 ? 404 : 500);
    echo json_encode(["success" => false, "message" => "Transaction failed: " . $e->getMessage()]);
}

$conn->close();
?>