<?php
session_start();

// Handle preflight requests (CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: http://localhost:5173');
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Access-Control-Allow-Credentials, Authorization, X-Requested-With');
    exit(0);
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Access-Control-Allow-Credentials, Authorization, X-Requested-With');

function getCartItems() {
    return isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
}

function addToCart($item) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $exists = false;
    foreach ($_SESSION['cart'] as &$cartItem) {
        if ($cartItem['id'] === $item['id']) {
            $cartItem['quantity'] += $item['quantity'];
            $exists = true;
            break;
        }
    }

    if (!$exists) {
        $_SESSION['cart'][] = $item;
    }
}

function updateCartItem($itemId, $quantity) {
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] === $itemId) {
                $item['quantity'] = $quantity;
                break;
            }
        }
    }
}

function removeCartItem($itemId) {
    if (isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) use ($itemId) {
            return $item['id'] !== $itemId;
        });
    }
}

function clearCart() {
    unset($_SESSION['cart']);
}

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'get':
            echo json_encode(getCartItems());
            break;

        case 'add':
            $data = json_decode(file_get_contents('php://input'), true);
            if (isset($data['id'], $data['name'], $data['price'], $data['quantity'])) {
                addToCart($data);
                echo json_encode(['status' => 'success', 'message' => 'Item added to cart']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
            }
            break;

        case 'update':
            $data = json_decode(file_get_contents('php://input'), true);
            if (isset($data['id'], $data['quantity'])) {
                updateCartItem($data['id'], $data['quantity']);
                echo json_encode(['status' => 'success', 'message' => 'Cart item updated']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
            }
            break;

        case 'remove':
            $data = json_decode(file_get_contents('php://input'), true);
            if (isset($data['id'])) {
                removeCartItem($data['id']);
                echo json_encode(['status' => 'success', 'message' => 'Item removed from cart']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
            }
            break;

        case 'clear':
            clearCart();
            echo json_encode(['status' => 'success', 'message' => 'Cart cleared']);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
