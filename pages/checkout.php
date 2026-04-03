<?php
$pageTitle = 'Checkout';
require_once __DIR__ . '/../includes/header.php';
requireLogin();

$conn = getDBConnection();

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid request.');
        header('Location: ' . SITE_URL . '/pages/checkout.php');
        exit;
    }

    $cartData = getCartTotal($conn);
    if (empty($cartData['items'])) {
        setFlash('error', 'Your cart is empty.');
        header('Location: ' . SITE_URL . '/pages/menu.php');
        exit;
    }

    $name = trim($_POST['name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    if (empty($name) || empty($address) || empty($phone)) {
        setFlash('error', 'Please fill in all required fields.');
        header('Location: ' . SITE_URL . '/pages/checkout.php');
        exit;
    }

    // Discount handling
    $discountCode = trim($_POST['discount_code'] ?? '');
    $discountAmount = 0;
    $discountRow = null;
    if (!empty($discountCode)) {
        $discountRow = validateDiscountCode($conn, $discountCode);
        if ($discountRow) {
            $discountAmount = applyDiscount($cartData['subtotal'], $discountRow);
        }
    }

    // Delivery scheduling
    $deliveryDate = trim($_POST['delivery_date'] ?? '');
    $deliverySlot = trim($_POST['delivery_slot'] ?? '');

    // Loyalty points
    $pointsRedeemed = (int)($_POST['points_redeemed'] ?? 0);
    $pointsDiscount = 0;
    if ($pointsRedeemed > 0) {
        $pointsDiscount = redeemPoints($conn, $_SESSION['user_id'], $pointsRedeemed);
    }

    // Calculate totals
    $subtotal = $cartData['subtotal'];
    $tax = $cartData['tax'];
    $shipping = $cartData['shipping'];
    $total = $subtotal - $discountAmount - $pointsDiscount + $tax + $shipping;
    if ($total < 0) $total = 0;

    // Points earned (1 point per dollar spent)
    $pointsEarned = (int)floor($total);

    // Create order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, customer_name, customer_address, customer_phone, notes, subtotal, tax, shipping, discount_code, discount_amount, delivery_date, delivery_slot, points_earned, points_redeemed, total) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('issssdddsdssiid',
        $_SESSION['user_id'], $name, $address, $phone, $notes,
        $subtotal, $tax, $shipping,
        $discountCode, $discountAmount,
        $deliveryDate, $deliverySlot,
        $pointsEarned, $pointsRedeemed,
        $total
    );
    $stmt->execute();
    $orderId = $conn->insert_id;

    // Create order items
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
    foreach ($cartData['items'] as $item) {
        $stmt->bind_param('iiid', $orderId, $item['product']['id'], $item['quantity'], $item['product']['price']);
        $stmt->execute();
    }

    // Earn loyalty points
    earnPoints($conn, $_SESSION['user_id'], $orderId, $total);

    // Book delivery slot if provided
    if (!empty($deliveryDate) && !empty($deliverySlot)) {
        bookDeliverySlot($conn, $deliveryDate, $deliverySlot);
    }

    // Increment discount usage if applied
    if ($discountRow) {
        incrementDiscountUse($conn, $discountRow['id']);
    }

    // Clear cart and discount session
    $_SESSION['cart'] = [];
    unset($_SESSION['applied_discount']);

    $conn->close();
    header('Location: ' . SITE_URL . '/pages/order-confirmation.php?id=' . $orderId);
    exit;
}

$cartData = getCartTotal($conn);
if (empty($cartData['items'])) {
    header('Location: ' . SITE_URL . '/pages/cart.php');
    exit;
}
$user = getCurrentUser();
$userPoints = getUserPoints($conn, $_SESSION['user_id']);

$tomorrow = date('Y-m-d', strtotime('+1 day'));
$maxDate = date('Y-m-d', strtotime('+14 days'));
?>

<section class="checkout-page">
    <div class="container">
        <div class="checkout-page-header">
            <h1>Checkout</h1>
            <div class="section-divider"></div>
        </div>

        <div class="checkout-layout">
            <form class="checkout-form" id="checkoutForm" method="POST">
                <input type="hidden" name="csrf_token" value="<?= sanitize($csrfToken) ?>">
                <input type="hidden" name="place_order" value="1">
                <input type="hidden" name="discount_code" id="discountCodeHidden" value="">
                <input type="hidden" name="delivery_date" id="deliveryDateHidden" value="">
                <input type="hidden" name="delivery_slot" id="deliverySlotHidden" value="">
                <input type="hidden" name="points_redeemed" id="pointsRedeemedHidden" value="0">

                <!-- Delivery Details -->
                <div class="checkout-section">
                    <h3>Delivery Details</h3>
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" value="<?= sanitize($user['name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Delivery Address</label>
                        <textarea id="address" name="address" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes (optional)</label>
                        <textarea id="notes" name="notes"></textarea>
                    </div>
                </div>

                <!-- Discount Code -->
                <div class="checkout-section">
                    <h3>Discount Code</h3>
                    <div class="discount-row">
                        <input type="text" id="discountInput" placeholder="Enter code" autocomplete="off">
                        <button type="button" id="applyDiscountBtn" class="btn btn-outline">Apply</button>
                    </div>
                </div>

                <!-- Delivery Scheduling -->
                <div class="checkout-section delivery-section">
                    <h3>Schedule Delivery</h3>
                    <div class="form-group">
                        <label for="deliveryDate">Delivery Date</label>
                        <input type="date" id="deliveryDate" min="<?= $tomorrow ?>" max="<?= $maxDate ?>">
                    </div>
                    <div id="deliverySlotsGrid" class="delivery-slots-grid"></div>
                </div>

                <!-- Loyalty Points -->
                <?php if ($userPoints > 0): ?>
                <div class="checkout-section loyalty-section">
                    <h3>Loyalty Points</h3>
                    <p>You have <strong><?= (int)$userPoints ?> points</strong> available (<?= formatPrice($userPoints / 100) ?> value).</p>
                    <div class="form-group">
                        <label for="pointsRedeem">Points to Redeem (100 pts = $1.00)</label>
                        <input type="number" id="pointsRedeem" min="0" max="<?= (int)$userPoints ?>" step="1" value="0" placeholder="0">
                    </div>
                </div>
                <?php endif; ?>

                <!-- Payment -->
                <div class="checkout-section payment-section">
                    <h3>Payment</h3>
                    <div class="card-input-group">
                        <div class="card-number-row">
                            <span class="card-brand-icon">&#128179;</span>
                            <input type="text" id="cardNumber" placeholder="Card number" maxlength="19" autocomplete="cc-number" required>
                        </div>
                        <div class="card-extra-row">
                            <input type="text" id="cardExpiry" placeholder="MM/YY" maxlength="5" autocomplete="cc-exp" required>
                            <input type="text" id="cardCvc" placeholder="CVC" maxlength="4" autocomplete="cc-csc" required>
                        </div>
                    </div>
                </div>

                <!-- Pay Button -->
                <button type="button" id="payBtn" class="btn btn-pay btn-full">
                    <span class="spinner" style="display:none"></span>
                    <span class="pay-text">Pay <?= formatPrice($cartData['total']) ?></span>
                </button>
            </form>

            <!-- Order Summary Sidebar -->
            <div class="checkout-order-summary">
                <h3>Order Summary</h3>
                <?php foreach ($cartData['items'] as $item): ?>
                <div class="checkout-item">
                    <span>
                        <span class="checkout-item-name"><?= sanitize($item['product']['name']) ?></span>
                        <span class="checkout-item-qty"> x<?= $item['quantity'] ?></span>
                    </span>
                    <span><?= formatPrice($item['line_total']) ?></span>
                </div>
                <?php endforeach; ?>
                <div class="summary-row" style="margin-top:1rem; padding-top:1rem; border-top:1px solid var(--color-cream-dark)">
                    <span>Subtotal</span><span id="cartSubtotal"><?= formatPrice($cartData['subtotal']) ?></span>
                </div>
                <div class="summary-row" id="discountRow" style="display:none; color:var(--color-gold)">
                    <span>Discount</span><span>-$0.00</span>
                </div>
                <div class="summary-row"><span>Tax (5%)</span><span id="cartTax"><?= formatPrice($cartData['tax']) ?></span></div>
                <div class="summary-row"><span>Shipping</span><span id="cartShipping"><?= formatPrice($cartData['shipping']) ?></span></div>
                <div class="summary-row total"><span>Total</span><span id="cartTotal"><?= formatPrice($cartData['total']) ?></span></div>
            </div>
        </div>
    </div>
</section>

<script>
// Sync delivery date input to hidden field
document.getElementById('deliveryDate')?.addEventListener('change', function() {
    document.getElementById('deliveryDateHidden').value = this.value;
});
// Sync points input to hidden field
document.getElementById('pointsRedeem')?.addEventListener('input', function() {
    document.getElementById('pointsRedeemedHidden').value = this.value;
});
</script>

<?php
$conn->close();
require_once __DIR__ . '/../includes/footer.php';
?>
