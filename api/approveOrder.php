<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");

include 'config.php';

$data = json_decode(file_get_contents("php://input"));

if (isset($data->order_id)) {
    $order_id = $data->order_id;
    $conn = connectDB();

    // 1. Get Order Price for revenue calculation
    $orderStmt = $conn->prepare("SELECT price FROM orders WHERE order_id_ref = ?");
    $orderStmt->bind_param("s", $order_id);
    $orderStmt->execute();
    $orderResult = $orderStmt->get_result();
    
    if ($orderResult->num_rows === 0) {
        echo json_encode(["success" => false, "message" => "Order not found"]);
        exit;
    }
    
    $orderRow = $orderResult->fetch_assoc();
    $amount = $orderRow['price'];

    // 2. Approve the Order in Database
    $updateStmt = $conn->prepare("UPDATE orders SET status = 'approved' WHERE order_id_ref = ?");
    $updateStmt->bind_param("s", $order_id);

    if ($updateStmt->execute()) {
        
        // 3. Update Total Sales/Revenue
        $conn->query("UPDATE settings SET key_value = key_value + 1 WHERE key_name = 'total_sales'");
        $conn->query("UPDATE settings SET key_value = key_value + $amount WHERE key_name = 'total_revenue'");

        echo json_encode([
            "success" => true, 
            "message" => "Order Approved Successfully (No email sent)"
        ]);

    } else {
        echo json_encode(["success" => false, "message" => "DB Error"]);
    }

    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Missing ID"]);
}
?>