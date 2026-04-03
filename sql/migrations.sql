USE bakenflake;

-- ========================================
-- ALTER EXISTING TABLES
-- ========================================

ALTER TABLE users
  ADD COLUMN is_admin TINYINT(1) NOT NULL DEFAULT 0 AFTER password_hash,
  ADD COLUMN loyalty_points INT NOT NULL DEFAULT 0 AFTER is_admin,
  ADD COLUMN phone VARCHAR(20) DEFAULT NULL AFTER email,
  ADD COLUMN address TEXT DEFAULT NULL AFTER phone;

ALTER TABLE products
  ADD COLUMN allergens VARCHAR(500) DEFAULT NULL AFTER image,
  ADD COLUMN nutrition_tags VARCHAR(500) DEFAULT NULL AFTER allergens,
  ADD COLUMN is_seasonal TINYINT(1) NOT NULL DEFAULT 0 AFTER is_available,
  ADD COLUMN seasonal_label VARCHAR(100) DEFAULT NULL AFTER is_seasonal,
  ADD COLUMN seasonal_start DATE DEFAULT NULL AFTER seasonal_label,
  ADD COLUMN seasonal_end DATE DEFAULT NULL AFTER seasonal_start;

ALTER TABLE orders
  ADD COLUMN discount_code VARCHAR(50) DEFAULT NULL AFTER notes,
  ADD COLUMN discount_amount DECIMAL(10,2) DEFAULT 0 AFTER discount_code,
  ADD COLUMN subtotal DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER total,
  ADD COLUMN tax DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER subtotal,
  ADD COLUMN shipping DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER tax,
  ADD COLUMN points_earned INT NOT NULL DEFAULT 0 AFTER shipping,
  ADD COLUMN points_redeemed INT NOT NULL DEFAULT 0 AFTER points_earned,
  ADD COLUMN delivery_date DATE DEFAULT NULL AFTER status,
  ADD COLUMN delivery_slot VARCHAR(50) DEFAULT NULL AFTER delivery_date,
  ADD COLUMN payment_method VARCHAR(20) DEFAULT 'card' AFTER delivery_slot;

ALTER TABLE reservations
  ADD COLUMN status ENUM('pending','confirmed','cancelled') DEFAULT 'pending' AFTER message;

-- ========================================
-- NEW TABLES
-- ========================================

CREATE TABLE IF NOT EXISTS reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  user_id INT NOT NULL,
  rating TINYINT NOT NULL,
  review_text TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY unique_review (product_id, user_id)
);

CREATE TABLE IF NOT EXISTS favorites (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  product_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  UNIQUE KEY unique_fav (user_id, product_id)
);

CREATE TABLE IF NOT EXISTS discount_codes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(50) NOT NULL UNIQUE,
  type ENUM('percentage','fixed') NOT NULL,
  value DECIMAL(10,2) NOT NULL,
  min_order DECIMAL(10,2) DEFAULT 0,
  max_uses INT DEFAULT NULL,
  times_used INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  expires_at DATETIME DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS custom_orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  occasion VARCHAR(100) NOT NULL,
  flavor_base VARCHAR(100) NOT NULL,
  filling VARCHAR(100) DEFAULT NULL,
  frosting VARCHAR(100) DEFAULT NULL,
  size VARCHAR(50) NOT NULL,
  tiers TINYINT DEFAULT 1,
  cake_message VARCHAR(200) DEFAULT NULL,
  reference_image VARCHAR(255) DEFAULT NULL,
  date_needed DATE NOT NULL,
  special_notes TEXT,
  estimated_price DECIMAL(10,2) DEFAULT NULL,
  status ENUM('pending','quoted','confirmed','in_progress','ready','completed','cancelled') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS blog_posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  excerpt TEXT,
  content LONGTEXT NOT NULL,
  image VARCHAR(255) DEFAULT NULL,
  author_id INT,
  is_published TINYINT(1) DEFAULT 0,
  published_at DATETIME DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS newsletter_subscribers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL UNIQUE,
  is_active TINYINT(1) DEFAULT 1,
  subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS loyalty_ledger (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  order_id INT DEFAULT NULL,
  points INT NOT NULL,
  type ENUM('earned','redeemed','adjusted') NOT NULL,
  description VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS delivery_slots (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slot_date DATE NOT NULL,
  slot_time VARCHAR(50) NOT NULL,
  max_orders INT DEFAULT 5,
  current_orders INT DEFAULT 0,
  UNIQUE KEY unique_slot (slot_date, slot_time)
);

CREATE TABLE IF NOT EXISTS search_log (
  id INT AUTO_INCREMENT PRIMARY KEY,
  query VARCHAR(255) NOT NULL,
  results_count INT DEFAULT 0,
  user_id INT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ========================================
-- SEED DATA
-- ========================================

INSERT INTO discount_codes (code, type, value, min_order, max_uses) VALUES
('WELCOME10', 'percentage', 10.00, 15.00, 100),
('SWEET5', 'fixed', 5.00, 25.00, 50),
('BAKE20', 'percentage', 20.00, 40.00, 30);

-- Update products with allergen and nutrition info
UPDATE products SET allergens = 'Dairy, Eggs, Wheat', nutrition_tags = 'Vegetarian' WHERE category = 'cakes';
UPDATE products SET allergens = 'Dairy, Eggs, Wheat, Nuts', nutrition_tags = 'Vegetarian' WHERE id IN (1, 2, 5);
UPDATE products SET allergens = 'Dairy, Eggs, Wheat', nutrition_tags = 'Vegetarian' WHERE category = 'cookies';
UPDATE products SET nutrition_tags = 'Vegan' WHERE id = 11;
UPDATE products SET allergens = 'Coconut', nutrition_tags = 'Gluten-Free, Vegan' WHERE id = 13;
UPDATE products SET allergens = 'Nuts', nutrition_tags = 'Gluten-Free' WHERE id = 14;
UPDATE products SET allergens = 'Dairy, Eggs, Wheat', nutrition_tags = 'Vegetarian' WHERE category = 'donuts' AND allergens IS NULL;
UPDATE products SET allergens = 'Wheat', nutrition_tags = 'Vegan' WHERE category = 'bread';
UPDATE products SET allergens = 'Dairy, Eggs, Wheat, Nuts', nutrition_tags = 'Vegetarian' WHERE id IN (17, 21);

-- Mark some products as seasonal
UPDATE products SET is_seasonal = 1, seasonal_label = 'Spring Special', seasonal_start = '2026-03-01', seasonal_end = '2026-05-31' WHERE id IN (4, 7, 15);

-- Seed blog posts
INSERT INTO blog_posts (title, slug, excerpt, content, image, author_id, is_published, published_at) VALUES
('The Art of Sourdough', 'the-art-of-sourdough',
 'Everything you need to know about crafting the perfect sourdough loaf at home.',
 '<p>Sourdough baking is both an art and a science. The magic begins with a simple mixture of flour and water, left to capture wild yeast from the air around us.</p><h2>Getting Started with Your Starter</h2><p>A healthy sourdough starter is the foundation of great bread. Mix equal parts flour and water in a clean jar, and feed it daily. Within 5-7 days, you''ll notice bubbles forming — that''s the wild yeast coming to life.</p><h2>The Perfect Loaf</h2><p>Once your starter doubles in size within 4-6 hours of feeding, it''s ready to use. Mix it with flour, water, and salt, then let time do the work. A long, slow fermentation develops complex flavors that no commercial yeast can match.</p><p>The key is patience. Great sourdough can''t be rushed. Let the dough rest, stretch and fold it gently, and give it a cold overnight proof in the refrigerator for the best flavor development.</p>',
 'f1.jpg', NULL, 1, NOW()),

('5 Tips for Perfect Buttercream', '5-tips-for-perfect-buttercream',
 'Master the art of silky smooth buttercream frosting with these professional techniques.',
 '<p>Buttercream can make or break a cake. Here are the secrets our bakers swear by.</p><h2>1. Temperature is Everything</h2><p>Your butter should be at exactly room temperature — soft enough to indent with a finger, but not greasy or melted. Too cold and it''ll be lumpy; too warm and it''ll be soupy.</p><h2>2. Sift Your Sugar</h2><p>Always sift powdered sugar before adding it. Those tiny lumps might seem harmless in the bag, but they''ll show up as gritty spots in your frosting.</p><h2>3. Beat Longer Than You Think</h2><p>After combining butter and sugar, beat on medium-high for a full 5 minutes. This incorporates air and creates that light, fluffy texture you see in bakery frosting.</p><h2>4. Add Liquid Gradually</h2><p>Whether it''s vanilla extract, cream, or milk, add your liquid a tablespoon at a time. Too much at once can break the emulsion.</p><h2>5. Color at the End</h2><p>Add gel food coloring as the very last step. Gel colors are more concentrated than liquid and won''t thin your frosting.</p>',
 'f2.jpg', NULL, 1, NOW()),

('Seasonal Baking: Spring Flavors', 'seasonal-baking-spring-flavors',
 'Embrace the freshness of spring with these inspired recipes featuring seasonal ingredients.',
 '<p>Spring brings an abundance of fresh flavors to our kitchen — and to your table.</p><h2>Lavender & Lemon</h2><p>The combination of dried culinary lavender and bright lemon zest is quintessentially spring. Try it in shortbread cookies, pound cake, or even a delicate buttercream.</p><h2>Strawberry Everything</h2><p>When strawberries hit peak season, we go all in. Fresh strawberry compote between cake layers, strawberry cream cheese frosting, and our legendary strawberry rhubarb pie.</p><h2>Herbs in Sweet Baking</h2><p>Don''t be afraid to use fresh herbs in your baking. Rosemary pairs beautifully with olive oil cake, fresh mint elevates chocolate brownies, and thyme adds an unexpected warmth to lemon bars.</p><p>The best spring baking celebrates ingredients at their freshest. Visit your local farmers market and let the produce inspire your next creation.</p>',
 'f3.jpg', NULL, 1, NOW());
