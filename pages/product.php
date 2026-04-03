<?php
$pageTitle = 'Product Details';
require_once __DIR__ . '/../includes/header.php';
$conn = getDBConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = getProductWithReviews($conn, $id);

if (!$product) {
    echo '<section class="container" style="padding:4rem 0;text-align:center;">';
    echo '<h2>Product Not Found</h2>';
    echo '<p>The product you are looking for does not exist.</p>';
    echo '<a href="' . SITE_URL . '/pages/menu.php" class="btn btn-gold">Back to Menu</a>';
    echo '</section>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$reviews = getProductReviews($conn, $id);
$related = getRelatedProducts($conn, $id);
$isFav = false;
if (isLoggedIn()) {
    $isFav = isFavorited($conn, $_SESSION['user_id'], $id);
}

$pageTitle = $product['name'];
?>

<section class="product-detail-section">
    <div class="container">
        <div class="product-detail-layout">
            <!-- Left Column: Image -->
            <div class="product-detail-image">
                <?php if (!empty($product['is_seasonal'])): ?>
                    <span class="product-card-ribbon">Seasonal</span>
                <?php endif; ?>
                <img src="<?= SITE_URL ?>/img/<?= sanitize($product['image']) ?>" alt="<?= sanitize($product['name']) ?>">
            </div>

            <!-- Right Column: Info -->
            <div class="product-detail-info">
                <span class="product-card-category"><?= sanitize($product['category']) ?></span>
                <h1><?= sanitize($product['name']) ?></h1>

                <div class="product-detail-rating">
                    <?= renderStars($product['avg_rating']) ?>
                    <span>(<?= (int)$product['review_count'] ?> review<?= $product['review_count'] != 1 ? 's' : '' ?>)</span>
                </div>

                <div class="product-detail-price"><?= formatPrice($product['price']) ?></div>

                <p class="product-detail-description"><?= sanitize($product['description']) ?></p>

                <?php if (!empty($product['allergens'])): ?>
                <div class="product-detail-allergens">
                    <strong>Allergens:</strong>
                    <?php foreach (explode(',', $product['allergens']) as $allergen): ?>
                        <span class="allergen-pill"><?= sanitize(trim($allergen)) ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($product['nutrition_tags'])): ?>
                <div class="product-detail-nutrition-tags">
                    <?php foreach (explode(',', $product['nutrition_tags']) as $tag): ?>
                        <span class="nutrition-tag"><?= sanitize(trim($tag)) ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="product-detail-actions">
                    <div class="quantity-selector">
                        <button class="qty-btn qty-minus" type="button">-</button>
                        <input type="number" class="qty-input" id="productQty" value="1" min="1" max="20">
                        <button class="qty-btn qty-plus" type="button">+</button>
                    </div>
                    <button class="btn btn-gold btn-add-cart" data-product-id="<?= $product['id'] ?>" id="addToCartBtn">Add to Cart</button>
                    <button class="btn-favorite <?= $isFav ? 'favorited' : '' ?>" data-product-id="<?= $product['id'] ?>" title="Toggle Favorite">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="<?= $isFav ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="product-tabs">
            <div class="product-tabs-nav">
                <button class="product-tab-btn active" data-tab="description">Description</button>
                <button class="product-tab-btn" data-tab="reviews">Reviews</button>
                <button class="product-tab-btn" data-tab="nutrition">Nutrition</button>
            </div>

            <div class="product-tab-panel active" id="tab-description">
                <p><?= nl2br(sanitize($product['description'])) ?></p>
            </div>

            <div class="product-tab-panel" id="tab-reviews">
                <div class="reviews-list">
                    <?php if (empty($reviews)): ?>
                        <p class="no-reviews">No reviews yet. Be the first to review this product!</p>
                    <?php else: ?>
                        <?php foreach ($reviews as $review): ?>
                        <div class="review-item">
                            <div class="review-header">
                                <strong><?= sanitize($review['user_name']) ?></strong>
                                <?= renderStars($review['rating']) ?>
                                <span class="review-date"><?= formatDate($review['created_at']) ?></span>
                            </div>
                            <p class="review-text"><?= nl2br(sanitize($review['review_text'])) ?></p>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <?php if (isLoggedIn()): ?>
                <div class="review-form-wrapper">
                    <h3>Write a Review</h3>
                    <form class="review-form" id="reviewForm" method="POST" action="<?= SITE_URL ?>/api/reviews.php">
                        <input type="hidden" name="csrf_token" value="<?= sanitize($csrfToken) ?>">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <input type="hidden" name="rating" id="ratingInput" value="5">
                        <div class="star-selector">
                            <span class="star-select" data-value="1">&#9733;</span>
                            <span class="star-select" data-value="2">&#9733;</span>
                            <span class="star-select" data-value="3">&#9733;</span>
                            <span class="star-select" data-value="4">&#9733;</span>
                            <span class="star-select" data-value="5">&#9733;</span>
                        </div>
                        <textarea name="review_text" rows="4" placeholder="Share your experience..." required></textarea>
                        <button type="submit" class="btn btn-gold">Submit Review</button>
                    </form>
                </div>
                <?php else: ?>
                <p class="login-to-review"><a href="<?= SITE_URL ?>/pages/login.php">Log in</a> to leave a review.</p>
                <?php endif; ?>
            </div>

            <div class="product-tab-panel" id="tab-nutrition">
                <?php if (!empty($product['nutrition_tags'])): ?>
                <div class="nutrition-info">
                    <?php foreach (explode(',', $product['nutrition_tags']) as $tag): ?>
                        <span class="nutrition-tag"><?= sanitize(trim($tag)) ?></span>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p>No nutrition information available for this product.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Related Products -->
        <?php if (!empty($related)): ?>
        <div class="related-products">
            <h2>You May Also Like</h2>
            <div class="products-grid">
                <?php foreach ($related as $rp): ?>
                <div class="product-card" data-category="<?= sanitize($rp['category']) ?>">
                    <div class="product-card-img">
                        <?php if (!empty($rp['is_seasonal'])): ?>
                            <span class="product-card-ribbon">Seasonal</span>
                        <?php endif; ?>
                        <a href="<?= SITE_URL ?>/pages/product.php?id=<?= $rp['id'] ?>">
                            <img src="<?= SITE_URL ?>/img/<?= sanitize($rp['image']) ?>" alt="<?= sanitize($rp['name']) ?>" loading="lazy">
                        </a>
                        <div class="product-card-overlay">
                            <button class="btn btn-gold btn-sm btn-add-cart" data-product-id="<?= $rp['id'] ?>">Add to Cart</button>
                        </div>
                    </div>
                    <div class="product-card-body">
                        <span class="product-card-category"><?= sanitize($rp['category']) ?></span>
                        <h3><a href="<?= SITE_URL ?>/pages/product.php?id=<?= $rp['id'] ?>"><?= sanitize($rp['name']) ?></a></h3>
                        <?= renderStars(getAverageRating($conn, $rp['id'])) ?>
                    </div>
                    <div class="product-card-footer">
                        <span class="product-price"><?= formatPrice($rp['price']) ?></span>
                        <button class="btn-add-cart" data-product-id="<?= $rp['id'] ?>">Add to Cart</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quantity selector
    const qtyInput = document.getElementById('productQty');
    document.querySelector('.qty-minus')?.addEventListener('click', function() {
        let val = parseInt(qtyInput.value) || 1;
        if (val > 1) qtyInput.value = val - 1;
    });
    document.querySelector('.qty-plus')?.addEventListener('click', function() {
        let val = parseInt(qtyInput.value) || 1;
        if (val < 20) qtyInput.value = val + 1;
    });

    // Tabs
    document.querySelectorAll('.product-tab-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.product-tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.product-tab-panel').forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById('tab-' + btn.dataset.tab).classList.add('active');
        });
    });

    // Star selector for review form
    const starSelects = document.querySelectorAll('.star-select');
    const ratingInput = document.getElementById('ratingInput');
    starSelects.forEach(function(star) {
        star.addEventListener('click', function() {
            const val = parseInt(this.dataset.value);
            ratingInput.value = val;
            starSelects.forEach(function(s, i) {
                s.classList.toggle('selected', i < val);
            });
        });
    });
    // Initialize stars
    starSelects.forEach(function(s, i) { s.classList.add('selected'); });

    // Review form AJAX submit
    const reviewForm = document.getElementById('reviewForm');
    if (reviewForm) {
        reviewForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(reviewForm);
            fetch(reviewForm.action, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Error submitting review.');
                }
            })
            .catch(() => alert('Error submitting review.'));
        });
    }

    // Favorite toggle
    document.querySelectorAll('.btn-favorite').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            fetch('<?= SITE_URL ?>/api/favorites.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({product_id: productId, csrf_token: csrfToken})
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    btn.classList.toggle('favorited', data.favorited);
                    const svg = btn.querySelector('svg');
                    svg.setAttribute('fill', data.favorited ? 'currentColor' : 'none');
                } else {
                    alert(data.message || 'Please log in to favorite products.');
                }
            });
        });
    });
});
</script>

<?php
$conn->close();
require_once __DIR__ . '/../includes/footer.php';
?>
