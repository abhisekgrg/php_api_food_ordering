<?php
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: http://localhost:5173');
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers,Access-Control-Allow-Credentials, Authorization, X-Requested-With');
    exit(0); // End the script execution for OPTIONS request
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Access-Control-Allow-Methods,Access-Control-Allow-Credentials, Content-Type, Authorization, X-Requested-With');

include "config.php";

$sql = "SELECT * FROM tbl_food";
$result = mysqli_query($conn, $sql) or die("SQL Query Failed");

if (mysqli_num_rows($result) > 0) {
    $food_items = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Get filter parameters from the request
    $min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
    $max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : PHP_INT_MAX;
    $category = isset($_GET['category']) ? $_GET['category'] : '';

    // Apply filtering
    $filtered_items = array_filter($food_items, function ($food) use ($min_price, $max_price, $category) {
        $is_within_price_range = $food['price'] >= $min_price && $food['price'] <= $max_price;
        $is_in_category = empty($category) || $food['category'] === $category;
        return $is_within_price_range && $is_in_category;
    });

    if (!empty($filtered_items)) {
        echo json_encode(array_values($filtered_items));
    } else {
        echo json_encode(array('message' => 'No Record Found.', 'status' => false));
    }
} else {
    echo json_encode(array('message' => 'No Record Found.', 'status' => false));
}
?>
