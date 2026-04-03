<?php
require_once __DIR__ . '/../config/init.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$action = $input['action'] ?? '';
$token = $input['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');

if (!validateCSRFToken($token)) {
    echo json_encode(['success' => false, 'message' => 'Invalid request token.']);
    exit;
}

$conn = getDBConnection();

switch ($action) {
    case 'add':
        $productId = (int)($input['product_id'] ?? 0);
        $quantity = max(1, (int)($input['quantity'] ?? 1));
        $product = getProductById($conn, $productId);
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found.']);
            exit;
        }
        addToCart($productId, $quantity);
        echo json_encode([
            'success' => true,
            'message' => $product['name'] . ' added to cart!',
            'cartCount' => getCartCount()
        ]);
        break;

    case 'update':
        $productId = (int)($input['product_id'] ?? 0);
        $quantity = (int)($input['quantity'] ?? 0);
        updateCartQuantity($productId, $quantity);
        $cartData = getCartTotal($conn);
        echo json_encode([
            'success' => true,
            'cartCount' => getCartCount(),
            'subtotal' => formatPrice($cartData['subtotal']),
            'tax' => formatPrice($cartData['tax']),
            'shipping' => formatPrice($cartData['shipping']),
            'total' => formatPrice($cartData['total'])
        ]);
        break;

    case 'remove':
        $productId = (int)($input['product_id'] ?? 0);
        removeFromCart($productId);
        $cartData = getCartTotal($conn);
        echo json_encode([
            'success' => true,
            'message' => 'Item removed from cart.',
            'cartCount' => getCartCount(),
            'subtotal' => formatPrice($cartData['subtotal']),
            'tax' => formatPrice($cartData['tax']),
            'shipping' => formatPrice($cartData['shipping']),
            'total' => formatPrice($cartData['total'])
        ]);
        break;

    case 'get':
        $cartData = getCartTotal($conn);
        echo json_encode([
            'success' => true,
            'items' => $cartData['items'],
            'cartCount' => getCartCount(),
            'subtotal' => formatPrice($cartData['subtotal']),
            'tax' => formatPrice($cartData['tax']),
            'shipping' => formatPrice($cartData['shipping']),
            'total' => formatPrice($cartData['total'])
        ]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
}

$conn->close();
