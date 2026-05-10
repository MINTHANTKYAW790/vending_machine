<?php

declare(strict_types=1);

namespace App\Core;

use App\Controllers\Api\AuthApiController;
use App\Controllers\Api\ProductsApiController;
use App\Controllers\Api\TransactionsApiController;
use App\Controllers\Api\UsersApiController;
use App\Controllers\AuthController;
use App\Controllers\ProductsController;
use App\Controllers\TransactionsController;
use App\Controllers\UsersController;
use App\Repositories\ProductRepository;
use App\Repositories\ProductRepositoryInterface;
use App\Repositories\UserRepository;
use App\Services\Database;
use Throwable;

final class Application
{
    public function __construct(
        private readonly array $appConfig,
        private readonly array $databaseConfig,
        private readonly array $routes
    ) {
    }

    public function run(): void
    {
        session_name($this->appConfig['session_name']);
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        Container::set(Database::class, new Database($this->databaseConfig));
        Container::bind(ProductRepositoryInterface::class, ProductRepository::class);
        Container::bind(UserRepository::class, UserRepository::class);
        $router = new Router();
        $router->register($this->routes);
        $router->registerAttributeRoutes([
            ProductsController::class,
            AuthController::class,
            TransactionsController::class,
            AuthApiController::class,
            ProductsApiController::class,
            TransactionsApiController::class,
            UsersController::class,
            UsersApiController::class,
        ]);

        try {
            $router->dispatch(Request::capture());
        } catch (Throwable $exception) {
            http_response_code(500);
            if ($this->appConfig['debug']) {
                echo '<pre>' . e($exception->getMessage()) . "\n" . e($exception->getTraceAsString()) . '</pre>';
                return;
            }
            echo 'Server error';
        }
    }
}
