<?php
require_once __DIR__ . '/../config/init.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$csrfToken = $input['csrf_token'] ?? '';
if (!validateCSRFToken($csrfToken)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
    exit;
}

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'You must be logged in to manage favorites.']);
    exit;
}

$productId = isset($input['product_id']) ? (int)$input['product_id'] : 0;

if ($productId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product.']);
    exit;
}

$conn = getDBConnection();
$userId = $_SESSION['user_id'];
$favorited = toggleFavorite($conn, $userId, $productId);
$conn->close();

$message = $favorited ? 'Added to favorites.' : 'Removed from favorites.';
echo json_encode(['success' => true, 'favorited' => $favorited, 'message' => $message]);
