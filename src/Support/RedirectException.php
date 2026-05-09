<?php

declare(strict_types=1);

namespace App\Support;

use RuntimeException;

final class RedirectException extends RuntimeException
{
    public function __construct(public readonly string $path)
    {
        parent::__construct('Redirect to ' . $path);
    }
}
