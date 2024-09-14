<?php
session_start();

// CORS headers for preflight requests
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
header('Access-Control-Allow-Headers: Content-Type, Authorization');

include "config.php";

// Functions to handle the checkout
function clearCart() {
    unset($_SESSION['cart']);
}

$input = json_decode(file_get_contents('php://input'), true);

if (
    !isset($input['contactInfo']) ||
    !isset($input['items']) ||
    !isset($input['paymentMethod'])
) {
    echo json_encode(['error' => 'Invalid input.']);
    exit();
}

$contactInfo = $input['contactInfo'];
$items = $input['items'];
$paymentMethod = $input['paymentMethod'];
$total = $input['total']; // Ensure the total is being sent from the client

// Start transaction
$conn->begin_transaction();

try {
    // Insert order
    $stmt = $conn->prepare(
        "INSERT INTO orders (email, name, address, phone, total, payment_method) VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param(
        "ssssds",
        $contactInfo['email'],
        $contactInfo['name'],
        $contactInfo['address'],
        $contactInfo['phone'],
        $total,
        $paymentMethod
    );
    $stmt->execute();
    $orderId = $stmt->insert_id;
    $stmt->close();

    // Insert order items
    $stmt = $conn->prepare(
        "INSERT INTO order_items (order_id, item_id, item_name, item_price, quantity) VALUES (?, ?, ?, ?, ?)"
    );
    
    foreach ($items as $item) {
        $stmt->bind_param(
            "iisdi",
            $orderId,
            $item['id'],
            $item['name'],
            $item['price'],
            $item['quantity']
        );
        $stmt->execute();
    }
    $stmt->close();

    // Commit transaction
    $conn->commit();

    // Clear cart
    clearCart();

    echo json_encode(['success' => 'Order placed successfully!']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['error' => 'Order placement failed: ' . $e->getMessage()]);
} finally {
    $conn->close();
}
