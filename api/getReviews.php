<?php
// PHP Script: getReviews.php
require_once 'config.php';
setHeaders();

$conn = connectDB();

// Fetch all reviews, ordered by creation date (newest first)
$sql = "SELECT id, quote, displayName, created_at, name, email, rating FROM reviews ORDER BY created_at DESC";
$result = $conn->query($sql);

$reviews = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
    echo json_encode(["success" => true, "reviews" => $reviews]);
} else {
    // Return empty array if no reviews found
    echo json_encode(["success" => true, "reviews" => []]);
}

$conn->close();
?>