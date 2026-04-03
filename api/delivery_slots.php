<?php
require_once __DIR__ . '/../config/init.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['slots' => []]);
    exit;
}

$date = trim($_GET['date'] ?? '');
if (empty($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    echo json_encode(['slots' => []]);
    exit;
}

$conn = getDBConnection();

// Generate slots for the date if they don't exist yet
$availableSlots = getAvailableSlots($conn, $date);

// Get ALL slots for the date (including full ones) so the UI can show them greyed out
$stmt = $conn->prepare("SELECT slot_time, current_orders, max_orders FROM delivery_slots WHERE slot_date = ? ORDER BY slot_time");
$stmt->bind_param('s', $date);
$stmt->execute();
$allSlots = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$availableTimes = [];
foreach ($availableSlots as $s) {
    $availableTimes[] = $s['slot_time'];
}

$slots = [];
foreach ($allSlots as $slot) {
    $slots[] = [
        'slot_time' => $slot['slot_time'],
        'available' => in_array($slot['slot_time'], $availableTimes)
    ];
}

echo json_encode(['slots' => $slots]);

$conn->close();
