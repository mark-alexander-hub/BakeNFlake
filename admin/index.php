<?php
$currentPage = 'dashboard';
require_once __DIR__ . '/../includes/admin_header.php';
$conn = getDBConnection();
$stats = getDashboardStats($conn);
$revenueData = getRevenueByMonth($conn, 6);
$categoryData = getOrdersByCategory($conn);
$recentOrders = getRecentOrders($conn, 10);
?>

<h1 class="admin-page-title">Dashboard</h1>

<!-- Stats Cards -->
<div class="admin-stats">
    <div class="admin-stat-card">
        <div class="admin-stat-icon revenue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
        </div>
        <div class="admin-stat-info">
            <h3>Total Revenue</h3>
            <div class="stat-value">$<span class="counter-animate" data-target="<?= (float)$stats['total_revenue'] ?>"><?= number_format($stats['total_revenue'], 2) ?></span></div>
        </div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-icon orders">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        </div>
        <div class="admin-stat-info">
            <h3>Total Orders</h3>
            <div class="stat-value"><span class="counter-animate" data-target="<?= (int)$stats['total_orders'] ?>"><?= $stats['total_orders'] ?></span></div>
        </div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-icon users">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
        </div>
        <div class="admin-stat-info">
            <h3>Total Users</h3>
            <div class="stat-value"><span class="counter-animate" data-target="<?= (int)$stats['total_users'] ?>"><?= $stats['total_users'] ?></span></div>
        </div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-icon products">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
        </div>
        <div class="admin-stat-info">
            <h3>Products</h3>
            <div class="stat-value"><span class="counter-animate" data-target="<?= (int)$stats['total_products'] ?>"><?= $stats['total_products'] ?></span></div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="admin-charts">
    <div class="admin-chart-card">
        <h3>Revenue (Last 6 Months)</h3>
        <canvas id="revenueChart" height="260"
            data-labels='<?= json_encode(array_column($revenueData, "month_label")) ?>'
            data-values='<?= json_encode(array_column($revenueData, "revenue")) ?>'></canvas>
    </div>
    <div class="admin-chart-card">
        <h3>Orders by Category</h3>
        <canvas id="categoryChart" height="260"
            data-labels='<?= json_encode(array_column($categoryData, "category")) ?>'
            data-values='<?= json_encode(array_column($categoryData, "count")) ?>'></canvas>
    </div>
</div>

<!-- Recent Orders -->
<div class="admin-table-wrap">
    <h3>Recent Orders</h3>
    <?php if (empty($recentOrders)): ?>
        <p style="color:#888;padding:20px 0;">No orders yet.</p>
    <?php else: ?>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recentOrders as $order): ?>
            <tr>
                <td><?= sanitize(generateOrderNumber($order['id'])) ?></td>
                <td><?= sanitize($order['customer_name']) ?></td>
                <td><?= formatPrice($order['total']) ?></td>
                <td><span class="badge badge-<?= sanitize($order['status']) ?>"><?= sanitize($order['status']) ?></span></td>
                <td><?= formatDate($order['created_at']) ?></td>
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
