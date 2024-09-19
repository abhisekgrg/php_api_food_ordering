<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header("Access-Control-Allow-Credentials: true");

$isLoggedIn = isset($_SESSION['user_id']); // Adjust based on your session management

echo json_encode(['loggedIn' => $isLoggedIn]);
?>
