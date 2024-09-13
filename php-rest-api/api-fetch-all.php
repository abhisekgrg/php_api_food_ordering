<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

include "config.php"; // This includes your database connection settings

$sql = "SELECT * FROM tbl_food";
$result = mysqli_query($conn, $sql) or die("SQL Query Failed");

if (mysqli_num_rows($result) > 0) {
    $food_items = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Get filter parameters from the request (if they exist)
    $min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
    $max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : PHP_INT_MAX;
    $category = isset($_GET['category']) ? $_GET['category'] : '';

    // Apply filtering
    $filtered_items = array_filter($food_items, function ($food) use ($min_price, $max_price, $category) {
        $is_within_price_range = $food['price'] >= $min_price && $food['price'] <= $max_price;
        $is_in_category = empty($category) || $food['category'] === $category;
        return $is_within_price_range && $is_in_category;
    });

    // Return the filtered data
    if (!empty($filtered_items)) {
        echo json_encode(array_values($filtered_items));
    } else {
        echo json_encode(array('message' => 'No Record Found.', 'status' => false));
    }
} else {
    echo json_encode(array('message' => 'No Record Found.', 'status' => false));
}
?>
