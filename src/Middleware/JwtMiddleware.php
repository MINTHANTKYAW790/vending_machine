<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Services\JwtService;
use Throwable;

final class JwtMiddleware
{
    public function __construct(private readonly JwtService $jwtService)
    {
    }

    public function authenticate(Request $request): ?array
    {
        $header = $request->header('Authorization');
        if (!str_starts_with($header, 'Bearer ')) {
            return null;
        }

        $token = trim(substr($header, 7));
        if ($token === '') {
            return null;
        }

        try {
            return $this->jwtService->decodeToken($token);
        } catch (Throwable) {
            return null;
        }
    }
}
