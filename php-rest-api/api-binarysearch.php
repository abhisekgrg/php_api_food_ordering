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

// Prepare SQL query to fetch all food items
$sql = "SELECT * FROM tbl_food";
$result = $conn->query($sql);

$food_items = array();

if ($result->num_rows > 0) {
    // Fetch all food items
    while($row = $result->fetch_assoc()) {
        $food_items[] = $row;
    }
}

// Sort the food items by title (case-insensitive sorting)
usort($food_items, function($a, $b) {
    return strcasecmp($a['title'], $b['title']);
});

// Perform binary search on the sorted array
function binarySearch($items, $search_query) {
    $low = 0;
    $high = count($items) - 1;

    while ($low <= $high) {
        $mid = floor(($low + $high) / 2);
        $midValue = $items[$mid]['title'];

        // Case-insensitive comparison
        $cmp = strcasecmp($midValue, $search_query);

        if ($cmp === 0) {
            // Exact match found
            return [$items[$mid]];
        } elseif ($cmp < 0) {
            // Search in the right half
            $low = $mid + 1;
        } else {
            // Search in the left half
            $high = $mid - 1;
        }
    }

    return []; // No match found
}

// Execute binary search
$filtered_items = binarySearch($food_items, $search_query);

// Return results as JSON
echo json_encode($filtered_items);

$conn->close();
?>
