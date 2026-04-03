<?php
$q = trim($_GET['q'] ?? '');
$pageTitle = $q ? 'Search: ' . $q : 'Search';
require_once __DIR__ . '/../includes/header.php';

$conn = getDBConnection();
$results = [];
if (!empty($q)) {
    $results = searchProducts($conn, $q);
}
?>

<section class="search-page">
    <div class="container">
        <div class="menu-page-header animate-on-scroll">
            <h1>Search Results</h1>
            <div class="section-divider"></div>
            <?php if (!empty($q)): ?>
                <p class="section-subtitle"><?= count($results) ?> result<?= count($results) !== 1 ? 's' : '' ?> for "<?= sanitize($q) ?>"</p>
            <?php else: ?>
                <p class="section-subtitle">Enter a search term to find products.</p>
            <?php endif; ?>
        </div>

        <?php if (!empty($q) && empty($results)): ?>
        <div class="empty-state animate-on-scroll">
            <h2>No products found</h2>
            <p>Try a different search term or browse our full menu.</p>
            <a href="<?= SITE_URL ?>/pages/menu.php" class="btn btn-gold">Browse Menu</a>
        </div>
        <?php elseif (!empty($results)): ?>
        <div class="products-grid stagger-children">
            <?php foreach ($results as $p): ?>
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
                    <button class="btn-add-cart" data-product-id="<?= $p['id'] ?>">Add to Cart</button>
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
