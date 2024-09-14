
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

$data = json_decode(file_get_contents("php://input"),true);

$id = $data['id'];
$sql = "DELETE FROM tbl_food WHERE id = {$id}";



if(mysqli_query($conn, $sql)){

    echo json_encode(array('message'=>' Record Deleted.','status'=>true));

}
else{
echo json_encode(array('message'=>'Not Deleted.','status'=>false));

}
   
  

?>