<?php
require_once __DIR__ . '/../config/init.php';
$conn = getDBConnection();

$slug = trim($_GET['slug'] ?? '');
$post = $slug ? getBlogPostBySlug($conn, $slug) : null;

if (!$post) {
    $pageTitle = 'Post Not Found';
    require_once __DIR__ . '/../includes/header.php';
    ?>
    <section class="section" style="text-align:center;padding:80px 0;">
        <div class="container">
            <h1 style="font-family:'Playfair Display',serif;margin-bottom:16px;">Post Not Found</h1>
            <p style="color:#888;margin-bottom:24px;">The blog post you're looking for doesn't exist or has been unpublished.</p>
            <a href="<?= SITE_URL ?>/pages/blog.php" class="btn btn-gold">Back to Blog</a>
        </div>
    </section>
    <?php
    require_once __DIR__ . '/../includes/footer.php';
    $conn->close();
    exit;
}

$pageTitle = $post['title'];
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Blog Post Hero -->
<?php if (!empty($post['image'])): ?>
<div class="blog-post-hero" style="width:100%;height:400px;position:relative;overflow:hidden;">
    <img src="<?= SITE_URL ?>/img/<?= sanitize($post['image']) ?>" alt="<?= sanitize($post['title']) ?>"
        style="width:100%;height:100%;object-fit:cover;display:block;">
    <div style="position:absolute;inset:0;background:linear-gradient(to bottom,transparent 50%,rgba(44,24,16,0.6));"></div>
</div>
<?php endif; ?>

<section class="section">
    <div class="container" style="max-width:800px;">
        <a href="<?= SITE_URL ?>/pages/blog.php" style="display:inline-flex;align-items:center;gap:6px;color:#DAA520;text-decoration:none;font-weight:600;margin-bottom:24px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back to Blog
        </a>

        <h1 style="font-family:'Playfair Display',serif;font-size:2.2rem;margin-bottom:16px;"><?= sanitize($post['title']) ?></h1>

        <div class="blog-post-meta" style="display:flex;gap:16px;color:#888;font-size:0.9rem;margin-bottom:32px;">
            <span><?= formatDate($post['published_at']) ?></span>
            <?php if (!empty($post['author'])): ?>
                <span>&middot;</span>
                <span>By <?= sanitize($post['author']) ?></span>
            <?php endif; ?>
        </div>

        <div class="blog-post-content" style="font-size:1.05rem;line-height:1.8;color:#444;">
            <?= $post['content'] ?>
        </div>
    </div>
</section>

<?php
$conn->close();
require_once __DIR__ . '/../includes/footer.php';
?>
