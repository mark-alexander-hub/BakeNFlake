<?php
require_once __DIR__ . '/../config/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . SITE_URL . '/pages/home.php');
    exit;
}

$token = $_POST['csrf_token'] ?? '';
if (!validateCSRFToken($token)) {
    setFlash('error', 'Invalid request. Please try again.');
    header('Location: ' . SITE_URL . '/pages/home.php#reserve');
    exit;
}

$name = trim($_POST['name'] ?? '');
$people = (int)($_POST['people'] ?? 0);
$date = trim($_POST['date'] ?? '');
$message = trim($_POST['message'] ?? '');

if (empty($name) || $people < 1 || empty($date)) {
    setFlash('error', 'Please fill in all required reservation fields.');
    header('Location: ' . SITE_URL . '/pages/home.php#reserve');
    exit;
}

$conn = getDBConnection();
$userId = isLoggedIn() ? $_SESSION['user_id'] : null;

$stmt = $conn->prepare("INSERT INTO reservations (user_id, name, people, reservation_date, message) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param('isiss', $userId, $name, $people, $date, $message);
$stmt->execute();
$conn->close();

setFlash('success', 'Reservation confirmed! We look forward to seeing you.');
header('Location: ' . SITE_URL . '/pages/home.php#reserve');
exit;
