<?php
$pageTitle = 'Our Menu';
require_once __DIR__ . '/../includes/header.php';
$conn = getDBConnection();
$products = getProducts($conn);
$favIds = [];
if (isLoggedIn()) {
    $favIds = getUserFavoriteIds($conn, $_SESSION['user_id']);
}
?>

<section class="menu-page">
    <div class="container">
        <div class="menu-page-header animate-on-scroll">
            <h1>Our Menu</h1>
            <div class="section-divider"></div>
            <p class="section-subtitle">Handcrafted pastries, baked fresh every day</p>
        </div>

        <div class="category-filters animate-on-scroll">
            <button class="filter-pill active" data-category="all">All</button>
            <button class="filter-pill" data-category="cakes">Cakes</button>
            <button class="filter-pill" data-category="pastries">Pastries</button>
            <button class="filter-pill" data-category="cookies">Cookies</button>
            <button class="filter-pill" data-category="donuts">Donuts</button>
            <button class="filter-pill" data-category="bread">Bread</button>
            <button class="filter-pill" data-category="drinks">Drinks</button>
        </div>

        <div class="products-grid stagger-children">
            <?php foreach ($products as $p):
                $isFav = in_array($p['id'], $favIds);
            ?>
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
                    <button class="btn-favorite <?= $isFav ? 'favorited' : '' ?>" data-product-id="<?= $p['id'] ?>" title="Toggle Favorite">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="<?= $isFav ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                        </svg>
                    </button>
                    <button class="btn-add-cart" data-product-id="<?= $p['id'] ?>">Add to Cart</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-favorite').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const favBtn = this;
            fetch('<?= SITE_URL ?>/api/favorites.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({product_id: productId, csrf_token: csrfToken})
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    favBtn.classList.toggle('favorited', data.favorited);
                    const svg = favBtn.querySelector('svg');
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
