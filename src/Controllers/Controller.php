<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Response;

abstract class Controller
{
    protected function view(string $view, array $data = []): void
    {
        Response::view($view, $data);
    }

    protected function json(array $data, int $status = 200): void
    {
        Response::json($data, $status);
    }
}
