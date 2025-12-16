<?php
// PHP Script: config.php

// --- DATABASE CONNECTION CONFIGURATION ---
$db_server = getenv('DB_SERVER') ?: 'localhost';
$db_user   = getenv('DB_USERNAME') ?: 'root'; 
$db_pass   = getenv('DB_PASSWORD') ?: ''; 
$db_name   = getenv('DB_NAME') ?: 'deepsolution_db';
$db_port   = getenv('DB_PORT') ?: 3306;

function connectDB() {
    global $db_server, $db_user, $db_pass, $db_name, $db_port;
    
    // Suppress errors during connection to prevent leaking sensitive info
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $conn = new mysqli($db_server, $db_user, $db_pass, $db_name, $db_port);
        $conn->set_charset("utf8mb4");
        return $conn;
    } catch (mysqli_sql_exception $e) {
        // Log the error internally but return a generic JSON error to the user
        error_log("DB Connection Failed: " . $e->getMessage());
        http_response_code(500);
        die(json_encode(["success" => false, "message" => "Database connection error."]));
    }
}

// --- CORS AND HEADER CONFIGURATION ---
function setHeaders() {
    // SECURITY UPDATE: Prefer the specific domain over wildcard
    $allowed_origin = getenv('ALLOWED_ORIGIN') ?: 'https://www.deepsolution.store';
    
    header("Access-Control-Allow-Origin: $allowed_origin"); 
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Limit methods to what you use
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    header("Content-Type: application/json; charset=UTF-8");

    // Handle Preflight Request
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit(); 
    }
}