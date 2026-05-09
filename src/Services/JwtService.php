<?php

declare(strict_types=1);

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

final class JwtService
{
    public function issueToken(array $user): string
    {
        $now = time();
        $payload = [
            'iss' => env('JWT_ISSUER', 'vending-machine'),
            'iat' => $now,
            'exp' => $now + (int) env('JWT_TTL', 3600),
            'sub' => (int) $user['id'],
            'role' => $user['role'],
            'username' => $user['username'],
        ];

        return JWT::encode($payload, (string) env('JWT_SECRET', 'secret'), 'HS256');
    }

    public function decodeToken(string $token): array
    {
        $decoded = JWT::decode($token, new Key((string) env('JWT_SECRET', 'secret'), 'HS256'));
        return (array) $decoded;
    }
}
