<?php
// PHP Script: downloadOrders.php - Exports all order data to CSV
require_once 'config.php';

$conn = connectDB();

// Fetch all order data, including buyer details, ordered by date
$sql = "SELECT id, order_id_ref, product_name, price, status, buyer_name, buyer_email, buyer_phone, created_at 
        FROM orders 
        ORDER BY created_at DESC";
$result = $conn->query($sql);

if (!$result || $result->num_rows == 0) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="orders_export_empty_' . date('Y-m-d') . '.csv"');
    $output = fopen("php://output", "w");
    fputcsv($output, ['No orders found to export.'], ","); 
    fclose($output);
    $conn->close();
    exit;
}

// Set headers for CSV file download
header('Content-Type: text/csv; charset=utf-8'); 
header('Content-Disposition: attachment; filename="orders_export_' . date('Y-m-d') . '.csv"'); 

$output = fopen("php://output", "w");

// Define column headers
$headers = [
    'DB_ID', 
    'Order_Ref_ID', 
    'Product_Name', 
    'Price', 
    'Status', 
    'Buyer_Name', 
    'Buyer_Email', 
    'Buyer_Phone', 
    'Order_Date_UTC'
];
fputcsv($output, $headers, ","); 

// Write data rows
while ($row = $result->fetch_assoc()) {
    $date = date('Y-m-d H:i:s', strtotime($row['created_at']));
    
    fputcsv($output, [
        $row['id'],
        $row['order_id_ref'],
        $row['product_name'],
        $row['price'],
        $row['status'],
        $row['buyer_name'],
        $row['buyer_email'],
        $row['buyer_phone'],
        $date
    ], ","); 
}

fclose($output);
$conn->close();
exit;
?>