<?php

// ============================================
// Admin Helper Functions
// ============================================

function getDashboardStats($conn) {
    $stats = [];

    $stmt = $conn->prepare("SELECT COALESCE(SUM(total), 0) as total_revenue FROM orders");
    $stmt->execute();
    $stats['total_revenue'] = $stmt->get_result()->fetch_assoc()['total_revenue'];

    $stmt = $conn->prepare("SELECT COUNT(*) as total_orders FROM orders");
    $stmt->execute();
    $stats['total_orders'] = $stmt->get_result()->fetch_assoc()['total_orders'];

    $stmt = $conn->prepare("SELECT COUNT(*) as total_users FROM users");
    $stmt->execute();
    $stats['total_users'] = $stmt->get_result()->fetch_assoc()['total_users'];

    $stmt = $conn->prepare("SELECT COUNT(*) as total_products FROM products WHERE is_available = 1");
    $stmt->execute();
    $stats['total_products'] = $stmt->get_result()->fetch_assoc()['total_products'];

    return $stats;
}

function getRevenueByMonth($conn, $months = 6) {
    $data = [];
    $stmt = $conn->prepare("SELECT DATE_FORMAT(created_at, '%Y-%m') as month_key, DATE_FORMAT(created_at, '%b %Y') as month_label, COALESCE(SUM(total), 0) as revenue FROM orders WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL ? MONTH) GROUP BY month_key, month_label ORDER BY month_key ASC");
    $stmt->bind_param('i', $months);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'month_label' => $row['month_label'],
            'revenue' => (float)$row['revenue']
        ];
    }
    return $data;
}

function getOrdersByCategory($conn) {
    $data = [];
    $stmt = $conn->prepare("SELECT p.category, COUNT(*) as count FROM order_items oi JOIN products p ON oi.product_id = p.id GROUP BY p.category ORDER BY count DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'category' => $row['category'],
            'count' => (int)$row['count']
        ];
    }
    return $data;
}

function getRecentOrders($conn, $limit = 10) {
    $stmt = $conn->prepare("SELECT o.*, u.name as customer_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT ?");
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getTopProducts($conn, $limit = 5) {
    $stmt = $conn->prepare("SELECT p.*, COUNT(oi.id) as order_count FROM products p JOIN order_items oi ON p.id = oi.product_id GROUP BY p.id ORDER BY order_count DESC LIMIT ?");
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getAllProductsAdmin($conn) {
    $stmt = $conn->prepare("SELECT * FROM products ORDER BY category, name");
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function updateOrderStatus($conn, $orderId, $status) {
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param('si', $status, $orderId);
    return $stmt->execute();
}

function updateReservationStatus($conn, $id, $status) {
    $stmt = $conn->prepare("UPDATE reservations SET status = ? WHERE id = ?");
    $stmt->bind_param('si', $status, $id);
    return $stmt->execute();
}

function getAllOrders($conn) {
    $stmt = $conn->prepare("SELECT o.*, u.name as customer_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC");
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getAllReservations($conn) {
    $stmt = $conn->prepare("SELECT * FROM reservations ORDER BY created_at DESC");
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getAllUsers($conn) {
    $stmt = $conn->prepare("SELECT u.*, (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as order_count FROM users u ORDER BY u.created_at DESC");
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function toggleUserAdmin($conn, $userId) {
    $stmt = $conn->prepare("UPDATE users SET is_admin = 1 - is_admin WHERE id = ?");
    $stmt->bind_param('i', $userId);
    return $stmt->execute();
}
