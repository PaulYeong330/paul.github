-- Create database
CREATE DATABASE IF NOT EXISTS simple_ecommerce;
USE simple_ecommerce;

-- Create products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255) NOT NULL,
    stock INT NOT NULL DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_product_name (name)
);

-- Create orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_address TEXT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered') DEFAULT 'pending',
    INDEX idx_customer_email (customer_email),
    INDEX idx_order_date (order_date)
);

-- Create order_items table for order details
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
    INDEX idx_order_id (order_id),
    INDEX idx_product_id (product_id)
);

-- Insert sample products
INSERT INTO products (name, description, price, image, stock) VALUES
('Smartphone', 'Latest smartphone with advanced features', 599.99, 'images/smartphone.jpg', 20),
('Laptop', 'High-performance laptop for work and gaming', 999.99, 'images/laptop.jpg', 15),
('Wireless Earbuds', 'Premium wireless earbuds with noise cancellation', 149.99, 'images/earbuds.jpg', 30),
('Smart Watch', 'Fitness and health tracking smart watch', 199.99, 'images/smartwatch.jpg', 25),
('Tablet', 'Lightweight tablet with high-resolution display', 299.99, 'images/tablet.jpg', 18);