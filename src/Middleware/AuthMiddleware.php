<?php

declare(strict_types=1);

namespace App\Middleware;

final class AuthMiddleware
{
    public static function ensureAuthenticated(): void
    {
        if (auth_user_id() === null) {
            flash('error', 'Please log in first.');
            redirect('/login');
        }
    }
}
