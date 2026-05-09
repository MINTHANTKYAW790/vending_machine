<?php

declare(strict_types=1);

use App\Core\Application;
use Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

if (file_exists(dirname(__DIR__) . '/.env')) {
    Dotenv::createImmutable(dirname(__DIR__))->safeLoad();
}

$app = new Application(
    require dirname(__DIR__) . '/config/app.php',
    require dirname(__DIR__) . '/config/database.php',
    require dirname(__DIR__) . '/config/routes.php',
);

$app->run();
