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

$sql = file_get_contents(dirname(__DIR__) . '/database/migrations/001_create_tables.sql');
if ($sql === false) {
    throw new RuntimeException('Migration file not found.');
}

$pdo->exec($sql);
echo "Migration completed.\n";
