INSERT INTO products (name, price, quantity_available, created_at, updated_at)
VALUES
    ('Coke', 3.990, 100, NOW(), NOW()),
    ('Pepsi', 6.885, 100, NOW(), NOW()),
    ('Water', 0.500, 100, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    price = VALUES(price),
    updated_at = NOW();
