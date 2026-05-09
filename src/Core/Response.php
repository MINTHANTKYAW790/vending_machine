<?php

declare(strict_types=1);

namespace App\Core;

final class Response
{
    public static function view(string $view, array $data = []): void
    {
        View::render($view, $data);
    }

    public static function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
