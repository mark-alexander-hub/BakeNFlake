# CLAUDE.md — Bake'n Flake Developer Guide

## Project Overview

Bake'n Flake is a full-featured bakery e-commerce web application built with PHP, MySQL, vanilla JavaScript, and custom CSS. It runs on XAMPP (Apache + MySQL). No frameworks, no Composer, no npm — pure hand-coded stack.

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.x (procedural) |
| Database | MySQL 8.x (via MySQLi with prepared statements) |
| Frontend | Vanilla JavaScript (ES6+), custom CSS with CSS variables |
| Server | Apache (XAMPP) |
| Charts | Chart.js (CDN) |
| Animations | canvas-confetti (CDN) |
| Fonts | Google Fonts — Playfair Display + Lato |

## Quick Start

```bash
# 1. Clone the repo into XAMPP htdocs
git clone https://github.com/mark-alexander-hub/BakeNFlake.git /c/xampp/htdocs/Bakery

# 2. Start XAMPP (Apache + MySQL)

# 3. Create database and seed products
mysql -u root < sql/schema.sql

# 4. Run migrations (adds new tables + columns)
mysql -u root < sql/migrations.sql

# 5. Seed demo data (admin user, reviews, orders, blog posts)
mysql -u root < sql/seed_demo.sql
mysql -u root < sql/seed_blogs.sql

# 6. Open http://localhost/Bakery/
```

### Demo Accounts
- **Admin**: admin@bakenflake.com / admin123
- **Customer**: demo@bakenflake.com / admin123

### Discount Codes (for testing checkout)
- `WELCOME10` — 10% off (min $15)
- `SWEET5` — $5 off (min $25)
- `BAKE20` — 20% off (min $40)

## Architecture

### Directory Structure

```
Bakery/
├── config/
│   ├── database.php          # DB connection, constants (SITE_URL, SITE_NAME)
│   └── init.php              # Session, CSRF, auth helpers (isLoggedIn, isAdmin, etc.)
│
├── includes/
│   ├── functions.php          # ~50 reusable functions (products, cart, reviews, favorites, etc.)
│   ├── header.php             # Public page header: nav, search bar, user dropdown, flash/toast
│   ├── footer.php             # Public page footer: newsletter, links, social, main.js
│   ├── admin_functions.php    # Admin helpers (stats, CRUD, dashboard queries)
│   ├── admin_header.php       # Admin layout: sidebar nav, Chart.js CDN
│   └── admin_footer.php       # Admin layout close, admin.js
│
├── pages/                     # Public-facing pages
│   ├── home.php               # Landing: hero, featured, seasonal, carousel, menu preview, blog, reservation
│   ├── menu.php               # Product catalog with category filters, ratings, favorites
│   ├── product.php            # Product detail: image, allergens, reviews, tabs, related products
│   ├── cart.php               # Session-based cart with AJAX quantity controls
│   ├── checkout.php           # Discount codes, delivery scheduling, loyalty points, Stripe-like payment
│   ├── order-confirmation.php # Confetti, animated checkmark, email receipt modal
│   ├── orders.php             # User order history with expandable details + timeline
│   ├── custom-cake.php        # Interactive cake builder with live CSS preview
│   ├── favorites.php          # User wishlist
│   ├── search.php             # Search results page
│   ├── blog.php               # Blog listing grid
│   ├── blog-post.php          # Individual blog post (HTML content from DB)
│   ├── about.php              # Our Story + gallery
│   ├── login.php              # Login form (floating labels)
│   ├── signup.php             # Registration form
│   └── logout.php             # Session destroy + redirect
│
├── admin/                     # Admin panel (requires is_admin=1)
│   ├── index.php              # Dashboard: stat cards, Chart.js charts, recent orders
│   ├── products.php           # Product CRUD with modal forms
│   ├── orders.php             # Order management + custom cake orders
│   ├── reservations.php       # Reservation management
│   └── users.php              # User management, toggle admin
│
├── api/                       # AJAX/API endpoints (return JSON or redirect)
│   ├── auth_actions.php       # Login + signup POST handler
│   ├── cart_actions.php       # Cart add/update/remove/get (JSON)
│   ├── search.php             # Live search (GET, JSON)
│   ├── reviews.php            # Submit review (POST, JSON)
│   ├── favorites.php          # Toggle favorite (POST, JSON)
│   ├── discount.php           # Validate discount code (POST, JSON)
│   ├── delivery_slots.php     # Get available slots (GET, JSON)
│   ├── newsletter.php         # Email subscribe (POST, JSON)
│   ├── reservation.php        # Reservation form handler (POST, redirect)
│   ├── custom_order.php       # Custom cake submission (POST, redirect)
│   └── admin_actions.php      # Admin CRUD operations (POST, JSON)
│
├── css/
│   └── style.css              # Complete design system (~2700 lines)
│
├── js/
│   ├── main.js                # Public JS (~550 lines): nav, search, cart, favorites, cake builder, payment, confetti
│   └── admin.js               # Admin JS: Chart.js, animated counters, CRUD modals, status updates
│
├── sql/
│   ├── schema.sql             # Base schema: users, products, orders, order_items, reservations + 22 product seeds
│   ├── migrations.sql         # Extended schema: 10 new tables + ALTER existing + allergen/seasonal data
│   ├── seed_demo.sql          # Demo data: admin user, reviews, orders, reservations, newsletter
│   └── seed_blogs.sql         # 6 additional blog posts
│
├── img/                       # Product images (m1-m22.jpg), carousel (f1-f9.jpg), drink SVGs, logo.svg
├── uploads/                   # Custom cake reference image uploads
└── index.php                  # Entry redirect → pages/home.php
```

### Database Schema (15 tables)

| Table | Purpose |
|-------|---------|
| `users` | Auth, admin flag, loyalty points |
| `products` | 46 products with allergens, nutrition, seasonal flags |
| `orders` | Orders with discount, delivery, loyalty tracking |
| `order_items` | Line items per order |
| `reservations` | Table reservations with status |
| `reviews` | Star ratings + text reviews per product/user |
| `favorites` | User wishlist (product favorites) |
| `discount_codes` | Promo codes (percentage/fixed, expiry, usage limits) |
| `custom_orders` | Custom cake requests with all options |
| `blog_posts` | Blog articles with slugs, HTML content |
| `newsletter_subscribers` | Email subscriptions |
| `loyalty_ledger` | Points earned/redeemed audit trail |
| `delivery_slots` | Time slots per date with capacity limits |
| `search_log` | Search query analytics |

### Security

- **Passwords**: bcrypt via `password_hash()` / `password_verify()`
- **SQL**: All queries use prepared statements (MySQLi)
- **XSS**: All output through `sanitize()` → `htmlspecialchars()`
- **CSRF**: Token generated per session, validated on all POST requests
- **Auth**: Session-based with `isLoggedIn()`, `requireLogin()`, `requireAdmin()` gates

### Design System (CSS Variables)

```
Primary:    #8B4513 (saddle brown)
Dark:       #2C1810 (deep chocolate)
Gold:       #DAA520 (CTAs, accents)
Cream:      #FDF6EC (backgrounds)
Pink:       #E8B0AF (accents)
Success:    #5a9e6f
Error:      #c0392b
Headings:   Playfair Display (serif)
Body:       Lato (sans-serif)
```

### Key Patterns

- **Page pattern**: Set `$pageTitle`, include `header.php`, render content, include `footer.php`
- **API pattern**: Include `init.php`, validate CSRF, switch on action, return JSON
- **Cart**: Session-based (`$_SESSION['cart']`), AJAX via `fetch()` to `api/cart_actions.php`
- **Admin**: Separate layout (admin_header/footer), `requireAdmin()` gate, AJAX CRUD
- **Animations**: CSS keyframes + Intersection Observer for scroll-triggered animations

## Common Tasks

### Add a new product
Via admin panel (`/admin/products.php`) or directly in MySQL. Categories: cakes, cookies, donuts, bread, pastries, drinks.

### Add a new blog post
Insert into `blog_posts` table with `is_published=1` and a unique `slug`. Content field accepts raw HTML.

### Add a new page
1. Create `pages/newpage.php`
2. Set `$pageTitle`, include `header.php` at top and `footer.php` at bottom
3. Add link to nav in `includes/header.php`

### Add a new API endpoint
1. Create `api/endpoint.php`
2. Include `config/init.php`, validate CSRF for POST requests
3. Return `json_encode()` response with `Content-Type: application/json` header

### Change the color scheme
Edit CSS custom properties in `:root` at the top of `css/style.css`.
