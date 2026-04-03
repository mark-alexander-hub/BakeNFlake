<?php
require_once __DIR__ . '/../config/init.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!validateCSRFToken($input['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
    exit;
}

$code = trim($input['code'] ?? '');
if (empty($code)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a discount code.']);
    exit;
}

$conn = getDBConnection();

$discount = validateDiscountCode($conn, $code);
if (!$discount) {
    echo json_encode(['success' => false, 'message' => 'Invalid or expired discount code.']);
    $conn->close();
    exit;
}

// Store discount in session
$_SESSION['applied_discount'] = $discount;

// Calculate discount amount using cart subtotal
$cartData = getCartTotal($conn);
$subtotal = $cartData['subtotal'];
$discountAmount = applyDiscount($subtotal, $discount);

if ($discountAmount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Minimum order of ' . formatPrice($discount['min_order']) . ' required for this code.']);
    $conn->close();
    exit;
}

// Build display strings
if ($discount['type'] === 'percentage') {
    $discountDisplay = $discount['value'] . '%';
} else {
    $discountDisplay = formatPrice($discount['value']);
}

$description = sanitize($discount['code']) . ': ' . ($discount['type'] === 'percentage' ? $discount['value'] . '% off' : formatPrice($discount['value']) . ' off');

$newTotal = $subtotal - $discountAmount + $cartData['tax'] + $cartData['shipping'];

echo json_encode([
    'success' => true,
    'discount_display' => $discountDisplay,
    'description' => $description,
    'new_total' => formatPrice($newTotal)
]);

$conn->close();
