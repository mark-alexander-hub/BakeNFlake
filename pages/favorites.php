<?php
$pageTitle = 'My Favorites';
require_once __DIR__ . '/../includes/header.php';
requireLogin();

$conn = getDBConnection();
$userId = $_SESSION['user_id'];
$favorites = getUserFavorites($conn, $userId);
?>

<section class="favorites-page">
    <div class="container">
        <div class="menu-page-header animate-on-scroll">
            <h1>My Favorites</h1>
            <div class="section-divider"></div>
            <p class="section-subtitle">Products you love, all in one place</p>
        </div>

        <?php if (empty($favorites)): ?>
        <div class="empty-state animate-on-scroll">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="opacity:0.4;">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
            </svg>
            <h2>No favorites yet</h2>
            <p>Browse our menu and tap the heart icon to save your favorite treats!</p>
            <a href="<?= SITE_URL ?>/pages/menu.php" class="btn btn-gold">Browse Menu</a>
        </div>
        <?php else: ?>
        <div class="products-grid stagger-children">
            <?php foreach ($favorites as $p): ?>
            <div class="product-card" data-category="<?= sanitize($p['category']) ?>">
                <div class="product-card-img">
                    <?php if (!empty($p['is_seasonal'])): ?>
                        <span class="product-card-ribbon">Seasonal</span>
                    <?php endif; ?>
                    <a href="<?= SITE_URL ?>/pages/product.php?id=<?= $p['id'] ?>">
                        <img src="<?= SITE_URL ?>/img/<?= sanitize($p['image']) ?>" alt="<?= sanitize($p['name']) ?>" loading="lazy">
                    </a>
                    <div class="product-card-overlay">
                        <button class="btn btn-gold btn-sm btn-add-cart" data-product-id="<?= $p['id'] ?>">Add to Cart</button>
                    </div>
                </div>
                <div class="product-card-body">
                    <span class="product-card-category"><?= sanitize($p['category']) ?></span>
                    <h3><a href="<?= SITE_URL ?>/pages/product.php?id=<?= $p['id'] ?>"><?= sanitize($p['name']) ?></a></h3>
                    <?= renderStars(getAverageRating($conn, $p['id'])) ?>
                    <p class="description"><?= sanitize($p['description']) ?></p>
                </div>
                <div class="product-card-footer">
                    <span class="product-price"><?= formatPrice($p['price']) ?></span>
                    <button class="btn-favorite favorited" data-product-id="<?= $p['id'] ?>" title="Remove from Favorites">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                        </svg>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-favorite').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const card = this.closest('.product-card');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            fetch('<?= SITE_URL ?>/api/favorites.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({product_id: productId, csrf_token: csrfToken})
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && !data.favorited) {
                    card.remove();
                    if (document.querySelectorAll('.product-card').length === 0) {
                        window.location.reload();
                    }
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
