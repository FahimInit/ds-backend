<?php
// PHP Script: getSettings.php
require_once 'config.php';
setHeaders();

$conn = connectDB();

// Fetch all settings
$sql = "SELECT key_name, key_value FROM settings"; 
$result = $conn->query($sql);

$settings = [];

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        
        // --- CRITICAL SECURITY CHECK ---
        if ($row['key_name'] === 'admin_password') {
            continue; 
        }
        
        $settings[$row['key_name']] = $row['key_value'];
    }
    
    echo json_encode(["success" => true, "settings" => $settings]);
} else {
    // Log error if query failed
    if (!$result) {
        error_log("Error fetching settings: " . $conn->error);
    }
    // Return success with empty settings so frontend doesn't crash
    echo json_encode(["success" => true, "settings" => []]);
}

$conn->close();
?>