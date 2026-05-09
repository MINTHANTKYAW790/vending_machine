CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'User') NOT NULL DEFAULT 'User',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL UNIQUE,
    price DECIMAL(10,3) NOT NULL CHECK (price > 0),
    quantity_available INT NOT NULL CHECK (quantity_available >= 0),
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS transactions (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL CHECK (quantity > 0),
    unit_price DECIMAL(10,3) NOT NULL,
    total_price DECIMAL(12,3) NOT NULL,
    transaction_ref VARCHAR(50) NOT NULL UNIQUE,
    created_at DATETIME NOT NULL,
    CONSTRAINT fk_transactions_user FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT fk_transactions_product FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE INDEX idx_transactions_user_created_at ON transactions(user_id, created_at);
CREATE INDEX idx_transactions_product_created_at ON transactions(product_id, created_at);
CREATE INDEX idx_products_name ON products(name);
