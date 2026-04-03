<?php
$pageTitle = 'Home';
require_once __DIR__ . '/../includes/header.php';
$conn = getDBConnection();
$featured = getFeaturedProducts($conn, 4);
$seasonal = getSeasonalProducts($conn);
$blogPosts = getBlogPosts($conn, 3);
?>

<!-- Hero -->
<section class="hero">
    <div class="hero-bg" style="background-image: url('<?= SITE_URL ?>/img/emma-valerio-1060531-unsplash.jpg')"></div>
    <div class="hero-content">
        <h1>Bake'n Flake</h1>
        <p class="hero-tagline">Handcrafted with love, baked to perfection</p>
        <a href="<?= SITE_URL ?>/pages/menu.php" class="btn btn-gold">Explore Our Menu</a>
    </div>
    <div class="hero-scroll">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
            <path d="M12 5v14M5 12l7 7 7-7"/>
        </svg>
    </div>
</section>

<!-- Featured Products -->
<section class="featured-section">
    <div class="container">
        <h2 class="section-title animate-on-scroll">Fresh From the Oven</h2>
        <div class="section-divider"></div>
        <p class="section-subtitle animate-on-scroll">Our most loved creations, baked daily</p>
        <div class="featured-grid stagger-children">
            <?php foreach ($featured as $p): ?>
            <div class="product-card">
                <div class="product-card-img">
                    <img src="<?= SITE_URL ?>/img/<?= sanitize($p['image']) ?>" alt="<?= sanitize($p['name']) ?>" loading="lazy">
                    <div class="product-card-overlay">
                        <button class="btn btn-gold btn-sm btn-add-cart" data-product-id="<?= $p['id'] ?>">Add to Cart</button>
                    </div>
                </div>
                <div class="product-card-body">
                    <span class="product-card-category"><?= sanitize($p['category']) ?></span>
                    <h3><?= sanitize($p['name']) ?></h3>
                    <p class="description"><?= sanitize($p['description']) ?></p>
                </div>
                <div class="product-card-footer">
                    <span class="product-price"><?= formatPrice($p['price']) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align:center; margin-top:2.5rem;" class="animate-on-scroll">
            <a href="<?= SITE_URL ?>/pages/menu.php" class="btn btn-outline">View Full Menu</a>
        </div>
    </div>
</section>

<!-- About Teaser -->
<section class="about-teaser">
    <div class="container">
        <div class="about-teaser-grid">
            <div class="about-teaser-img animate-on-scroll">
                <img src="<?= SITE_URL ?>/img/f.jpg" alt="Our bakery">
            </div>
            <div class="about-teaser-text animate-on-scroll">
                <h2>Our Story</h2>
                <div class="section-divider" style="margin-left:0"></div>
                <p>At Bake'n Flake, every recipe tells a story. We blend time-honored techniques with bold, creative flavors to craft pastries that are as beautiful as they are delicious.</p>
                <p>From our signature naked cakes to artisan breads, each creation is made with the finest ingredients and a generous measure of love.</p>
                <a href="<?= SITE_URL ?>/pages/about.php" class="btn btn-outline">Read More</a>
            </div>
        </div>
    </div>
</section>

<!-- Gallery Carousel -->
<section class="carousel-section">
    <div class="container">
        <div class="carousel-title animate-on-scroll">
            <h2>Gallery</h2>
            <div class="section-divider"></div>
        </div>
        <div class="carousel-wrapper">
            <?php for ($i = 1; $i <= 9; $i++): ?>
            <div class="carousel-slide <?= $i === 1 ? 'active' : '' ?>">
                <img src="<?= SITE_URL ?>/img/f<?= $i ?>.jpg" alt="Gallery image <?= $i ?>" loading="lazy">
            </div>
            <?php endfor; ?>
            <div class="carousel-controls">
                <?php for ($i = 0; $i < 9; $i++): ?>
                <button class="carousel-dot <?= $i === 0 ? 'active' : '' ?>" aria-label="Slide <?= $i + 1 ?>"></button>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</section>

<!-- Menu Preview -->
<section class="menu-preview">
    <div class="container">
        <h2 class="section-title animate-on-scroll" style="color:var(--color-cream)">Our Menu</h2>
        <div class="section-divider"></div>
        <p class="section-subtitle" style="color:var(--color-cream-dark)">A taste of what we offer</p>

        <div class="menu-tabs animate-on-scroll">
            <button class="menu-tab-btn active" data-tab="eatPanel">Eat</button>
            <button class="menu-tab-btn" data-tab="drinkPanel">Drink</button>
        </div>

        <div id="eatPanel" class="menu-panel active">
            <div class="menu-list">
                <div class="menu-list-item"><span>Bread Basket</span><span>$5.50</span></div>
                <div class="menu-list-item"><span>Honey Almond Granola with Fruits</span><span>$7.00</span></div>
                <div class="menu-list-item"><span>Belgian Waffle</span><span>$7.50</span></div>
                <div class="menu-list-item"><span>Scrambled Eggs</span><span>$7.50</span></div>
                <div class="menu-list-item"><span>Blueberry Pancakes</span><span>$8.50</span></div>
            </div>
        </div>
        <div id="drinkPanel" class="menu-panel">
            <div class="menu-list">
                <div class="menu-list-item"><span>Espresso</span><span>$3.00</span></div>
                <div class="menu-list-item"><span>Cappuccino</span><span>$4.50</span></div>
                <div class="menu-list-item"><span>Corretto</span><span>$5.00</span></div>
                <div class="menu-list-item"><span>Iced Tea</span><span>$3.00</span></div>
                <div class="menu-list-item"><span>Fresh Juice</span><span>$4.00</span></div>
            </div>
        </div>
    </div>
</section>

<!-- Seasonal Specials -->
<?php if (!empty($seasonal)): ?>
<section class="featured-section" style="background:var(--color-cream)">
    <div class="container">
        <h2 class="section-title animate-on-scroll">Seasonal Specials</h2>
        <div class="section-divider"></div>
        <p class="section-subtitle animate-on-scroll">Limited-time creations you won't want to miss</p>
        <div class="featured-grid stagger-children">
            <?php foreach ($seasonal as $p): ?>
            <div class="product-card">
                <div class="product-card-img">
                    <span class="product-card-ribbon"><?= sanitize($p['seasonal_label'] ?? 'Seasonal') ?></span>
                    <a href="<?= SITE_URL ?>/pages/product.php?id=<?= $p['id'] ?>">
                        <img src="<?= SITE_URL ?>/img/<?= sanitize($p['image']) ?>" alt="<?= sanitize($p['name']) ?>" loading="lazy">
                    </a>
                    <div class="product-card-overlay">
                        <button class="btn btn-gold btn-sm btn-add-cart" data-product-id="<?= $p['id'] ?>">Add to Cart</button>
                    </div>
                </div>
                <div class="product-card-body">
                    <span class="product-card-category"><?= sanitize($p['category']) ?></span>
                    <h3><?= sanitize($p['name']) ?></h3>
                    <p class="description"><?= sanitize($p['description']) ?></p>
                </div>
                <div class="product-card-footer">
                    <span class="product-price"><?= formatPrice($p['price']) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Blog Preview -->
<?php if (!empty($blogPosts)): ?>
<section style="padding:5rem 0">
    <div class="container">
        <h2 class="section-title animate-on-scroll">From Our Blog</h2>
        <div class="section-divider"></div>
        <p class="section-subtitle animate-on-scroll">Tips, recipes, and stories from the kitchen</p>
        <div class="blog-grid stagger-children">
            <?php foreach ($blogPosts as $post): ?>
            <div class="blog-card">
                <div class="blog-card-img">
                    <img src="<?= SITE_URL ?>/img/<?= sanitize($post['image'] ?? 'f1.jpg') ?>" alt="<?= sanitize($post['title']) ?>" loading="lazy">
                </div>
                <div class="blog-card-body">
                    <div class="blog-card-date"><?= formatDate($post['published_at']) ?></div>
                    <h3><a href="<?= SITE_URL ?>/pages/blog-post.php?slug=<?= sanitize($post['slug']) ?>"><?= sanitize($post['title']) ?></a></h3>
                    <p class="blog-card-excerpt"><?= sanitize($post['excerpt']) ?></p>
                    <a href="<?= SITE_URL ?>/pages/blog-post.php?slug=<?= sanitize($post['slug']) ?>" class="blog-read-more">Read More &rarr;</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align:center; margin-top:2rem" class="animate-on-scroll">
            <a href="<?= SITE_URL ?>/pages/blog.php" class="btn btn-outline">View All Posts</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Reservation -->
<section class="reservation-section" id="reserve">
    <div class="container">
        <h2 class="section-title animate-on-scroll">Reserve a Table</h2>
        <div class="section-divider"></div>
        <p class="section-subtitle" style="color:var(--color-cream-dark)">We'd love to have you</p>

        <div class="reservation-grid">
            <div class="reservation-info animate-on-scroll">
                <h3>Visit Us</h3>
                <p>247 Bakers Lane<br>Downtown, NY 10012</p>
                <h3>Opening Hours</h3>
                <p>Monday - Saturday: 7:00 AM - 8:00 PM<br>Sunday: 8:00 AM - 6:00 PM</p>
                <h3>Get in Touch</h3>
                <p>Phone: (212) 555-1234<br>Email: hello@bakenflake.com</p>
            </div>
            <form class="reservation-form animate-on-scroll" action="<?= SITE_URL ?>/api/reservation.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= sanitize($csrfToken) ?>">
                <div class="form-group">
                    <label for="resName">Your Name</label>
                    <input type="text" id="resName" name="name" required>
                </div>
                <div class="form-group">
                    <label for="resPeople">Number of Guests</label>
                    <input type="number" id="resPeople" name="people" min="1" max="20" required>
                </div>
                <div class="form-group">
                    <label for="resDate">Date & Time</label>
                    <input type="datetime-local" id="resDate" name="date" required>
                </div>
                <div class="form-group">
                    <label for="resMessage">Special Requests</label>
                    <textarea id="resMessage" name="message" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-gold btn-full">Reserve Now</button>
            </form>
        </div>
    </div>
</section>

<?php
$conn->close();
require_once __DIR__ . '/../includes/footer.php';
?>
