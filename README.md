# Bake'n Flake

**Full-featured bakery e-commerce web application with admin panel, custom cake builder, loyalty program, and more.**

Built with PHP, MySQL, vanilla JavaScript, and custom CSS. No frameworks — hand-coded from scratch.

![PHP](https://img.shields.io/badge/PHP-8.x-blue)
![MySQL](https://img.shields.io/badge/MySQL-8.x-orange)
![License](https://img.shields.io/badge/License-MIT-green)

---

## Features

### Customer Experience
| Feature | Description |
|---------|-------------|
| **Product Catalog** | 46 products across 6 categories (Cakes, Pastries, Cookies, Donuts, Bread, Drinks) with category filtering |
| **Product Detail** | Full page per product with allergen info, nutrition tags, star ratings, reviews, and related products |
| **Live Search** | Real-time search dropdown in the nav bar with product thumbnails and prices |
| **Shopping Cart** | Session-based cart with AJAX add/remove/update, quantity controls, live total calculation |
| **Smart Checkout** | Discount codes, delivery date/time scheduling, loyalty points redemption, simulated Stripe payment |
| **Order Confirmation** | Confetti celebration, animated checkmark, HTML email receipt preview modal |
| **Custom Cake Builder** | Interactive form with live CSS cake preview — pick occasion, flavor, frosting, size, tiers, message |
| **Favorites/Wishlist** | Heart-toggle on any product, dedicated favorites page |
| **Order History** | Past orders with status badges, expandable item details, visual timeline |
| **Reviews & Ratings** | Star ratings + text reviews on products, average shown on cards |
| **Blog** | 9 articles about baking with full article pages |
| **Reservations** | Table booking form with date/time and guest count |
| **Loyalty Points** | Earn 1 point per $1 spent, redeem 100 points = $1 at checkout |
| **Newsletter** | Email subscription with toast confirmation |
| **Seasonal Specials** | Highlighted products with ribbon badges, date-range driven |

### Admin Panel (`/admin/`)
| Feature | Description |
|---------|-------------|
| **Dashboard** | Animated stat counters, Chart.js revenue line chart + category doughnut, recent orders table |
| **Product Management** | Full CRUD with modal forms — add/edit/delete products, toggle availability, seasonal flags |
| **Order Management** | View all orders, inline status updates, custom cake orders section |
| **Reservation Management** | View/update reservation status (pending/confirmed/cancelled) |
| **User Management** | View users, order counts, loyalty points, toggle admin privileges |

### Design & UX
- Warm artisan bakery palette (chocolate browns, cream, gold accents)
- Playfair Display + Lato typography
- Scroll-triggered animations via Intersection Observer
- Responsive mobile design with hamburger menu
- Toast notifications for all actions
- Skeleton loading states
- SVG logo and drink illustrations

---

## Quick Start

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL)
- PHP 8.0+

### Install & Run

```bash
# Clone into XAMPP htdocs
git clone https://github.com/mark-alexander-hub/BakeNFlake.git C:/xampp/htdocs/Bakery

# Start XAMPP (Apache + MySQL)

# Set up the database (run in order)
mysql -u root < sql/schema.sql
mysql -u root < sql/migrations.sql
mysql -u root < sql/seed_demo.sql
mysql -u root < sql/seed_blogs.sql

# Open in browser
http://localhost/Bakery/
```

### Demo Accounts

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@bakenflake.com | admin123 |
| Customer | demo@bakenflake.com | admin123 |

### Test Discount Codes

| Code | Discount | Minimum Order |
|------|----------|--------------|
| WELCOME10 | 10% off | $15 |
| SWEET5 | $5 off | $25 |
| BAKE20 | 20% off | $40 |

---

## Architecture

```
Bakery/
├── config/           # Database connection, session/auth/CSRF helpers
├── includes/         # Shared PHP: header, footer, functions, admin layout
├── pages/            # 15 public-facing pages
├── admin/            # 5 admin panel pages
├── api/              # 11 API endpoints (JSON + form handlers)
├── css/style.css     # Complete design system (~2700 lines)
├── js/main.js        # Public JavaScript (~550 lines)
├── js/admin.js       # Admin JavaScript (Chart.js, counters, CRUD)
├── sql/              # Schema, migrations, seed data
├── img/              # Product images, carousel, drink SVGs, logo
└── uploads/          # Custom cake reference image uploads
```

### Database: 15 Tables

`users` · `products` · `orders` · `order_items` · `reservations` · `reviews` · `favorites` · `discount_codes` · `custom_orders` · `blog_posts` · `newsletter_subscribers` · `loyalty_ledger` · `delivery_slots` · `search_log`

### Security

- Bcrypt password hashing (`password_hash` / `password_verify`)
- Prepared statements for all SQL queries (MySQLi)
- CSRF token validation on all forms and AJAX
- XSS prevention via `htmlspecialchars()` on all output
- Session-based authentication with admin role gating

---

## Tech Stack

| Component | Technology |
|-----------|-----------|
| Backend | PHP 8.x (procedural, no framework) |
| Database | MySQL 8.x via MySQLi |
| Frontend | Vanilla JavaScript (ES6+) |
| Styling | Custom CSS with CSS variables |
| Charts | [Chart.js](https://www.chartjs.org/) (CDN) |
| Confetti | [canvas-confetti](https://www.npmjs.com/package/canvas-confetti) (CDN) |
| Fonts | Google Fonts (Playfair Display + Lato) |
| Server | Apache (XAMPP) |

---

## Screenshots

### Home Page
Hero section with parallax background, featured products, seasonal specials, image carousel, menu preview, blog section, and reservation form.

### Menu
46 products across 6 categories with filtering, star ratings, favorite hearts, and add-to-cart overlays.

### Custom Cake Builder
Interactive form with live CSS cake preview that updates colors, tiers, and message in real-time.

### Admin Dashboard
Animated stat counters, revenue chart, category breakdown, and recent orders — all powered by Chart.js.

### Checkout
Discount codes, delivery scheduling with time slots, loyalty points redemption, and Stripe-like card payment UI with processing animation.

---

## License

MIT
