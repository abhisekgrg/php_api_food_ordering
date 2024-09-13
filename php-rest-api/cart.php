<?php
session_start();

// Handle the OPTIONS request for CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
    exit(0); // Exit to prevent further processing for the OPTIONS request
}

// Set the headers for the actual request
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

// Functions to manage cart items
function getCartItems() {
    return isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
}

function addToCart($item) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    $_SESSION['cart'][] = $item;
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

// Handle the request
$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'get':
            echo json_encode(getCartItems());
            break;
            case 'add':
                $data = json_decode(file_get_contents('php://input'), true);
                if (isset($data['id']) && isset($data['name']) && isset($data['price'])) {
                    $item = [
                        'id' => $data['id'],
                        'name' => $data['name'],
                        'price' => $data['price'],
                        
                    ];
                    addToCart($item);
                    echo json_encode(['status' => 'success', 'message' => 'Item added to cart']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
                }
                break;
        case 'update':
            $data = json_decode(file_get_contents('php://input'), true);
            if (isset($data['id']) && isset($data['quantity'])) {
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
?>
