<?php

// Handle CORS and OPTIONS request for preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: http://localhost:5173');
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers,Access-Control-Allow-Credentials, Authorization, X-Requested-With');
    exit(0); // End the script execution for OPTIONS request
}

// Set headers for JSON response and CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Access-Control-Allow-Methods,Access-Control-Allow-Credentials, Content-Type, Authorization, X-Requested-With');

// Connect to the database
include "config.php";

// Function to get all food items from the database
function getAllFoodItems($conn) {
    $query = "SELECT id, title, description, price, image_name, ingredients FROM tbl_food";
    $result = $conn->query($query);

    $foodItems = [];
    while ($row = $result->fetch_assoc()) {
        $foodItems[] = $row;
    }

    return $foodItems;
}

// Function to calculate similarity score between two sets of ingredients
function calculateSimilarity($ingredients1, $ingredients2) {
    // Handle NULL or empty ingredient fields
    if (empty($ingredients1) || empty($ingredients2)) {
        return 0;
    }

    $ingredients1Array = explode(',', strtolower(trim($ingredients1)));
    $ingredients2Array = explode(',', strtolower(trim($ingredients2)));

    $intersection = array_intersect($ingredients1Array, $ingredients2Array);
    $union = array_unique(array_merge($ingredients1Array, $ingredients2Array));

    if (count($union) === 0) {
        return 0;
    }

    return count($intersection) / count($union); // Jaccard similarity
}

// Function to get recommended food items based on a given food item
function getRecommendations($conn, $foodId) {
    $foodItems = getAllFoodItems($conn);

    // Find the target food item
    $targetFood = null;
    foreach ($foodItems as $item) {
        if ($item['id'] == $foodId) {
            $targetFood = $item;
            break;
        }
    }

    if (!$targetFood) {
        return [];
    }

    // Calculate similarity scores for all other food items
    $recommendations = [];
    foreach ($foodItems as $item) {
        if ($item['id'] != $foodId) {
            $similarity = calculateSimilarity($targetFood['ingredients'], $item['ingredients']);
            $recommendations[] = [
                'id' => $item['id'],
                'title' => $item['title'],
                'description' => $item['description'],
                'image_name' => $item['image_name'],
                'price' => $item['price'],
                'similarity' => $similarity
            ];
        }
    }

    // Sort recommendations by similarity score in descending order
    usort($recommendations, function($a, $b) {
        return $b['similarity'] <=> $a['similarity'];
    });

    return $recommendations;
}

// Check if the request is GET and if foodId is provided
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['foodId'])) {
    $foodId = intval($_GET['foodId']);
    $recommendations = getRecommendations($conn, $foodId);

    echo json_encode([
        'status' => true,
        'data' => $recommendations
    ]);
} else {
    echo json_encode([
        'status' => false,
        'message' => 'Invalid request'
    ]);
}

$conn->close();
?>
