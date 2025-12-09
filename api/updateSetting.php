<?php
// PHP Script: updateSetting.php
require_once 'config.php';
setHeaders();

// ... (Error reporting and connection code) ...
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log'); // Check this file!

$conn = connectDB();
$input_data = file_get_contents("php://input");
error_log("updateSetting.php - Received: " . $input_data);
$data = json_decode($input_data, true);

if (!isset($data['key_name']) || !isset($data['key_value'])) {
    http_response_code(400);
    error_log("updateSetting.php - Error: Key/Value missing.");
    die(json_encode(["success" => false, "message" => "Key name or value missing."]));
}

$key_name = $data['key_name'];
$key_value = $data['key_value'];
error_log("updateSetting.php - Processing: key=" . $key_name);

$sql = "UPDATE settings SET key_value = ? WHERE key_name = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    error_log("updateSetting.php - SQL Prepare failed: " . $conn->error);
    die(json_encode(["success" => false, "message" => "SQL Prepare failed: " . $conn->error]));
}

// --- MODIFICATION START ---
// Determine the correct data type for binding
if ($key_name === 'price' || $key_name === 'offer_price' || $key_name === 'total_revenue' || $key_name === 'total_sales') {
    // Use 'd' for double/decimal numbers
    $stmt->bind_param("ds", $key_value, $key_name);
    error_log("updateSetting.php - Binding as double/string (d, s)");
} else {
    // Use 's' for standard strings
    $stmt->bind_param("ss", $key_value, $key_name);
     error_log("updateSetting.php - Binding as string/string (s, s)");
}
// --- MODIFICATION END ---


if ($stmt->execute()) {
    // ... (rest of success/failure logic) ...
    if ($stmt->affected_rows > 0) {
        http_response_code(200);
        error_log("updateSetting.php - Success (updated rows) for key: " . $key_name);
        echo json_encode(["success" => true, "message" => "Setting updated successfully."]);
    } else {
        http_response_code(200);
        error_log("updateSetting.php - Success (0 rows affected) for key: " . $key_name);
        echo json_encode(["success" => true, "message" => "Setting value was likely unchanged."]);
    }
} else {
    http_response_code(500);
    error_log("updateSetting.php - SQL Execute failed: " . $stmt->error);
    echo json_encode(["success" => false, "message" => "Execution failed: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>