<?php
require_once __DIR__ . '/../config/init.php';
$cartCount = getCartCount();
$user = getCurrentUser();
$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? sanitize($pageTitle) . " | " . SITE_NAME : SITE_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= SITE_URL ?>/css/style.css">
    <meta name="csrf-token" content="<?= sanitize($csrfToken) ?>">
</head>
<body>
    <!-- Mobile Menu Overlay -->
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <a href="<?= SITE_URL ?>/pages/home.php" class="nav-logo">
                <img src="<?= SITE_URL ?>/img/logo.svg" alt="<?= SITE_NAME ?>">
            </a>

            <!-- Search Bar -->
            <div class="nav-search" id="navSearch">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                <input type="text" class="nav-search-input" id="navSearchInput" placeholder="Search pastries..." autocomplete="off">
                <div class="search-dropdown" id="searchDropdown"></div>
            </div>

            <ul class="nav-links" id="navLinks">
                <li><a href="<?= SITE_URL ?>/pages/home.php">Home</a></li>
                <li><a href="<?= SITE_URL ?>/pages/menu.php">Menu</a></li>
                <li><a href="<?= SITE_URL ?>/pages/custom-cake.php">Custom Cake</a></li>
                <li><a href="<?= SITE_URL ?>/pages/blog.php">Blog</a></li>
                <?php if ($user): ?>
                <li><a href="<?= SITE_URL ?>/pages/favorites.php" class="nav-icon-link" title="Favorites">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                </a></li>
                <?php endif; ?>
                <li><a href="<?= SITE_URL ?>/pages/cart.php" class="nav-cart">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                    </svg>
                    <span class="cart-badge" id="cartBadge" <?= $cartCount === 0 ? 'style="display:none"' : '' ?>><?= $cartCount ?></span>
                </a></li>
                <?php if ($user): ?>
                    <li class="nav-user-menu">
                        <button class="nav-user-btn" id="navUserBtn">
                            <span class="nav-avatar"><?= strtoupper(substr($user['name'], 0, 1)) ?></span>
                            <span class="nav-user-name"><?= sanitize($user['name']) ?></span>
                            <?php if ($user['loyalty_points'] > 0): ?>
                                <span class="nav-points-badge"><?= $user['loyalty_points'] ?> pts</span>
                            <?php endif; ?>
                        </button>
                        <div class="nav-user-dropdown" id="navUserDropdown">
                            <a href="<?= SITE_URL ?>/pages/orders.php">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                My Orders
                            </a>
                            <a href="<?= SITE_URL ?>/pages/favorites.php">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                                Favorites
                            </a>
                            <a href="<?= SITE_URL ?>/pages/custom-cake.php">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                                Custom Cake
                            </a>
                            <?php if ($user['is_admin']): ?>
                            <div class="dropdown-divider"></div>
                            <a href="<?= SITE_URL ?>/admin/" class="nav-admin-link">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                                Admin Panel
                            </a>
                            <?php endif; ?>
                            <div class="dropdown-divider"></div>
                            <a href="<?= SITE_URL ?>/pages/logout.php">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                                Logout
                            </a>
                        </div>
                    </li>
                <?php else: ?>
                    <li><a href="<?= SITE_URL ?>/pages/login.php" class="btn btn-outline-light btn-sm">Login</a></li>
                    <li><a href="<?= SITE_URL ?>/pages/signup.php" class="btn btn-gold btn-sm">Sign Up</a></li>
                <?php endif; ?>
            </ul>

            <button class="hamburger" id="hamburger" aria-label="Toggle menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php
    $flashError = getFlash('error');
    $flashSuccess = getFlash('success');
    ?>
    <?php if ($flashError): ?>
        <div class="flash flash-error" id="flashMsg"><?= sanitize($flashError) ?></div>
    <?php endif; ?>
    <?php if ($flashSuccess): ?>
        <div class="flash flash-success" id="flashMsg"><?= sanitize($flashSuccess) ?></div>
    <?php endif; ?>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <main>
