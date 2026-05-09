<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    public static function render(string $view, array $data = []): void
    {
        $basePath = dirname(__DIR__) . '/Views/';
        $file = $basePath . str_replace('.', '/', $view) . '.php';

        if (!file_exists($file)) {
            http_response_code(404);
            echo 'View not found';
            return;
        }

        extract($data, EXTR_SKIP);
        require $basePath . 'layouts/header.php';
        require $file;
        require $basePath . 'layouts/footer.php';
    }
}
