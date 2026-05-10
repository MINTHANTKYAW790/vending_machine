<?php

declare(strict_types=1);

use App\Controllers\Api\AuthApiController;
use App\Controllers\Api\ProductsApiController;
use App\Controllers\Api\TransactionsApiController;
use App\Controllers\Api\UsersApiController;
use App\Controllers\AuthController;
use App\Controllers\ProductsController;
use App\Controllers\TransactionsController;
use App\Controllers\UsersController;

return [
    ['GET', '/', [ProductsController::class, 'publicIndex']],
    ['GET', '/register', [AuthController::class, 'showRegister']],
    ['POST', '/register', [AuthController::class, 'register']],
    ['GET', '/login', [AuthController::class, 'showLogin']],
    ['POST', '/login', [AuthController::class, 'login']],
    ['POST', '/logout', [AuthController::class, 'logout']],

    ['GET', '/products', [ProductsController::class, 'index']],
    ['GET', '/transactions', [TransactionsController::class, 'index']],
    ['GET', '/products/create', [ProductsController::class, 'create']],
    ['POST', '/products', [ProductsController::class, 'store']],
    ['GET', '/products/{id}/edit', [ProductsController::class, 'edit']],
    ['POST', '/products/{id}/update', [ProductsController::class, 'update']],
    ['POST', '/products/{id}/delete', [ProductsController::class, 'destroy']],
    ['GET', '/products/{id}-{slug}', [ProductsController::class, 'show']],
    ['GET', '/products/{id}', [ProductsController::class, 'show']],
    ['GET', '/products/{id}/purchase', [ProductsController::class, 'showPurchase']],

    ['GET', '/users', [UsersController::class, 'index']],
    ['GET', '/users/create', [UsersController::class, 'create']],
    ['POST', '/users', [UsersController::class, 'store']],
    ['GET', '/users/{id}', [UsersController::class, 'show']],
    ['GET', '/users/{id}/edit', [UsersController::class, 'edit']],
    ['POST', '/users/{id}/update', [UsersController::class, 'update']],
    ['POST', '/users/{id}/delete', [UsersController::class, 'destroy']],

    ['POST', '/api/v1/auth/login', [AuthApiController::class, 'login']],
    ['GET', '/api/v1/products', [ProductsApiController::class, 'index']],
    ['GET', '/api/v1/products/{id}', [ProductsApiController::class, 'show']],
    ['POST', '/api/v1/products', [ProductsApiController::class, 'store']],
    ['PUT', '/api/v1/products/{id}', [ProductsApiController::class, 'update']],
    ['DELETE', '/api/v1/products/{id}', [ProductsApiController::class, 'destroy']],
    ['POST', '/api/v1/products/{id}/purchase', [ProductsApiController::class, 'purchase']],
    ['GET', '/api/v1/transactions', [TransactionsApiController::class, 'index']],
    ['GET', '/api/v1/transactions/{id}', [TransactionsApiController::class, 'show']],

    ['GET', '/api/v1/users', [UsersApiController::class, 'index']],
    ['GET', '/api/v1/users/{id}', [UsersApiController::class, 'show']],
    ['POST', '/api/v1/users', [UsersApiController::class, 'store']],
    ['PUT', '/api/v1/users/{id}', [UsersApiController::class, 'update']],
    ['DELETE', '/api/v1/users/{id}', [UsersApiController::class, 'destroy']],
];
