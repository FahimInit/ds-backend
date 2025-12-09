<?php
// PHP Script: deleteOrder.php
require_once 'config.php';
setHeaders();

$conn = connectDB();
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['order_id'])) {
    http_response_code(400); 
    die(json_encode(["success" => false, "message" => "Order ID is missing."]));
}

$order_id = $data['order_id'];

// Prepare the SQL statement for security (DELETE based on order_id_ref)
$sql = "DELETE FROM orders WHERE order_id_ref = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    die(json_encode(["success" => false, "message" => "SQL Prepare failed: " . $conn->error]));
}

// Bind the string parameter
$stmt->bind_param("s", $order_id); 

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        http_response_code(200); 
        echo json_encode(["success" => true, "message" => "Order deleted successfully."]);
    } else {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Order not found."]);
    }
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Execution failed: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>