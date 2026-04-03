<?php
$currentPage = 'orders';
require_once __DIR__ . '/../includes/admin_header.php';
$conn = getDBConnection();
$orders = getAllOrders($conn);

// Custom cake orders
$customStmt = $conn->prepare("SELECT co.*, u.name as customer_name FROM custom_orders co JOIN users u ON co.user_id = u.id ORDER BY co.created_at DESC");
$customStmt->execute();
$customOrders = $customStmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<h1 class="admin-page-title">Orders</h1>

<div class="admin-table-wrap">
    <h3>All Orders</h3>
    <?php if (empty($orders)): ?>
        <p style="color:#888;padding:20px 0;">No orders yet.</p>
    <?php else: ?>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Status</th>
                <th>Delivery Date</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= sanitize(generateOrderNumber($order['id'])) ?></td>
                <td><?= sanitize($order['customer_name']) ?></td>
                <td><?= formatPrice($order['total']) ?></td>
                <td>
                    <select class="status-select order-status-select" data-id="<?= (int)$order['id'] ?>">
                        <?php foreach (['pending','processing','completed','delivered','cancelled'] as $s): ?>
                            <option value="<?= $s ?>" <?= $order['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td><?= isset($order['delivery_date']) ? sanitize($order['delivery_date']) : '—' ?></td>
                <td><?= formatDate($order['created_at']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<!-- Custom Cake Orders -->
<div class="admin-table-wrap">
    <h3>Custom Cake Orders</h3>
    <?php if (empty($customOrders)): ?>
        <p style="color:#888;padding:20px 0;">No custom cake orders yet.</p>
    <?php else: ?>
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Occasion</th>
                <th>Size</th>
                <th>Tiers</th>
                <th>Date Needed</th>
                <th>Estimated Price</th>
                <th>Status</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customOrders as $co): ?>
            <tr>
                <td>#<?= (int)$co['id'] ?></td>
                <td><?= sanitize($co['customer_name']) ?></td>
                <td><?= sanitize($co['occasion']) ?></td>
                <td><?= sanitize($co['size']) ?></td>
                <td><?= (int)$co['tiers'] ?></td>
                <td><?= sanitize($co['date_needed']) ?></td>
                <td><?= formatPrice($co['estimated_price']) ?></td>
                <td><span class="badge badge-<?= sanitize($co['status']) ?>"><?= sanitize($co['status']) ?></span></td>
                <td><?= formatDate($co['created_at']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php
$conn->close();
require_once __DIR__ . '/../includes/admin_footer.php';
?>
