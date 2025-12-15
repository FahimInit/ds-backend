<?php
require_once 'config.php';
setHeaders();
$conn = connectDB();

$sql = "SELECT id, order_id_ref, product_name, price, payment_method, created_at, buyer_name, buyer_email, buyer_phone, trx_id 
        FROM orders 
        WHERE status = 'pending' 
        ORDER BY created_at ASC";
$result = $conn->query($sql);

$orders = [];
if ($result) {
    while($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}
echo json_encode(["success" => true, "orders" => $orders]);
$conn->close();
?>