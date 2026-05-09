<?php

declare(strict_types=1);

return [
    'name' => env('APP_NAME', 'VendingMachine'),
    'env' => env('APP_ENV', 'production'),
    'debug' => filter_var(env('APP_DEBUG', false), FILTER_VALIDATE_BOOL),
    'url' => env('APP_URL', 'http://localhost'),
    'session_name' => env('SESSION_NAME', 'vending_machine_session'),
];
