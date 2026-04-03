<?php
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../includes/admin_functions.php';

header('Content-Type: application/json');

requireAdmin();

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

    case 'create_product':
        $name = trim($input['name'] ?? '');
        $description = trim($input['description'] ?? '');
        $price = (float)($input['price'] ?? 0);
        $category = trim($input['category'] ?? '');
        $image = trim($input['image'] ?? '');
        $allergens = trim($input['allergens'] ?? '');
        $nutrition_tags = trim($input['nutrition_tags'] ?? '');
        $is_seasonal = isset($input['is_seasonal']) ? 1 : 0;
        $seasonal_label = trim($input['seasonal_label'] ?? '');
        $seasonal_start = $input['seasonal_start'] ?? null;
        $seasonal_end = $input['seasonal_end'] ?? null;

        if ($name === '' || $price <= 0 || $category === '') {
            echo json_encode(['success' => false, 'message' => 'Name, price, and category are required.']);
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO products (name, description, price, category, image, allergens, nutrition_tags, is_seasonal, seasonal_label, seasonal_start, seasonal_end, is_available) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
        $stmt->bind_param('ssdssssssss', $name, $description, $price, $category, $image, $allergens, $nutrition_tags, $is_seasonal, $seasonal_label, $seasonal_start, $seasonal_end);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Product created.', 'id' => $conn->insert_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create product.']);
        }
        break;

    case 'update_product':
        $id = (int)($input['product_id'] ?? 0);
        $name = trim($input['name'] ?? '');
        $description = trim($input['description'] ?? '');
        $price = (float)($input['price'] ?? 0);
        $category = trim($input['category'] ?? '');
        $image = trim($input['image'] ?? '');
        $allergens = trim($input['allergens'] ?? '');
        $nutrition_tags = trim($input['nutrition_tags'] ?? '');
        $is_seasonal = isset($input['is_seasonal']) ? 1 : 0;
        $seasonal_label = trim($input['seasonal_label'] ?? '');
        $seasonal_start = $input['seasonal_start'] ?? null;
        $seasonal_end = $input['seasonal_end'] ?? null;

        if ($id <= 0 || $name === '' || $price <= 0 || $category === '') {
            echo json_encode(['success' => false, 'message' => 'Invalid product data.']);
            exit;
        }

        $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, category=?, image=?, allergens=?, nutrition_tags=?, is_seasonal=?, seasonal_label=?, seasonal_start=?, seasonal_end=? WHERE id=?");
        $stmt->bind_param('ssdssssisssi', $name, $description, $price, $category, $image, $allergens, $nutrition_tags, $is_seasonal, $seasonal_label, $seasonal_start, $seasonal_end, $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Product updated.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update product.']);
        }
        break;

    case 'delete_product':
        $id = (int)($input['product_id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid product ID.']);
            exit;
        }
        $stmt = $conn->prepare("UPDATE products SET is_available = 0 WHERE id = ?");
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Product removed.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to remove product.']);
        }
        break;

    case 'update_order_status':
        $id = (int)($input['order_id'] ?? 0);
        $status = trim($input['status'] ?? '');
        $allowed = ['pending', 'processing', 'completed', 'delivered', 'cancelled'];
        if ($id <= 0 || !in_array($status, $allowed)) {
            echo json_encode(['success' => false, 'message' => 'Invalid order or status.']);
            exit;
        }
        if (updateOrderStatus($conn, $id, $status)) {
            echo json_encode(['success' => true, 'message' => 'Order status updated.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update order status.']);
        }
        break;

    case 'update_reservation_status':
        $id = (int)($input['reservation_id'] ?? 0);
        $status = trim($input['status'] ?? '');
        $allowed = ['pending', 'confirmed', 'cancelled'];
        if ($id <= 0 || !in_array($status, $allowed)) {
            echo json_encode(['success' => false, 'message' => 'Invalid reservation or status.']);
            exit;
        }
        if (updateReservationStatus($conn, $id, $status)) {
            echo json_encode(['success' => true, 'message' => 'Reservation status updated.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update reservation status.']);
        }
        break;

    case 'toggle_admin':
        $userId = (int)($input['user_id'] ?? 0);
        if ($userId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid user ID.']);
            exit;
        }
        if (toggleUserAdmin($conn, $userId)) {
            echo json_encode(['success' => true, 'message' => 'User role updated.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update user role.']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
}

$conn->close();
