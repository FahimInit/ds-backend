<?php
// PHP Script: getAverageRating.php
require_once 'config.php';
setHeaders();

$conn = connectDB();

// SQL to calculate average rating and count of ALL reviews
$sql = "SELECT AVG(rating) AS average_rating, COUNT(id) AS total_reviews FROM reviews";
$result = $conn->query($sql);

$ratingData = [
    'average_rating' => 0.0,
    'total_reviews' => 0
];

if ($result && $row = $result->fetch_assoc()) {
    // Check if average_rating is not NULL before rounding
    if ($row['average_rating'] !== null) {
         $ratingData['average_rating'] = round((float)$row['average_rating'], 1); // Round to 1 decimal place
    }
    $ratingData['total_reviews'] = (int)$row['total_reviews'];
} else {
     error_log("Failed to fetch average rating: " . $conn->error); // Log error if query fails
}


echo json_encode(["success" => true, "data" => $ratingData]);

$conn->close();
?>