<?php
require_once __DIR__ . '/../config/init.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$email = trim($input['email'] ?? '');

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

$conn = getDBConnection();

$result = subscribeNewsletter($conn, $email);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Successfully subscribed!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Could not subscribe. Please try again.']);
}

$conn->close();
