<?php $pageTitle = 'Our Story'; require_once __DIR__ . '/../includes/header.php'; ?>

<!-- About Hero -->
<section class="about-hero hero">
    <div class="hero-bg" style="background-image: url('<?= SITE_URL ?>/img/sarah-shaffer-697248-unsplash.jpg')"></div>
    <h1>Our Story</h1>
</section>

<!-- Story Content -->
<section class="about-content">
    <div class="container">
        <div class="about-story animate-on-scroll">
            <h2>Baked with Heart</h2>
            <div class="section-divider"></div>
            <p>Bake'n Flake began with a simple dream: to bring the warmth of homemade pastries to every table. What started as a weekend hobby of experimenting with sourdough and layered cakes has grown into a beloved neighborhood bakery.</p>
            <p>Every morning before dawn, our bakers begin crafting each pastry by hand. We source the finest ingredients — real butter, organic flour, seasonal fruits — because we believe great baking starts with great ingredients.</p>
            <p>From our signature Blackberry Lavender Naked Cake to our rustic Cranberry Walnut Artisan Bread, every item on our menu is a labor of love. We don't cut corners, and we never will.</p>
        </div>

        <h2 class="section-title animate-on-scroll">From Our Kitchen</h2>
        <div class="section-divider"></div>
        <div class="about-gallery animate-on-scroll">
            <?php for ($i = 1; $i <= 9; $i++): ?>
            <img src="<?= SITE_URL ?>/img/f<?= $i ?>.jpg" alt="Bakery gallery <?= $i ?>" loading="lazy">
            <?php endfor; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
