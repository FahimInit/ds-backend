<?php
// PHP Script: getOrderStatus.php
require_once 'config.php';
setHeaders();

$conn = connectDB();

if (!isset($_GET['order_id'])) {
    http_response_code(400);
    die(json_encode(["success" => false, "message" => "Order ID is missing."]));
}

$order_id = $_GET['order_id'];

// Get order status
$sql = "SELECT status FROM orders WHERE order_id_ref = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if ($order) {
    echo json_encode(["success" => true, "status" => $order['status']]);
} else {
    http_response_code(404);
    echo json_encode(["success" => false, "message" => "Order not found."]);
}

$stmt->close();
$conn->close();
?>