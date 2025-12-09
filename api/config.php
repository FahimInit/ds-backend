<?php
// PHP Script: config.php

// Define constants for database connection
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); 
define('DB_PASSWORD', '');     
define('DB_NAME', 'deepsolution_db');

// Function to establish a new database connection
function connectDB() {
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        http_response_code(500);
        // Do not die with JSON here, return the connection error message
        die(json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]));
    }
    
    $conn->set_charset("utf8mb4");
    return $conn;
}

// Function to set up headers for CORS and JSON response
function setHeaders() {
    // 1. Set up of the origin of the React development server.
    $react_dev_origin = 'http://192.168.0.101:5173';
    header("Access-Control-Allow-Origin: $react_dev_origin"); 
    
    // 2. Necessary HTTP methods
    header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
    
    // 3. Necessary headers
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    
    // 4. Peflight requests (OPTIONS method) sent by browsers
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit(); 
    }
    
    // 5. Response will be JSON
    header("Content-Type: application/json; charset=UTF-8");
}
?>