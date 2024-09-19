<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Access-Control-Allow-Methods, Access-Control-Allow-Credentials, Content-Type, Authorization, X-Requested-With');

include "config.php";

$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'];
$password = $data['password'];

if (empty($email) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Email and password are required.']);
    exit();
}

// Prepare SQL statement to prevent SQL injection
$sql = "SELECT * FROM users WHERE email = ? AND password = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $email, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // Login successful
    $_SESSION['user_id'] = $result->fetch_assoc()['id'];
    echo json_encode(['status' => 'success', 'message' => 'Login successful.']);
} else {
    // Login failed
    echo json_encode(['status' => 'error', 'message' => 'Invalid email or password.']);
}

$stmt->close();
$conn->close();
?>
