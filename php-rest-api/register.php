<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Access-Control-Allow-Credentials');

include "config.php";

$data = json_decode(file_get_contents("php://input"));

$fullName = $data->fullName;
$email = $data->email;
$password = $data->password;

// Validate data
if (empty($fullName) || empty($email) || empty($password)) {
    echo json_encode(array('success' => false, 'message' => 'All fields are required.'));
    exit();
}

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Check if email already exists
$query = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(array('success' => false, 'message' => 'Email is already registered.'));
    exit();
}

// Insert user data into the database
$sql = "INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $fullName, $email, $hashedPassword);

if ($stmt->execute()) {
    echo json_encode(array('success' => true, 'message' => 'Registration successful.'));
} else {
    echo json_encode(array('success' => false, 'message' => 'Registration failed.'));
}

$stmt->close();
$conn->close();
?>
