<?php

function sanitize($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function getProducts($conn, $category = null) {
    if ($category) {
        $stmt = $conn->prepare("SELECT * FROM products WHERE is_available = 1 AND category = ? ORDER BY id");
        $stmt->bind_param('s', $category);
    } else {
        $stmt = $conn->prepare("SELECT * FROM products WHERE is_available = 1 ORDER BY id");
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getProductById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND is_available = 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getFeaturedProducts($conn, $limit = 4) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE is_available = 1 ORDER BY RAND() LIMIT ?");
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// --- Cart (session-based) ---

function getCartItems() {
    return $_SESSION['cart'] ?? [];
}

function getCartCount() {
    $cart = getCartItems();
    $count = 0;
    foreach ($cart as $item) {
        $count += $item['quantity'];
    }
    return $count;
}

function addToCart($productId, $quantity = 1) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    $key = (string)$productId;
    if (isset($_SESSION['cart'][$key])) {
        $_SESSION['cart'][$key]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$key] = ['product_id' => $productId, 'quantity' => $quantity];
    }
}

function removeFromCart($productId) {
    $key = (string)$productId;
    unset($_SESSION['cart'][$key]);
}

function updateCartQuantity($productId, $quantity) {
    $key = (string)$productId;
    if ($quantity <= 0) {
        removeFromCart($productId);
    } else {
        if (isset($_SESSION['cart'][$key])) {
            $_SESSION['cart'][$key]['quantity'] = $quantity;
        }
    }
}

function getCartTotal($conn) {
    $cart = getCartItems();
    $subtotal = 0;
    $items = [];
    foreach ($cart as $item) {
        $product = getProductById($conn, $item['product_id']);
        if ($product) {
            $lineTotal = $product['price'] * $item['quantity'];
            $subtotal += $lineTotal;
            $items[] = [
                'product' => $product,
                'quantity' => $item['quantity'],
                'line_total' => $lineTotal
            ];
        }
    }
    $tax = round($subtotal * 0.05, 2);
    $shipping = $subtotal > 0 ? 5.00 : 0;
    $total = $subtotal + $tax + $shipping;
    return [
        'items' => $items,
        'subtotal' => $subtotal,
        'tax' => $tax,
        'shipping' => $shipping,
        'total' => $total
    ];
}

function formatPrice($amount) {
    return '$' . number_format($amount, 2);
}

// --- Product Extended ---

function getProductWithReviews($conn, $id) {
    $product = getProductById($conn, $id);
    if (!$product) return null;
    $product['avg_rating'] = getAverageRating($conn, $id);
    $product['review_count'] = getReviewCount($conn, $id);
    return $product;
}

function getRelatedProducts($conn, $productId, $limit = 4) {
    $product = getProductById($conn, $productId);
    if (!$product) return [];
    $stmt = $conn->prepare("SELECT * FROM products WHERE is_available = 1 AND category = ? AND id != ? ORDER BY RAND() LIMIT ?");
    $stmt->bind_param('sii', $product['category'], $productId, $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function searchProducts($conn, $query) {
    $like = '%' . $query . '%';
    $stmt = $conn->prepare("SELECT * FROM products WHERE is_available = 1 AND (name LIKE ? OR description LIKE ? OR category LIKE ?) ORDER BY name LIMIT 20");
    $stmt->bind_param('sss', $like, $like, $like);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getSeasonalProducts($conn) {
    $today = date('Y-m-d');
    $stmt = $conn->prepare("SELECT * FROM products WHERE is_available = 1 AND is_seasonal = 1 AND seasonal_start <= ? AND seasonal_end >= ? ORDER BY id");
    $stmt->bind_param('ss', $today, $today);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getAverageRating($conn, $productId) {
    $stmt = $conn->prepare("SELECT AVG(rating) as avg_rating FROM reviews WHERE product_id = ?");
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return $row['avg_rating'] ? round($row['avg_rating'], 1) : 0;
}

function getReviewCount($conn, $productId) {
    $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM reviews WHERE product_id = ?");
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['cnt'];
}

// --- Reviews ---

function getProductReviews($conn, $productId) {
    $stmt = $conn->prepare("SELECT r.*, u.name as user_name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = ? ORDER BY r.created_at DESC");
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function submitReview($conn, $productId, $userId, $rating, $text) {
    $stmt = $conn->prepare("INSERT INTO reviews (product_id, user_id, rating, review_text) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE rating = VALUES(rating), review_text = VALUES(review_text)");
    $stmt->bind_param('iiis', $productId, $userId, $rating, $text);
    return $stmt->execute();
}

function userHasReviewed($conn, $productId, $userId) {
    $stmt = $conn->prepare("SELECT id FROM reviews WHERE product_id = ? AND user_id = ?");
    $stmt->bind_param('ii', $productId, $userId);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

// --- Favorites ---

function toggleFavorite($conn, $userId, $productId) {
    if (isFavorited($conn, $userId, $productId)) {
        $stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param('ii', $userId, $productId);
        $stmt->execute();
        return false;
    } else {
        $stmt = $conn->prepare("INSERT INTO favorites (user_id, product_id) VALUES (?, ?)");
        $stmt->bind_param('ii', $userId, $productId);
        $stmt->execute();
        return true;
    }
}

function isFavorited($conn, $userId, $productId) {
    $stmt = $conn->prepare("SELECT id FROM favorites WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param('ii', $userId, $productId);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

function getUserFavorites($conn, $userId) {
    $stmt = $conn->prepare("SELECT p.* FROM favorites f JOIN products p ON f.product_id = p.id WHERE f.user_id = ? AND p.is_available = 1 ORDER BY f.created_at DESC");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getUserFavoriteIds($conn, $userId) {
    $stmt = $conn->prepare("SELECT product_id FROM favorites WHERE user_id = ?");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $ids = [];
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $ids[] = $row['product_id'];
    }
    return $ids;
}

// --- Discount Codes ---

function validateDiscountCode($conn, $code) {
    $stmt = $conn->prepare("SELECT * FROM discount_codes WHERE code = ? AND is_active = 1 AND (expires_at IS NULL OR expires_at > NOW()) AND (max_uses IS NULL OR times_used < max_uses)");
    $stmt->bind_param('s', $code);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function applyDiscount($subtotal, $discount) {
    if ($subtotal < $discount['min_order']) return 0;
    if ($discount['type'] === 'percentage') {
        return round($subtotal * ($discount['value'] / 100), 2);
    }
    return min($discount['value'], $subtotal);
}

function incrementDiscountUse($conn, $codeId) {
    $stmt = $conn->prepare("UPDATE discount_codes SET times_used = times_used + 1 WHERE id = ?");
    $stmt->bind_param('i', $codeId);
    $stmt->execute();
}

// --- Loyalty Points ---

function getUserPoints($conn, $userId) {
    $stmt = $conn->prepare("SELECT loyalty_points FROM users WHERE id = ?");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return $row ? $row['loyalty_points'] : 0;
}

function earnPoints($conn, $userId, $orderId, $amount) {
    $points = (int)floor($amount);
    if ($points <= 0) return 0;
    $stmt = $conn->prepare("UPDATE users SET loyalty_points = loyalty_points + ? WHERE id = ?");
    $stmt->bind_param('ii', $points, $userId);
    $stmt->execute();
    $stmt = $conn->prepare("INSERT INTO loyalty_ledger (user_id, order_id, points, type, description) VALUES (?, ?, ?, 'earned', ?)");
    $desc = "Earned from order #" . $orderId;
    $stmt->bind_param('iiis', $userId, $orderId, $points, $desc);
    $stmt->execute();
    $_SESSION['user_loyalty_points'] = getUserPoints($conn, $userId);
    return $points;
}

function redeemPoints($conn, $userId, $points) {
    $current = getUserPoints($conn, $userId);
    if ($points > $current || $points <= 0) return 0;
    $dollarValue = round($points / 100, 2);
    $negPoints = -$points;
    $stmt = $conn->prepare("UPDATE users SET loyalty_points = loyalty_points - ? WHERE id = ?");
    $stmt->bind_param('ii', $points, $userId);
    $stmt->execute();
    $stmt = $conn->prepare("INSERT INTO loyalty_ledger (user_id, points, type, description) VALUES (?, ?, 'redeemed', 'Redeemed at checkout')");
    $stmt->bind_param('ii', $userId, $negPoints);
    $stmt->execute();
    $_SESSION['user_loyalty_points'] = getUserPoints($conn, $userId);
    return $dollarValue;
}

// --- Orders ---

function getUserOrders($conn, $userId) {
    $stmt = $conn->prepare("SELECT o.*, (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count FROM orders o WHERE o.user_id = ? ORDER BY o.created_at DESC");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getOrderDetails($conn, $orderId) {
    $stmt = $conn->prepare("SELECT oi.*, p.name, p.image, p.category FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
    $stmt->bind_param('i', $orderId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function generateOrderNumber($orderId) {
    return 'BNF-' . str_pad($orderId, 6, '0', STR_PAD_LEFT);
}

// --- Delivery Slots ---

function getAvailableSlots($conn, $date) {
    generateSlotsForDate($conn, $date);
    $stmt = $conn->prepare("SELECT * FROM delivery_slots WHERE slot_date = ? AND current_orders < max_orders ORDER BY slot_time");
    $stmt->bind_param('s', $date);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function generateSlotsForDate($conn, $date) {
    $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM delivery_slots WHERE slot_date = ?");
    $stmt->bind_param('s', $date);
    $stmt->execute();
    if ($stmt->get_result()->fetch_assoc()['cnt'] > 0) return;
    $slots = ['9:00 AM - 11:00 AM', '11:00 AM - 1:00 PM', '1:00 PM - 3:00 PM', '3:00 PM - 5:00 PM', '5:00 PM - 7:00 PM'];
    $stmt = $conn->prepare("INSERT INTO delivery_slots (slot_date, slot_time) VALUES (?, ?)");
    foreach ($slots as $slot) {
        $stmt->bind_param('ss', $date, $slot);
        $stmt->execute();
    }
}

function bookDeliverySlot($conn, $date, $time) {
    $stmt = $conn->prepare("UPDATE delivery_slots SET current_orders = current_orders + 1 WHERE slot_date = ? AND slot_time = ? AND current_orders < max_orders");
    $stmt->bind_param('ss', $date, $time);
    $stmt->execute();
    return $stmt->affected_rows > 0;
}

// --- Newsletter ---

function subscribeNewsletter($conn, $email) {
    $stmt = $conn->prepare("INSERT IGNORE INTO newsletter_subscribers (email) VALUES (?)");
    $stmt->bind_param('s', $email);
    return $stmt->execute();
}

// --- Custom Cake ---

function submitCustomOrder($conn, $userId, $data) {
    $stmt = $conn->prepare("INSERT INTO custom_orders (user_id, occasion, flavor_base, filling, frosting, size, tiers, cake_message, reference_image, date_needed, special_notes, estimated_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('isssssissssd',
        $userId, $data['occasion'], $data['flavor_base'], $data['filling'],
        $data['frosting'], $data['size'], $data['tiers'], $data['cake_message'],
        $data['reference_image'], $data['date_needed'], $data['special_notes'], $data['estimated_price']
    );
    $stmt->execute();
    return $conn->insert_id;
}

function getUserCustomOrders($conn, $userId) {
    $stmt = $conn->prepare("SELECT * FROM custom_orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// --- Blog ---

function getBlogPosts($conn, $limit = 10, $offset = 0) {
    $stmt = $conn->prepare("SELECT * FROM blog_posts WHERE is_published = 1 ORDER BY published_at DESC LIMIT ? OFFSET ?");
    $stmt->bind_param('ii', $limit, $offset);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getBlogPostBySlug($conn, $slug) {
    $stmt = $conn->prepare("SELECT * FROM blog_posts WHERE slug = ? AND is_published = 1");
    $stmt->bind_param('s', $slug);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// --- Utility ---

function formatDate($datetime) {
    return date('M j, Y', strtotime($datetime));
}

function renderStars($rating) {
    $html = '<div class="star-rating" title="' . $rating . ' out of 5">';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= floor($rating)) {
            $html .= '<span class="star star-filled">&#9733;</span>';
        } elseif ($i - 0.5 <= $rating) {
            $html .= '<span class="star star-half">&#9733;</span>';
        } else {
            $html .= '<span class="star star-empty">&#9733;</span>';
        }
    }
    $html .= '</div>';
    return $html;
}
