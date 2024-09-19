<?php
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: http://localhost:5173');
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Access-Control-Allow-Credentials, Authorization, X-Requested-With');
    exit(0); // End the script execution for OPTIONS request
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Access-Control-Allow-Methods, Access-Control-Allow-Credentials, Content-Type, Authorization, X-Requested-With');

include "config.php";

// Initialize variables
$food_id = $_POST['id'];
$food_name = $_POST['title'];
$food_description = $_POST['description'];
$food_price = $_POST['price'];
$food_category_id = $_POST['category'];
$image_name = $_POST['image_name'] ?? null; // Existing image name

// Handle file upload
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_name = basename($_FILES['image']['name']);
    $upload_dir = 'uploads/'; // Set your upload directory
    $upload_file = $upload_dir . $image_name;

    if (move_uploaded_file($image_tmp_name, $upload_file)) {
        // File uploaded successfully, update image name
        $image_name = $upload_file;
    } else {
        echo json_encode(array('message'=>'Failed to upload image.','status'=>false));
        exit;
    }
}

// Update database with food item details and image name
$sql = "UPDATE tbl_food SET title = '{$food_name}', description = '{$food_description}', price = {$food_price}, category_id = {$food_category_id}, image_name = '{$image_name}' WHERE id = {$food_id}";

if (mysqli_query($conn, $sql)) {
    echo json_encode(array('message'=>'Record Updated.','status'=>true));
} else {
    echo json_encode(array('message'=>'No Record Updated.','status'=>false));
}
?>
