<?php
require_once __DIR__ . '/../config/init.php';

header('Content-Type: application/json');

$q = trim($_GET['q'] ?? '');

if (empty($q)) {
    echo json_encode(['results' => []]);
    exit;
}

$conn = getDBConnection();
$products = searchProducts($conn, $q);
$conn->close();

$results = [];
foreach ($products as $p) {
    $results[] = [
        'id' => $p['id'],
        'name' => $p['name'],
        'price' => $p['price'],
        'category' => $p['category'],
        'image' => $p['image']
    ];
}

echo json_encode(['results' => $results]);
