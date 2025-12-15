<?php
// PHP Script: verifyPassword.php
require_once 'config.php';
setHeaders();

$conn = connectDB();
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['password'])) {
    http_response_code(400);
    die(json_encode(["success" => false, "message" => "Password missing."]));
}

$input_password = $data['password'];

// 1. Fetch the stored hash
$sql = "SELECT key_value FROM settings WHERE key_name = 'admin_password'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $stored_hash = $row['key_value'];

    // 2. Verify the password
    if (password_verify($input_password, $stored_hash)) {
        echo json_encode(["success" => true, "message" => "Login successful."]);
    } else {
        http_response_code(401); // Unauthorized
        echo json_encode(["success" => false, "message" => "Incorrect password."]);
    }
} else {
    // If no password is set in DB, block access or allow (depending on policy).
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "No admin password configured in database."]);
}

$conn->close();
?>