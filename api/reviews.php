<?php
require_once __DIR__ . '/../config/init.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$csrfToken = $_POST['csrf_token'] ?? '';
if (!validateCSRFToken($csrfToken)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
    exit;
}

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'You must be logged in to submit a review.']);
    exit;
}

$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
$text = trim($_POST['review_text'] ?? '');

if ($productId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product.']);
    exit;
}

if ($rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Rating must be between 1 and 5.']);
    exit;
}

if (empty($text)) {
    echo json_encode(['success' => false, 'message' => 'Review text is required.']);
    exit;
}

$conn = getDBConnection();
$result = submitReview($conn, $productId, $_SESSION['user_id'], $rating, $text);
$conn->close();

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Review submitted successfully!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to submit review. Please try again.']);
}
