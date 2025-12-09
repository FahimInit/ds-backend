<?php
// PHP Script: addReview.php
require_once 'config.php';
setHeaders();

$conn = connectDB();
$data = json_decode(file_get_contents("php://input"), true);

// Check for all required fields
if (!isset($data['review']) || !isset($data['email']) || !isset($data['rating'])) {
    http_response_code(400);
    die(json_encode(["success" => false, "message" => "Required data (review, email, or rating) missing."]));
}

// Extract data
$quote = $data['review'];
$email = $data['email'];
$name = isset($data['name']) ? $data['name'] : '';
$displayName = isset($data['displayName']) ? $data['displayName'] : 'Anonymous';
$rating = (int)$data['rating']; // Ensure rating is an integer

// Validate rating (basic check)
if ($rating < 1 || $rating > 5) {
    http_response_code(400);
    die(json_encode(["success" => false, "message" => "Invalid rating value."]));
}

// Prepare the SQL statement
$sql = "INSERT INTO reviews (quote, name, email, displayName, rating) VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    die(json_encode(["success" => false, "message" => "SQL Prepare failed: " . $conn->error]));
}

// Bind parameters
$stmt->bind_param("ssssi", $quote, $name, $email, $displayName, $rating);

// Execute the statement
if ($stmt->execute()) {
    $new_id = $stmt->insert_id;
    http_response_code(201); // Created
    echo json_encode([
        "success" => true,
        "message" => "Review added successfully.",
        "id" => $new_id
    ]);
} else {
    http_response_code(500); // Internal Server Error
    error_log("SQL Execute failed: " . $stmt->error); // Log the error
    echo json_encode(["success" => false, "message" => "Execution failed: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>