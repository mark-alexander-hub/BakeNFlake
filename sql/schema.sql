CREATE DATABASE IF NOT EXISTS bakenflake;
USE bakenflake;

-- Users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(8,2) NOT NULL,
    category ENUM('cakes','cookies','donuts','bread') NOT NULL,
    image VARCHAR(100) NOT NULL,
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    customer_name VARCHAR(100) NOT NULL,
    customer_address TEXT NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    notes TEXT,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pending','confirmed','completed','cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Order items
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    unit_price DECIMAL(8,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Reservations
CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(100) NOT NULL,
    people INT NOT NULL,
    reservation_date DATETIME NOT NULL,
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Seed products
INSERT INTO products (name, description, price, category, image) VALUES
('Blackberry Lavender Naked Cake', 'Four layers of vanilla cake with blackberry lavender jam and white chocolate buttercream.', 24.90, 'cakes', 'm1.jpg'),
('Hazelnut Fig Cake', 'Rich hazelnut cake layered with fresh fig compote and mascarpone cream.', 22.50, 'cakes', 'm2.jpg'),
('Mulled Wine Chocolate Cake', 'Chocolate cake with poached pears and mulled wine Swiss meringue buttercream.', 28.00, 'cakes', 'm3.jpg'),
('Peach Carrot Cake', 'Peach carrot cake with cream cheese frosting and candied walnuts.', 19.90, 'cakes', 'm4.jpg'),
('Chocolate Almond Pear Cake', 'Dark chocolate and almond cake with caramelized pears.', 23.50, 'cakes', 'm5.jpg'),
('Banana Cake', 'Banana cake with cinnamon brown sugar buttercream and toasted pecans.', 18.90, 'cakes', 'm6.jpg'),
('Winter Fruit Pavlova Roulade', 'Crispy meringue roulade with seasonal winter fruits and chantilly cream.', 21.00, 'cakes', 'm7.jpg'),
('French Chocolate Coffee Cake', 'Rich French-style chocolate and espresso layer cake with ganache drip.', 26.50, 'cakes', 'm8.jpg'),
('Dark Chocolate Chunk Oatmeal Coconut Cookies', 'Thick oatmeal coconut cookies with bittersweet chocolate chunks.', 8.90, 'cookies', 'm9.jpg'),
('Almond Milk Chocolate Chip Cookies', 'Chewy almond milk cookies with Belgian milk chocolate chips.', 7.50, 'cookies', 'm10.jpg'),
('Vegan Oreo Stuffed Chocolate Chip Cookies', 'Indulgent chocolate chip cookies stuffed with whole Oreo cookies.', 9.90, 'cookies', 'm11.jpg'),
('Jam Drop Cookies al Farro', 'Farro flour cookies with artisanal jam centers and powdered sugar.', 6.90, 'cookies', 'm12.jpg'),
('Coconut Flour Donuts', 'Fluffy fruit-sweetened coconut flour donuts with coconut butter glaze.', 5.50, 'donuts', 'm13.jpg'),
('Baked Almond Flour Donuts', 'Almond flour donuts with rose petals and dark chocolate drizzle.', 6.90, 'donuts', 'm14.jpg'),
('Eggnog Glazed Donuts', 'Seasonal donuts with silky eggnog glaze and nutmeg dusting.', 5.90, 'donuts', 'm15.jpg'),
('Red Velvet Donuts', 'Naturally dyed beetroot donuts with Greek yogurt frosting.', 6.50, 'donuts', 'm16.jpg'),
('Triple Chocolate Banana Bread', 'Banana bread loaded with dark, milk, and white chocolate chunks.', 12.90, 'bread', 'm17.jpg'),
('Flax and Oats Wheat Bread', 'Hearty wheat bread with golden flax seeds and rolled oats.', 8.50, 'bread', 'm18.jpg'),
('Carrot Cardamom Cake', 'Spiced carrot cake with aromatic cardamom and cream cheese glaze.', 14.90, 'bread', 'm19.jpg'),
('Cinnamon Almond Flour Bread', 'Grain-free bread with warm cinnamon and almond flour.', 10.50, 'bread', 'm20.jpg'),
('Cranberry Honey Walnut Artisan Bread', 'No-knead artisan bread with cranberries, raw honey, and walnuts.', 11.90, 'bread', 'm21.jpg'),
('Coriander Garlic Flat Bread', 'Herbed flatbread with fresh coriander and roasted garlic.', 7.90, 'bread', 'm22.jpg');
