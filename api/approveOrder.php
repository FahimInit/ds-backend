<?php
// 1. Start Output Buffering (Prevents whitespace/warnings from breaking JSON)
ob_start();

// 2. Handle CORS Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS"); 
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// 3. Handle Preflight Request (Browser checking permissions)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    ob_end_clean(); // Clean buffer
    http_response_code(200);
    exit();
}

include 'config.php';

// 4. Clear any accidental output from config.php
ob_clean(); 

$data = json_decode(file_get_contents("php://input"));

if (isset($data->order_id)) {
    $order_id = $data->order_id;
    $conn = connectDB();

    // Get price
    $stmt = $conn->prepare("SELECT price FROM orders WHERE order_id_ref = ?");
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $amount = $row['price'];

        // Approve
        $updateStmt = $conn->prepare("UPDATE orders SET status = 'approved' WHERE order_id_ref = ?");
        $updateStmt->bind_param("s", $order_id);

        if ($updateStmt->execute()) {
            // Update stats
            $conn->query("UPDATE settings SET key_value = key_value + 1 WHERE key_name = 'total_sales'");
            $conn->query("UPDATE settings SET key_value = key_value + $amount WHERE key_name = 'total_revenue'");

            echo json_encode(["success" => true, "message" => "Approved"]);
        } else {
            echo json_encode(["success" => false, "message" => "Update failed"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Order not found"]);
    }
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Missing ID"]);
}

// 5. Send the buffer
ob_end_flush();
?>