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





// Get search query from request
$search_query = isset($_GET['query']) ? $_GET['query'] : '';

// Prepare SQL query
$sql = "SELECT * FROM tbl_food";
$result = $conn->query($sql);

$food_items = array();

if ($result->num_rows > 0) {
    // Fetch all food items
    while($row = $result->fetch_assoc()) {
        $food_items[] = $row;
    }
}

// Perform linear search
$filtered_items = array();
foreach ($food_items as $item) {
    if (stripos($item['title'], $search_query) !== false) {
        $filtered_items[] = $item;
    }
}

// Return results as JSON
echo json_encode($filtered_items);

$conn->close();
?>
