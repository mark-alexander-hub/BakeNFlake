<?php
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/admin_functions.php';
requireAdmin();
$csrfToken = generateCSRFToken();
$currentPage = $currentPage ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — <?= sanitize(ucfirst($currentPage)) ?> | <?= SITE_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= SITE_URL ?>/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <meta name="csrf-token" content="<?= sanitize($csrfToken) ?>">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Lato', sans-serif; background: #f5f0eb; color: #2C1810; }

        .admin-layout { display: flex; min-height: 100vh; }

        /* Sidebar */
        .admin-sidebar {
            width: 260px;
            background: #2C1810;
            color: #fff;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            overflow-y: auto;
        }
        .admin-sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 24px 20px;
            text-decoration: none;
            color: #fff;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .admin-sidebar-logo img { width: 40px; height: 40px; border-radius: 8px; }
        .admin-sidebar-logo span { font-family: 'Playfair Display', serif; font-size: 1.2rem; font-weight: 700; }

        .admin-nav { padding: 16px 0; flex: 1; }
        .admin-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 24px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            font-size: 0.95rem;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        .admin-nav a:hover { color: #fff; background: rgba(255,255,255,0.05); }
        .admin-nav a.active {
            color: #DAA520;
            background: rgba(218,165,32,0.1);
            border-left-color: #DAA520;
        }
        .admin-nav a svg { width: 20px; height: 20px; flex-shrink: 0; }

        .nav-divider { height: 1px; background: rgba(255,255,255,0.1); margin: 12px 20px; }

        .back-to-site {
            padding: 16px 24px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255,255,255,0.5);
            text-decoration: none;
            font-size: 0.85rem;
            transition: color 0.2s;
        }
        .back-to-site:hover { color: #fff; }

        /* Main Content */
        .admin-main {
            flex: 1;
            margin-left: 260px;
            padding: 32px;
            min-height: 100vh;
        }

        .admin-page-title { font-family: 'Playfair Display', serif; font-size: 1.8rem; margin-bottom: 24px; }

        /* Stats Grid */
        .admin-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 32px; }
        .admin-stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .admin-stat-icon {
            width: 52px; height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .admin-stat-icon svg { width: 24px; height: 24px; }
        .admin-stat-icon.revenue { background: #fef3c7; color: #d97706; }
        .admin-stat-icon.orders { background: #dbeafe; color: #2563eb; }
        .admin-stat-icon.users { background: #ede9fe; color: #7c3aed; }
        .admin-stat-icon.products { background: #dcfce7; color: #16a34a; }
        .admin-stat-info h3 { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: #888; margin-bottom: 4px; }
        .admin-stat-info .stat-value { font-size: 1.6rem; font-weight: 700; }

        /* Charts */
        .admin-charts { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 32px; }
        .admin-chart-card {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .admin-chart-card h3 { font-family: 'Playfair Display', serif; font-size: 1.1rem; margin-bottom: 16px; }

        /* Table */
        .admin-table-wrap {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            margin-bottom: 32px;
            overflow-x: auto;
        }
        .admin-table-wrap h3 { font-family: 'Playfair Display', serif; font-size: 1.1rem; margin-bottom: 16px; }
        .admin-table { width: 100%; border-collapse: collapse; }
        .admin-table th {
            text-align: left;
            padding: 12px 16px;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #888;
            border-bottom: 2px solid #f0ebe5;
        }
        .admin-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #f0ebe5;
            font-size: 0.9rem;
            vertical-align: middle;
        }
        .admin-table tr:last-child td { border-bottom: none; }
        .admin-table tr:hover { background: #faf8f5; }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: capitalize;
        }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-processing { background: #dbeafe; color: #1e40af; }
        .badge-completed, .badge-delivered { background: #dcfce7; color: #166534; }
        .badge-cancelled { background: #fee2e2; color: #991b1b; }
        .badge-confirmed { background: #dcfce7; color: #166534; }
        .badge-admin { background: #DAA520; color: #fff; }

        /* Buttons */
        .admin-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s;
            text-decoration: none;
        }
        .admin-btn-primary { background: #DAA520; color: #fff; }
        .admin-btn-primary:hover { background: #c4941c; }
        .admin-btn-danger { background: #fee2e2; color: #991b1b; }
        .admin-btn-danger:hover { background: #fca5a5; }
        .admin-btn-sm { padding: 5px 10px; font-size: 0.8rem; }

        /* Status select */
        .status-select {
            padding: 5px 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 0.85rem;
            background: #fff;
            cursor: pointer;
        }

        /* Availability dot */
        .avail-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
        .avail-dot.available { background: #16a34a; }
        .avail-dot.unavailable { background: #dc2626; }

        /* Product thumbnail */
        .product-thumb { width: 48px; height: 48px; border-radius: 8px; object-fit: cover; }

        /* Modal */
        .admin-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 200;
        }
        .admin-modal-overlay.open { display: flex; }
        .admin-modal {
            background: #fff;
            border-radius: 16px;
            padding: 32px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
        }
        .admin-modal h2 { font-family: 'Playfair Display', serif; margin-bottom: 20px; }
        .admin-modal-close {
            position: absolute;
            top: 16px; right: 16px;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #888;
        }
        .admin-form-group { margin-bottom: 16px; }
        .admin-form-group label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px; color: #555; }
        .admin-form-group input,
        .admin-form-group select,
        .admin-form-group textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 0.9rem;
            font-family: 'Lato', sans-serif;
        }
        .admin-form-group textarea { resize: vertical; min-height: 80px; }
        .admin-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .admin-checkbox { display: flex; align-items: center; gap: 8px; }
        .admin-checkbox input { width: auto; }

        .admin-actions-cell { display: flex; gap: 6px; }

        .admin-top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }

        @media (max-width: 900px) {
            .admin-sidebar { width: 220px; }
            .admin-main { margin-left: 220px; padding: 20px; }
            .admin-charts { grid-template-columns: 1fr; }
        }
        @media (max-width: 640px) {
            .admin-sidebar { display: none; }
            .admin-main { margin-left: 0; }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <a href="<?= SITE_URL ?>/admin/index.php" class="admin-sidebar-logo">
                <img src="<?= SITE_URL ?>/img/logo.svg" alt="<?= SITE_NAME ?>">
                <span>Bake'n Flake</span>
            </a>
            <nav class="admin-nav">
                <a href="<?= SITE_URL ?>/admin/index.php" class="<?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Dashboard
                </a>
                <a href="<?= SITE_URL ?>/admin/products.php" class="<?= $currentPage === 'products' ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                    Products
                </a>
                <a href="<?= SITE_URL ?>/admin/orders.php" class="<?= $currentPage === 'orders' ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    Orders
                </a>
                <a href="<?= SITE_URL ?>/admin/reservations.php" class="<?= $currentPage === 'reservations' ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Reservations
                </a>
                <a href="<?= SITE_URL ?>/admin/users.php" class="<?= $currentPage === 'users' ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                    Users
                </a>
            </nav>
            <div class="nav-divider"></div>
            <a href="<?= SITE_URL ?>/pages/home.php" class="back-to-site">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                Back to Site
            </a>
        </aside>
        <div class="admin-main">
