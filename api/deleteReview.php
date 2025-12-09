<?php
// PHP Script: deleteReview.php
require_once 'config.php';
setHeaders();

$conn = connectDB();

// 1. Get the JSON data sent from the React frontend
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id']) || !is_numeric($data['id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(["success" => false, "message" => "Review ID is missing or invalid."]);
    $conn->close();
    exit;
}

$review_id = $data['id'];

// 2. Prepare the SQL statement for security
// Use a prepared statement to prevent SQL Injection
$sql = "DELETE FROM reviews WHERE id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "SQL Prepare failed: " . $conn->error]);
    $conn->close();
    exit;
}

// 3. Bind the integer parameter
$stmt->bind_param("i", $review_id); // 'i' indicates integer type

// 4. Execute the statement
if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        http_response_code(200); // OK
        echo json_encode(["success" => true, "message" => "Review deleted successfully."]);
    } else {
        http_response_code(404); // Not Found
        echo json_encode(["success" => false, "message" => "Review not found."]);
    }
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(["success" => false, "message" => "Execution failed: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>