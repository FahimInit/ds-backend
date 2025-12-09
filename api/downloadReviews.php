<?php
// PHP Script: downloadReviews.php - Exports all reviews to a CSV file
require_once 'config.php';
// Note: setHeaders() is NOT called here

$conn = connectDB();

// Fetch all review data
$sql = "SELECT id, quote, name, email, displayName, created_at FROM reviews ORDER BY created_at DESC";
$result = $conn->query($sql);

if (!$result || $result->num_rows == 0) {
    // Optionally: Output an empty CSV or an error message
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="reviews_export_empty_' . date('Y-m-d') . '.csv"');
    $output = fopen("php://output", "w");
    fputcsv($output, ['No reviews found to export.'], ","); // Use comma delimiter
    fclose($output);
    $conn->close();
    exit;
}

// 1. Set CORRECT headers for CSV file download
header('Content-Type: text/csv; charset=utf-8'); // Set content type to CSV
header('Content-Disposition: attachment; filename="reviews_export_' . date('Y-m-d') . '.csv"'); // Use .csv extension

// 2. Create the output stream
$output = fopen("php://output", "w");

// Optional: Add UTF-8 BOM for better Excel compatibility with special characters
// fwrite($output, "\xEF\xBB\xBF"); 

// Define column headers
$headers = [
    'ID', 
    'Review Quote', 
    'Name (Private)', 
    'Email (Private)', 
    'Display Name', 
    'Date Submitted (UTC)' // Clarify timezone if applicable
];
// 3. Write headers using comma delimiter
fputcsv($output, $headers, ","); 

// 4. Write data rows using comma delimiter
while ($row = $result->fetch_assoc()) {
    // Format the date (keeping it simple ISO or similar is often best for spreadsheets)
    $date = date('Y-m-d H:i:s', strtotime($row['created_at']));
    
    // Write data fields
    fputcsv($output, [
        $row['id'],
        $row['quote'],
        $row['name'],
        $row['email'],
        $row['displayName'],
        $date
    ], ","); // Use comma delimiter
}

fclose($output);
$conn->close();
exit;

?>