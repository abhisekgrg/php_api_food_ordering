
<?php
header('Content-Type: application/json');
header('Acess-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods:DELETE');
header('Access-Control-Allow-Headers:Access-Control-Allow-Headers,Access-Control-Allow-Methods,Content-Type,
Authorization, X-Requested-With');

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