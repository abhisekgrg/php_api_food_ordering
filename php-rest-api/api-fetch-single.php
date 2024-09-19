<?php
// Allow cross-origin requests
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

// Include your database connection
include 'config.php'; // Adjust the path to your database config file

// Get the 'id' parameter from the URL query string
$food_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($food_id <= 0) {
    // Invalid or missing food ID
    echo json_encode([
        "status" => false,
        "message" => "Invalid food ID"
    ]);
    exit;
}

// Prepare the SQL statement to fetch the food item
$sql = "SELECT id, title, description, price, image_name FROM tbl_food WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $food_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if the food item was found
if ($result->num_rows > 0) {
    $food = $result->fetch_assoc();

    // Return the food item details in JSON format
    echo json_encode([
        "status" => true,
        "data" => $food
    ]);
} else {
    // No food item found for the given ID
    echo json_encode([
        "status" => false,
        "message" => "Food item not found"
    ]);
}

// Close the database connection
$stmt->close();
$conn->close();
