<?php
// PHP Script: config.php

// --- DATABASE CONNECTION CONFIGURATION ---
// Use Environment Variables (set on Railway) OR fallback to local defaults (XAMPP).
$db_server = getenv('DB_SERVER') ?: 'localhost';
$db_user   = getenv('DB_USERNAME') ?: 'root'; 
$db_pass   = getenv('DB_PASSWORD') ?: 'ZnIQToaUXfwMeyjJsvoNNmzYUKjcQzDo'; 
$db_name   = getenv('DB_NAME') ?: 'railway';
$db_port   = getenv('DB_PORT') ?: 3306; // Railway sets this, 3306 is MySQL default

// Function to establish a new database connection
function connectDB() {
    global $db_server, $db_user, $db_pass, $db_name, $db_port;
    
    // Connect using variables
    $conn = new mysqli($db_server, $db_user, $db_pass, $db_name, $db_port);
    
    // Check connection
    if ($conn->connect_error) {
        http_response_code(500);
        die(json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]));
    }
    
    $conn->set_charset("utf8mb4");
    return $conn;
}


// --- CORS AND HEADER CONFIGURATION ---
function setHeaders() {
    // Railway URL or your Vercel domain. Using '*' is safest for deployment but less secure.
    // Replace '*' with your Vercel domain (e.g., 'https://deepsolution.store') once confirmed.
    $allowed_origin = getenv('ALLOWED_ORIGIN') ?: '*'; 
    
    header("Access-Control-Allow-Origin: $allowed_origin"); 
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    
    // Peflight requests (OPTIONS method) sent by browsers
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit(); 
    }
    
    // Response will be JSON
    header("Content-Type: application/json; charset=UTF-8");
}
?>