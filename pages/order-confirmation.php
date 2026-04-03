<?php
$pageTitle = 'Order Confirmed';
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$orderId = (int)($_GET['id'] ?? 0);
if ($orderId <= 0) {
    header('Location: ' . SITE_URL . '/pages/home.php');
    exit;
}

$conn = getDBConnection();

// Load order
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param('ii', $orderId, $_SESSION['user_id']);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    setFlash('error', 'Order not found.');
    header('Location: ' . SITE_URL . '/pages/home.php');
    $conn->close();
    exit;
}

$orderItems = getOrderDetails($conn, $orderId);
$orderNumber = generateOrderNumber($orderId);

$cartCount = getCartCount();
$user = getCurrentUser();
$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed | <?= SITE_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= SITE_URL ?>/css/style.css">
    <meta name="csrf-token" content="<?= sanitize($csrfToken) ?>">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
</head>
<body>
    <!-- Navigation (minimal) -->
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <a href="<?= SITE_URL ?>/pages/home.php" class="nav-logo">
                <img src="<?= SITE_URL ?>/img/logo.svg" alt="<?= SITE_NAME ?>">
                <span>Bake'n Flake</span>
            </a>
        </div>
    </nav>

    <main>
        <section class="checkout-page">
            <div class="container">
                <div class="order-confirmed animate-fade-in">
                    <!-- Animated Checkmark -->
                    <svg class="checkmark-svg" width="80" height="80" viewBox="0 0 52 52">
                        <circle class="checkmark-circle" cx="26" cy="26" r="24" fill="none" stroke="#DAA520" stroke-width="3"/>
                        <path class="checkmark-check" fill="none" stroke="#DAA520" stroke-width="3" d="M14 27l7 7 16-16"/>
                    </svg>

                    <h2>Order Confirmed!</h2>
                    <p>Thank you, <?= sanitize($order['customer_name']) ?>! Your order is being prepared with love.</p>

                    <div class="order-number" style="font-size:1.2rem;font-weight:700;color:var(--color-gold);margin:1rem 0">
                        <?= sanitize($orderNumber) ?>
                    </div>

                    <?php if (!empty($order['points_earned']) && $order['points_earned'] > 0): ?>
                    <p class="points-earned-msg">You earned <strong><?= (int)$order['points_earned'] ?> loyalty points</strong> with this order!</p>
                    <?php endif; ?>

                    <div class="confirmation-actions" style="margin-top:2rem;display:flex;gap:1rem;justify-content:center;flex-wrap:wrap">
                        <button type="button" class="btn btn-outline" onclick="document.getElementById('emailModalOverlay').style.display='flex'">View Email Receipt</button>
                        <a href="<?= SITE_URL ?>/pages/home.php" class="btn btn-gold">Back to Home</a>
                        <a href="<?= SITE_URL ?>/pages/orders.php" class="btn btn-outline">View Orders</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Email Receipt Modal -->
        <div class="email-modal-overlay" id="emailModalOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:1000;align-items:center;justify-content:center;padding:1rem">
            <div class="email-modal" style="background:#fff;max-width:600px;width:100%;max-height:90vh;overflow-y:auto;border-radius:12px;box-shadow:0 20px 60px rgba(0,0,0,0.3)">
                <!-- Email Template Header -->
                <div class="email-template-header" style="background:linear-gradient(135deg,#2C1810,#5C3317);color:#fff;padding:2rem;text-align:center;border-radius:12px 12px 0 0">
                    <h2 style="font-family:'Playfair Display',serif;margin:0;font-size:1.5rem">Bake'n Flake</h2>
                    <p style="margin:0.5rem 0 0;opacity:0.8;font-size:0.9rem">Order Receipt</p>
                </div>

                <div style="padding:2rem">
                    <!-- Order Details -->
                    <table style="width:100%;border-collapse:collapse;margin-bottom:1.5rem">
                        <tr>
                            <td style="padding:0.5rem 0;color:#888;font-size:0.9rem">Order Number</td>
                            <td style="padding:0.5rem 0;text-align:right;font-weight:600"><?= sanitize($orderNumber) ?></td>
                        </tr>
                        <tr>
                            <td style="padding:0.5rem 0;color:#888;font-size:0.9rem">Date</td>
                            <td style="padding:0.5rem 0;text-align:right"><?= formatDate($order['created_at']) ?></td>
                        </tr>
                        <tr>
                            <td style="padding:0.5rem 0;color:#888;font-size:0.9rem">Name</td>
                            <td style="padding:0.5rem 0;text-align:right"><?= sanitize($order['customer_name']) ?></td>
                        </tr>
                    </table>

                    <!-- Itemized List -->
                    <h4 style="margin:1rem 0 0.5rem;font-size:1rem">Items</h4>
                    <table style="width:100%;border-collapse:collapse">
                        <?php foreach ($orderItems as $item): ?>
                        <tr style="border-bottom:1px solid #eee">
                            <td style="padding:0.75rem 0">
                                <strong><?= sanitize($item['name']) ?></strong><br>
                                <span style="color:#888;font-size:0.85rem">Qty: <?= (int)$item['quantity'] ?></span>
                            </td>
                            <td style="padding:0.75rem 0;text-align:right"><?= formatPrice($item['unit_price'] * $item['quantity']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>

                    <!-- Totals -->
                    <table style="width:100%;border-collapse:collapse;margin-top:1rem">
                        <tr>
                            <td style="padding:0.4rem 0;color:#888">Subtotal</td>
                            <td style="padding:0.4rem 0;text-align:right"><?= formatPrice($order['subtotal'] ?? $order['total']) ?></td>
                        </tr>
                        <?php if (!empty($order['discount_amount']) && $order['discount_amount'] > 0): ?>
                        <tr>
                            <td style="padding:0.4rem 0;color:var(--color-gold)">Discount<?= !empty($order['discount_code']) ? ' (' . sanitize($order['discount_code']) . ')' : '' ?></td>
                            <td style="padding:0.4rem 0;text-align:right;color:var(--color-gold)">-<?= formatPrice($order['discount_amount']) ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td style="padding:0.4rem 0;color:#888">Tax</td>
                            <td style="padding:0.4rem 0;text-align:right"><?= formatPrice($order['tax'] ?? 0) ?></td>
                        </tr>
                        <tr>
                            <td style="padding:0.4rem 0;color:#888">Shipping</td>
                            <td style="padding:0.4rem 0;text-align:right"><?= formatPrice($order['shipping'] ?? 0) ?></td>
                        </tr>
                        <tr style="border-top:2px solid #333">
                            <td style="padding:0.75rem 0;font-weight:700;font-size:1.1rem">Total</td>
                            <td style="padding:0.75rem 0;text-align:right;font-weight:700;font-size:1.1rem"><?= formatPrice($order['total']) ?></td>
                        </tr>
                    </table>

                    <!-- Delivery Info -->
                    <?php if (!empty($order['delivery_date'])): ?>
                    <div style="margin-top:1.5rem;padding:1rem;background:#f9f6f1;border-radius:8px">
                        <h4 style="margin:0 0 0.5rem;font-size:0.95rem">Delivery</h4>
                        <p style="margin:0;font-size:0.9rem"><?= sanitize($order['delivery_date']) ?><?= !empty($order['delivery_slot']) ? ' &mdash; ' . sanitize($order['delivery_slot']) : '' ?></p>
                        <p style="margin:0.25rem 0 0;font-size:0.9rem"><?= sanitize($order['customer_address']) ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                <div style="padding:1rem 2rem 2rem;text-align:center">
                    <button type="button" class="btn btn-gold" onclick="document.getElementById('emailModalOverlay').style.display='none'">Close</button>
                </div>
            </div>
        </div>

        <!-- Close modal on overlay click -->
        <script>
        document.getElementById('emailModalOverlay')?.addEventListener('click', function(e) {
            if (e.target === this) this.style.display = 'none';
        });
        </script>
    </main>

    <script src="<?= SITE_URL ?>/js/main.js"></script>
</body>
</html>
<?php
$conn->close();
?>
