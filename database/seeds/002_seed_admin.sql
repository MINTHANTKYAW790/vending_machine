INSERT INTO users (username, email, password_hash, role, created_at, updated_at)
VALUES (
    'admin',
    'admin@example.com',
    '$2y$10$5jR6bpMIjtJDQrl.rfu4LuUnMsPlRe9NsXZ53iNg0Zmlhpyw.Snui',
    'Admin',
    NOW(),
    NOW()
)
ON DUPLICATE KEY UPDATE
    role = 'Admin',
    updated_at = NOW();
