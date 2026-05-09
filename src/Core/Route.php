<?php

declare(strict_types=1);

namespace App\Core;

#[\Attribute(\Attribute::TARGET_METHOD)]
final class Route
{
    public function __construct(
        public readonly string $method,
        public readonly string $path,
        public readonly string $name = ''
    ) {
    }
}
