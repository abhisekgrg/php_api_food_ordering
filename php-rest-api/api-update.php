
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

$data = json_decode(file_get_contents("php://input"),true);

$food_id = $data['fid'];
$food_name = $data['fname'];
$food_description = $data['fdescription'];
$food_price = $data['fprice'];
$food_category_id = $data['fcategory-id'];


include "config.php";


$sql = "UPDATE tbl_food SET title = '{$food_name}', description = '{$food_description}', price = {$food_price}, category_id ={$food_category_id}
WHERE id = {$food_id}";



if(mysqli_query($conn, $sql)){

    echo json_encode(array('message'=>'Record Updated.','status'=>true));
}
else{
echo json_encode(array('message'=>'No Record Updated.','status'=>false));

}
   
  

?>