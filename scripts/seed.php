<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Services\Database;
use Dotenv\Dotenv;

if (file_exists(dirname(__DIR__) . '/.env')) {
    Dotenv::createImmutable(dirname(__DIR__))->safeLoad();
}

$config = require dirname(__DIR__) . '/config/database.php';
$db = new Database($config);
$pdo = $db->connection();

$seedFiles = [
    dirname(__DIR__) . '/database/seeds/001_seed_products.sql',
    dirname(__DIR__) . '/database/seeds/002_seed_admin.sql',
];

foreach ($seedFiles as $file) {
    $sql = file_get_contents($file);
    if ($sql === false) {
        throw new RuntimeException("Seed file not found: {$file}");
    }
    $pdo->exec($sql);
}

echo "Seeding completed.\n";
