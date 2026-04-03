<?php
$pageTitle = 'Blog';
require_once __DIR__ . '/../includes/header.php';
$conn = getDBConnection();
$posts = getBlogPosts($conn);
?>

<!-- Blog Hero -->
<section class="page-hero" style="background-image: url('<?= SITE_URL ?>/img/emma-valerio-1060531-unsplash.jpg');">
    <div class="page-hero-overlay"></div>
    <div class="page-hero-content">
        <h1>Our Blog</h1>
        <p>Stories, recipes, and behind-the-scenes from our bakery</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if (empty($posts)): ?>
            <div style="text-align:center;padding:60px 0;">
                <h2 style="color:#888;margin-bottom:12px;">No posts yet</h2>
                <p style="color:#aaa;">Check back soon for fresh stories from the bakery.</p>
            </div>
        <?php else: ?>
        <div class="blog-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:30px;">
            <?php foreach ($posts as $post): ?>
            <article class="blog-card animate-on-scroll" style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.08);transition:transform 0.3s;">
                <?php if (!empty($post['image'])): ?>
                <a href="<?= SITE_URL ?>/pages/blog-post.php?slug=<?= sanitize($post['slug']) ?>">
                    <img src="<?= SITE_URL ?>/img/<?= sanitize($post['image']) ?>" alt="<?= sanitize($post['title']) ?>"
                        style="width:100%;height:220px;object-fit:cover;display:block;">
                </a>
                <?php endif; ?>
                <div style="padding:24px;">
                    <span style="font-size:0.8rem;color:#888;text-transform:uppercase;letter-spacing:0.04em;">
                        <?= formatDate($post['published_at']) ?>
                    </span>
                    <h3 style="font-family:'Playfair Display',serif;font-size:1.25rem;margin:8px 0 12px;">
                        <a href="<?= SITE_URL ?>/pages/blog-post.php?slug=<?= sanitize($post['slug']) ?>" style="color:#2C1810;text-decoration:none;">
                            <?= sanitize($post['title']) ?>
                        </a>
                    </h3>
                    <p style="color:#666;font-size:0.9rem;line-height:1.6;margin-bottom:16px;">
                        <?= sanitize($post['excerpt'] ?? mb_substr(strip_tags($post['content']), 0, 140) . '...') ?>
                    </p>
                    <a href="<?= SITE_URL ?>/pages/blog-post.php?slug=<?= sanitize($post['slug']) ?>" style="color:#DAA520;font-weight:700;text-decoration:none;font-size:0.9rem;">
                        Read More &rarr;
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php
$conn->close();
require_once __DIR__ . '/../includes/footer.php';
?>
