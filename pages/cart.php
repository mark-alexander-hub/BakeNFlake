<?php
$pageTitle = 'Your Cart';
require_once __DIR__ . '/../includes/header.php';
$conn = getDBConnection();
$cartData = getCartTotal($conn);
$items = $cartData['items'];
$isEmpty = empty($items);
?>

<section class="cart-page">
    <div class="container">
        <div class="cart-page-header">
            <h1>Your Cart</h1>
            <div class="section-divider"></div>
        </div>

        <?php if ($isEmpty): ?>
        <div class="cart-empty animate-fade-in">
            <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
            </svg>
            <h2>Your cart is empty</h2>
            <p>Looks like you haven't added any treats yet.</p>
            <a href="<?= SITE_URL ?>/pages/menu.php" class="btn btn-gold">Browse Menu</a>
        </div>
        <?php else: ?>
        <div class="cart-layout">
            <div class="cart-items">
                <?php foreach ($items as $item):
                    $p = $item['product'];
                ?>
                <div class="cart-item" data-product-id="<?= $p['id'] ?>">
                    <div class="cart-item-img">
                        <img src="<?= SITE_URL ?>/img/<?= sanitize($p['image']) ?>" alt="<?= sanitize($p['name']) ?>">
                    </div>
                    <div class="cart-item-name"><?= sanitize($p['name']) ?></div>
                    <div class="cart-item-price"><?= formatPrice($p['price']) ?></div>
                    <div class="qty-controls">
                        <button class="qty-btn" data-product-id="<?= $p['id'] ?>" data-action="minus">-</button>
                        <span class="qty-value"><?= $item['quantity'] ?></span>
                        <button class="qty-btn" data-product-id="<?= $p['id'] ?>" data-action="plus">+</button>
                    </div>
                    <button class="cart-item-remove" data-product-id="<?= $p['id'] ?>" title="Remove">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                        </svg>
                    </button>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary">
                <h3>Order Summary</h3>
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span id="cartSubtotal"><?= formatPrice($cartData['subtotal']) ?></span>
                </div>
                <div class="summary-row">
                    <span>Tax (5%)</span>
                    <span id="cartTax"><?= formatPrice($cartData['tax']) ?></span>
                </div>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span id="cartShipping"><?= formatPrice($cartData['shipping']) ?></span>
                </div>
                <div class="summary-row total">
                    <span>Total</span>
                    <span id="cartTotal"><?= formatPrice($cartData['total']) ?></span>
                </div>
                <a href="<?= SITE_URL ?>/pages/checkout.php" class="btn btn-gold btn-full">Proceed to Checkout</a>
                <a href="<?= SITE_URL ?>/pages/menu.php" class="cart-continue">Continue Shopping</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php
$conn->close();
require_once __DIR__ . '/../includes/footer.php';
?>
