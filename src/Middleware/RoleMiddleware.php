<?php

declare(strict_types=1);

namespace App\Middleware;

final class RoleMiddleware
{
    public static function ensureRole(string $role): void
    {
        self::ensureAnyRole([$role]);
    }

    public static function ensureAnyRole(array $roles): void
    {
        AuthMiddleware::ensureAuthenticated();
        $currentRole = auth_role();

        if ($currentRole === null || !in_array($currentRole, $roles, true)) {
            if (defined('APP_TESTING') && APP_TESTING === true) {
                throw new \RuntimeException('Forbidden');
            }
            http_response_code(403);
            echo 'Forbidden';
            exit;
        }
    }
}
