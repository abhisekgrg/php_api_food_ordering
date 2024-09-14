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

$food_name = $_POST['fname'];
$food_description = $_POST['fdescription'];
$food_price = $_POST['fprice'];
$food_category_id = $_POST['fcategory_id'];

$image = $_FILES['image'];

// Check if an image file is provided
if ($image && $image['tmp_name']) {
    $target_dir = "uploads/"; // Directory to save the uploaded image
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true); // Create the directory if it doesn't exist
    }

    $image_name = basename($image['name']);
    $target_file = $target_dir . uniqid() . '-' . $image_name;
    $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Only allow certain file formats
    $allowed_file_types = array("jpg", "jpeg", "png", "gif");

    if (in_array($image_file_type, $allowed_file_types)) {
        if (move_uploaded_file($image['tmp_name'], $target_file)) {
            $image_path = $target_file; // Save the relative path to the database
        } else {
            echo json_encode(array('message' => 'Error uploading the image.', 'status' => false));
            exit();
        }
    } else {
        echo json_encode(array('message' => 'Only JPG, JPEG, PNG, & GIF files are allowed.', 'status' => false));
        exit();
    }
} else {
    $image_path = ""; // If no image is provided, save an empty string or a default image path
}

$sql = "INSERT INTO tbl_food (title, description, price, category_id, image_name) 
VALUES ('{$food_name}', '{$food_description}', {$food_price}, {$food_category_id}, '{$image_path}')";

if (mysqli_query($conn, $sql)) {
    echo json_encode(array('message' => 'Record Inserted.', 'status' => true));
} else {
    echo json_encode(array('message' => 'No Record Inserted.', 'status' => false));
}

mysqli_close($conn);
?>
