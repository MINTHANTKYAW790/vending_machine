<?php

declare(strict_types=1);

namespace App\Services;

class UserValidator
{
    public function validateCreate(array $input): array
    {
        $errors = [];

        $username = trim((string) ($input['username'] ?? ''));
        if ($username === '') {
            $errors['username'] = 'Username is required.';
        } elseif (strlen($username) < 3) {
            $errors['username'] = 'Username must be at least 3 characters.';
        }

        $email = trim((string) ($input['email'] ?? ''));
        if ($email === '') {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email must be a valid email address.';
        }

        $password = (string) ($input['password'] ?? '');
        if ($password === '') {
            $errors['password'] = 'Password is required.';
        } elseif (strlen($password) < 6) {
            $errors['password'] = 'Password must be at least 6 characters.';
        }

        $role = (string) ($input['role'] ?? 'User');
        if (!in_array($role, ['User', 'Admin'], true)) {
            $errors['role'] = 'Role must be User or Admin.';
        }

        return $errors;
    }

    public function validateUpdate(array $input): array
    {
        $errors = [];

        $username = trim((string) ($input['username'] ?? ''));
        if ($username === '') {
            $errors['username'] = 'Username is required.';
        } elseif (strlen($username) < 3) {
            $errors['username'] = 'Username must be at least 3 characters.';
        }

        $email = trim((string) ($input['email'] ?? ''));
        if ($email === '') {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email must be a valid email address.';
        }

        $password = (string) ($input['password'] ?? '');
        if ($password !== '' && strlen($password) < 6) {
            $errors['password'] = 'Password must be at least 6 characters.';
        }

        $role = (string) ($input['role'] ?? 'User');
        if (!in_array($role, ['User', 'Admin'], true)) {
            $errors['role'] = 'Role must be User or Admin.';
        }

        return $errors;
    }
}