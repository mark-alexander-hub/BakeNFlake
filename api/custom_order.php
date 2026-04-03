<?php
require_once __DIR__ . '/../config/init.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . SITE_URL . '/pages/custom-cake.php');
    exit;
}

if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
    setFlash('error', 'Invalid request. Please try again.');
    header('Location: ' . SITE_URL . '/pages/custom-cake.php');
    exit;
}

$conn = getDBConnection();

// Collect cake options from POST
$occasion = trim($_POST['occasion'] ?? '');
$flavorBase = trim($_POST['flavor_base'] ?? '');
$filling = trim($_POST['filling'] ?? '');
$frosting = trim($_POST['frosting'] ?? '');
$size = trim($_POST['size'] ?? '8"');
$tiers = (int)($_POST['tiers'] ?? 1);
$cakeMessage = trim($_POST['cake_message'] ?? '');
$dateNeeded = trim($_POST['date_needed'] ?? '');
$specialNotes = trim($_POST['special_notes'] ?? '');

// Calculate estimated price based on size and tiers
$sizePrices = ['6"' => 35, '8"' => 50, '10"' => 70, '12"' => 95];
$tierMultipliers = [1 => 1, 2 => 1.8, 3 => 2.5];
$basePrice = $sizePrices[$size] ?? 50;
$tierMult = $tierMultipliers[$tiers] ?? 1;
$estimatedPrice = round($basePrice * $tierMult, 2);

// Handle file upload for reference image
$referenceImage = '';
if (!empty($_FILES['reference_image']['name']) && $_FILES['reference_image']['error'] === UPLOAD_ERR_OK) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = mime_content_type($_FILES['reference_image']['tmp_name']);

    if (in_array($fileType, $allowedTypes)) {
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($_FILES['reference_image']['size'] <= $maxSize) {
            $ext = pathinfo($_FILES['reference_image']['name'], PATHINFO_EXTENSION);
            $uniqueName = 'cake_' . uniqid() . '_' . time() . '.' . $ext;
            $uploadDir = __DIR__ . '/../uploads/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (move_uploaded_file($_FILES['reference_image']['tmp_name'], $uploadDir . $uniqueName)) {
                $referenceImage = $uniqueName;
            }
        }
    }
}

// Validate required fields
if (empty($occasion) || empty($flavorBase) || empty($frosting) || empty($size) || empty($dateNeeded)) {
    setFlash('error', 'Please fill in all required cake options.');
    header('Location: ' . SITE_URL . '/pages/custom-cake.php');
    $conn->close();
    exit;
}

// Validate date is at least 5 days from now
$minDate = date('Y-m-d', strtotime('+5 days'));
if ($dateNeeded < $minDate) {
    setFlash('error', 'Please select a date at least 5 days from today.');
    header('Location: ' . SITE_URL . '/pages/custom-cake.php');
    $conn->close();
    exit;
}

$data = [
    'occasion' => $occasion,
    'flavor_base' => $flavorBase,
    'filling' => $filling,
    'frosting' => $frosting,
    'size' => $size,
    'tiers' => $tiers,
    'cake_message' => $cakeMessage,
    'reference_image' => $referenceImage,
    'date_needed' => $dateNeeded,
    'special_notes' => $specialNotes,
    'estimated_price' => $estimatedPrice
];

$orderId = submitCustomOrder($conn, $_SESSION['user_id'], $data);

if ($orderId) {
    setFlash('success', 'Your custom cake order has been submitted! We will contact you to confirm the details.');
} else {
    setFlash('error', 'Something went wrong. Please try again.');
}

$conn->close();
header('Location: ' . SITE_URL . '/pages/home.php');
exit;
