<?php
$pageTitle = 'My Orders';
require_once __DIR__ . '/../includes/header.php';
requireLogin();

$conn = getDBConnection();
$orders = getUserOrders($conn, $_SESSION['user_id']);
?>

<section class="orders-page">
    <div class="container">
        <div class="page-header">
            <h1>My Orders</h1>
            <div class="section-divider"></div>
        </div>

        <?php if (empty($orders)): ?>
        <div class="empty-state animate-fade-in">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color:var(--color-gold)">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10 9 9 9 8 9"/>
            </svg>
            <h3>No orders yet</h3>
            <p>When you place an order, it will appear here.</p>
            <a href="<?= SITE_URL ?>/pages/menu.php" class="btn btn-gold">Browse Menu</a>
        </div>
        <?php else: ?>
        <div class="orders-list">
            <?php foreach ($orders as $order):
                $orderItems = getOrderDetails($conn, $order['id']);
                $orderNumber = generateOrderNumber($order['id']);
                $status = $order['status'] ?? 'pending';
            ?>
            <div class="order-card">
                <div class="order-card-header">
                    <div class="order-card-info">
                        <span class="order-number"><?= sanitize($orderNumber) ?></span>
                        <span class="order-date"><?= formatDate($order['created_at']) ?></span>
                    </div>
                    <div class="order-card-meta">
                        <span class="status-badge status-<?= sanitize($status) ?>"><?= sanitize(ucfirst($status)) ?></span>
                        <span class="order-total"><?= formatPrice($order['total']) ?></span>
                        <svg class="expand-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </div>
                </div>
                <div class="order-card-details">
                    <div class="order-timeline">
                        <?php
                        $steps = ['pending', 'confirmed', 'completed'];
                        $currentIndex = array_search($status, $steps);
                        if ($currentIndex === false) $currentIndex = 0;
                        foreach ($steps as $i => $step):
                            $stepClass = '';
                            if ($i < $currentIndex) $stepClass = 'completed';
                            elseif ($i === $currentIndex) $stepClass = 'active';
                        ?>
                        <div class="timeline-step <?= $stepClass ?>">
                            <div class="timeline-dot"></div>
                            <span class="timeline-label"><?= ucfirst($step) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="order-items-list">
                        <?php foreach ($orderItems as $item): ?>
                        <div class="order-item">
                            <img src="<?= SITE_URL ?>/img/<?= sanitize($item['image']) ?>" alt="<?= sanitize($item['name']) ?>" class="order-item-img">
                            <div class="order-item-info">
                                <span class="order-item-name"><?= sanitize($item['name']) ?></span>
                                <span class="order-item-qty">Qty: <?= (int)$item['quantity'] ?></span>
                            </div>
                            <span class="order-item-price"><?= formatPrice($item['unit_price'] * $item['quantity']) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php
$conn->close();
require_once __DIR__ . '/../includes/footer.php';
?>
