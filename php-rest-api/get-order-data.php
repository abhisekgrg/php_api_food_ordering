<?php

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: http://localhost:5173');
    header('Access-Control-Allow-Methods: GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Access-Control-Allow-Credentials, Authorization, X-Requested-With');
    header('Access-Control-Allow-Credentials: true'); 
    exit(0); // End the script execution for OPTIONS request
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true'); 

include 'config.php'; // Include your database connection settings

// SQL query to get total orders for each food item
$sql = "SELECT item_name, SUM(quantity) AS total_orders FROM order_items GROUP BY item_name";
$result = mysqli_query($conn, $sql);

if ($result) {
    $data = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
} else {
    $error = mysqli_error($conn);
    echo json_encode(array('message' => 'Error fetching data: ' . $error, 'status' => false));
}

mysqli_close($conn);
?>
