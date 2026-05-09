<?php

declare(strict_types=1);

function env(string $key, mixed $default = null): mixed
{
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? null;
    if ($value === null || $value === '') {
        return $default;
    }
    return $value;
}

function redirect(string $path): void
{
    if (defined('APP_TESTING') && APP_TESTING === true) {
        throw new \App\Support\RedirectException($path);
    }
    header('Location: ' . $path);
    exit;
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function auth_user_id(): ?int
{
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

function auth_role(): ?string
{
    return $_SESSION['role'] ?? null;
}

function is_admin(): bool
{
    return auth_role() === 'Admin';
}

function flash(string $key, ?string $value = null): ?string
{
    if ($value !== null) {
        $_SESSION['_flash'][$key] = $value;
        return null;
    }

    $message = $_SESSION['_flash'][$key] ?? null;
    if ($message !== null) {
        unset($_SESSION['_flash'][$key]);
    }
    return $message;
}
