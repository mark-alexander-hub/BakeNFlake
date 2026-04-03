USE bakenflake;

-- Delete existing demo admin if exists and recreate
DELETE FROM users WHERE email = 'admin@bakenflake.com';
INSERT INTO users (name, email, password_hash, is_admin, loyalty_points) VALUES
('Admin Baker', 'admin@bakenflake.com', '$2y$10$YVyndCKzy7mKLmTt21rzY.UwAagetxzHYRuyzrBonDUi3eP6fGB4W', 1, 500);

-- Create demo customer
DELETE FROM users WHERE email = 'demo@bakenflake.com';
INSERT INTO users (name, email, password_hash, is_admin, loyalty_points) VALUES
('Sarah Johnson', 'demo@bakenflake.com', '$2y$10$YVyndCKzy7mKLmTt21rzY.UwAagetxzHYRuyzrBonDUi3eP6fGB4W', 0, 150);

-- Sample reviews
INSERT IGNORE INTO reviews (product_id, user_id, rating, review_text) VALUES
(1, (SELECT id FROM users WHERE email='demo@bakenflake.com'), 5, 'Absolutely stunning cake! The blackberry lavender combination is heavenly. Ordered this for my daughter''s birthday and everyone was raving about it.'),
(2, (SELECT id FROM users WHERE email='demo@bakenflake.com'), 4, 'Rich and delicious. The hazelnut flavor really comes through. Would have liked a bit more fig compote between the layers.'),
(3, (SELECT id FROM users WHERE email='admin@bakenflake.com'), 5, 'A showstopper for winter gatherings. The mulled wine buttercream is unlike anything I''ve tasted. Worth every penny.'),
(5, (SELECT id FROM users WHERE email='demo@bakenflake.com'), 5, 'The caramelized pears add such a lovely texture. One of the best chocolate cakes I''ve had.'),
(8, (SELECT id FROM users WHERE email='admin@bakenflake.com'), 4, 'Perfect with afternoon coffee. Not too sweet, beautifully layered.'),
(9, (SELECT id FROM users WHERE email='demo@bakenflake.com'), 5, 'These cookies are INCREDIBLE. Thick, chewy, and loaded with chocolate chunks. I always order a double batch.'),
(11, (SELECT id FROM users WHERE email='admin@bakenflake.com'), 4, 'Great vegan option! You''d never know they''re dairy-free. The hidden Oreo is such a fun surprise.'),
(13, (SELECT id FROM users WHERE email='demo@bakenflake.com'), 5, 'So fluffy and light! Love that they''re gluten-free. The coconut butter glaze is the perfect touch.'),
(17, (SELECT id FROM users WHERE email='demo@bakenflake.com'), 5, 'Triple chocolate banana bread is my weakness. Moist, chocolatey, and the banana flavor is perfectly balanced.'),
(21, (SELECT id FROM users WHERE email='admin@bakenflake.com'), 4, 'Beautiful artisan bread. The cranberry and walnut combination is classic. Great with cheese.'),
(14, (SELECT id FROM users WHERE email='demo@bakenflake.com'), 5, 'The rose petals make these donuts look and taste extraordinary. A truly unique treat!'),
(6, (SELECT id FROM users WHERE email='admin@bakenflake.com'), 5, 'The cinnamon brown sugar buttercream is addictive. This cake disappears fast at every gathering.');

-- Sample orders for demo purposes
INSERT INTO orders (user_id, customer_name, customer_address, customer_phone, subtotal, tax, shipping, total, status, created_at) VALUES
((SELECT id FROM users WHERE email='demo@bakenflake.com'), 'Sarah Johnson', '123 Baker St, Downtown NY', '555-0101', 52.30, 2.62, 5.00, 59.92, 'completed', DATE_SUB(NOW(), INTERVAL 15 DAY)),
((SELECT id FROM users WHERE email='demo@bakenflake.com'), 'Sarah Johnson', '123 Baker St, Downtown NY', '555-0101', 38.80, 1.94, 5.00, 45.74, 'confirmed', DATE_SUB(NOW(), INTERVAL 5 DAY)),
((SELECT id FROM users WHERE email='admin@bakenflake.com'), 'Admin Baker', '247 Bakers Lane, Downtown NY', '555-0202', 71.40, 3.57, 5.00, 79.97, 'completed', DATE_SUB(NOW(), INTERVAL 20 DAY)),
((SELECT id FROM users WHERE email='admin@bakenflake.com'), 'Admin Baker', '247 Bakers Lane, Downtown NY', '555-0202', 24.90, 1.25, 5.00, 31.15, 'pending', DATE_SUB(NOW(), INTERVAL 1 DAY));

-- Sample order items
INSERT INTO order_items (order_id, product_id, quantity, unit_price)
SELECT o.id, 1, 1, 24.90 FROM orders o WHERE o.total = 59.92 LIMIT 1;
INSERT INTO order_items (order_id, product_id, quantity, unit_price)
SELECT o.id, 9, 2, 8.90 FROM orders o WHERE o.total = 59.92 LIMIT 1;
INSERT INTO order_items (order_id, product_id, quantity, unit_price)
SELECT o.id, 13, 1, 5.50 FROM orders o WHERE o.total = 59.92 LIMIT 1;

INSERT INTO order_items (order_id, product_id, quantity, unit_price)
SELECT o.id, 6, 1, 18.90 FROM orders o WHERE o.total = 45.74 LIMIT 1;
INSERT INTO order_items (order_id, product_id, quantity, unit_price)
SELECT o.id, 11, 2, 9.90 FROM orders o WHERE o.total = 45.74 LIMIT 1;

INSERT INTO order_items (order_id, product_id, quantity, unit_price)
SELECT o.id, 3, 1, 28.00 FROM orders o WHERE o.total = 79.97 LIMIT 1;
INSERT INTO order_items (order_id, product_id, quantity, unit_price)
SELECT o.id, 17, 1, 12.90 FROM orders o WHERE o.total = 79.97 LIMIT 1;
INSERT INTO order_items (order_id, product_id, quantity, unit_price)
SELECT o.id, 21, 1, 11.90 FROM orders o WHERE o.total = 79.97 LIMIT 1;
INSERT INTO order_items (order_id, product_id, quantity, unit_price)
SELECT o.id, 6, 1, 18.90 FROM orders o WHERE o.total = 79.97 LIMIT 1;

INSERT INTO order_items (order_id, product_id, quantity, unit_price)
SELECT o.id, 1, 1, 24.90 FROM orders o WHERE o.total = 31.15 LIMIT 1;

-- Sample reservations
INSERT INTO reservations (user_id, name, people, reservation_date, message, status) VALUES
((SELECT id FROM users WHERE email='demo@bakenflake.com'), 'Sarah Johnson', 4, DATE_ADD(NOW(), INTERVAL 3 DAY), 'Birthday celebration, would love a window seat!', 'confirmed'),
(NULL, 'Mike Chen', 2, DATE_ADD(NOW(), INTERVAL 5 DAY), 'Anniversary dinner', 'pending'),
(NULL, 'Emma Williams', 8, DATE_ADD(NOW(), INTERVAL 7 DAY), 'Team lunch - any dietary options?', 'pending');

-- Newsletter subscribers
INSERT IGNORE INTO newsletter_subscribers (email) VALUES
('sarah@example.com'), ('mike@example.com'), ('emma@example.com'), ('baker.fan@example.com');
