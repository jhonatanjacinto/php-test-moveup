CREATE DATABASE IF NOT EXISTS db_products;

USE db_products;

CREATE TABLE IF NOT EXISTS tbl_products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  price DECIMAL(10, 2),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO tbl_products (name, description, price) VALUES
('Product 1', 'Description for product 1', 10.50),
('Product 2', 'Description for product 2', 20.00),
('Product 3', 'Description for product 3', 30.99);
