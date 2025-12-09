<?php
require_once 'config.php';
setHeaders();

$conn = connectDB();
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['order_id_ref']) || !isset($data['product_name']) || !isset($data['price']) || !isset($data['buyer_name']) || !isset($data['buyer_email']) || !isset($data['buyer_phone']) || !isset($data['trx_id']) || !isset($data['payment_method'])) {
    http_response_code(400); 
    die(json_encode(["success" => false, "message" => "Required data missing."]));
}

$order_id_ref = $data['order_id_ref'];
$product_name = $data['product_name'];
$price = (float)$data['price'];
$payment_method = $data['payment_method']; // NEW
$buyer_name = $data['buyer_name'];
$buyer_email = $data['buyer_email'];
$buyer_phone = $data['buyer_phone'];
$trx_id = $data['trx_id'];

// Added payment_method to SQL
$sql = "INSERT INTO orders (order_id_ref, product_name, price, payment_method, status, buyer_name, buyer_email, buyer_phone, trx_id) VALUES (?, ?, ?, ?, 'pending', ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

// ssdsssss (8 params)
$stmt->bind_param("ssdsssss", $order_id_ref, $product_name, $price, $payment_method, $buyer_name, $buyer_email, $buyer_phone, $trx_id); 

if ($stmt->execute()) {
    http_response_code(201); 
    echo json_encode(["success" => true, "order_id" => $order_id_ref]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Execution failed: " . $stmt->error]);
}
$stmt->close();
$conn->close();
?>