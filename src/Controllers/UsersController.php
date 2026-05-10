<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Middleware\RoleMiddleware;
use App\Repositories\UserRepository;
use App\Services\Database;
use App\Services\UserValidator;
use PDOException;

final class UsersController extends Controller
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly UserValidator $validator
    ) {
    }

    public function index(Request $request): void
    {
        \App\Middleware\RoleMiddleware::ensureRole('Admin');

        $users = $this->users->findAll();

        $this->view('users.index', [
            'users' => $users,
            'flashSuccess' => flash('success'),
            'flashError' => flash('error'),
        ]);
    }

    public function show(Request $request, string $id): void
    {
        \App\Middleware\RoleMiddleware::ensureRole('Admin');
        $user = $this->users->findById((int) $id);
        if (!$user) {
            http_response_code(404);
            echo 'User not found';
            return;
        }

        $this->view('users.show', ['user' => $user]);
    }

    public function create(): void
    {
        \App\Middleware\RoleMiddleware::ensureRole('Admin');
        $this->view('users.create', [
            'errors' => [],
            'old' => ['username' => '', 'email' => '', 'password' => '', 'role' => 'User'],
        ]);
    }

    public function store(Request $request): void
    {
        \App\Middleware\RoleMiddleware::ensureRole('Admin');
        $input = [
            'username' => trim((string) $request->input('username')),
            'email' => trim((string) $request->input('email')),
            'password' => (string) $request->input('password'),
            'role' => (string) $request->input('role', 'User'),
        ];

        $errors = $this->validator->validateCreate($input);
        if ($errors !== []) {
            $this->view('users.create', ['errors' => $errors, 'old' => $input]);
            return;
        }

        // Check uniqueness
        if ($this->users->findByUsername($input['username'])) {
            $errors['username'] = 'Username already exists.';
        }
        if ($this->users->findByEmail($input['email'])) {
            $errors['email'] = 'Email already exists.';
        }
        if ($errors !== []) {
            $this->view('users.create', ['errors' => $errors, 'old' => $input]);
            return;
        }

        try {
            $this->users->create([
                'username' => $input['username'],
                'email' => $input['email'],
                'password_hash' => password_hash($input['password'], PASSWORD_DEFAULT),
                'role' => $input['role'],
            ]);
            flash('success', 'User created successfully.');
            redirect('/users');
        } catch (PDOException $e) {
            if ($this->users->isUniqueConstraintViolation($e)) {
                flash('error', 'Username or email already exists.');
                $this->view('users.create', ['errors' => [], 'old' => $input]);
            } else {
                throw $e;
            }
        }
    }

    public function edit(Request $request, string $id): void
    {
        \App\Middleware\RoleMiddleware::ensureRole('Admin');
        $user = $this->users->findById((int) $id);
        if (!$user) {
            http_response_code(404);
            echo 'User not found';
            return;
        }
        $this->view('users.edit', ['user' => $user, 'errors' => []]);
    }

    public function update(Request $request, string $id): void
    {
        \App\Middleware\RoleMiddleware::ensureRole('Admin');
        $userId = (int) $id;
        $existingUser = $this->users->findById($userId);
        if (!$existingUser) {
            http_response_code(404);
            echo 'User not found';
            return;
        }

        $input = [
            'username' => trim((string) $request->input('username')),
            'email' => trim((string) $request->input('email')),
            'password' => (string) $request->input('password'),
            'role' => (string) $request->input('role', 'User'),
        ];

        $errors = $this->validator->validateUpdate($input);
        if ($errors !== []) {
            $user = array_merge($existingUser, $input);
            $this->view('users.edit', ['user' => $user, 'errors' => $errors]);
            return;
        }

        // Check uniqueness, excluding current user
        if ($this->users->findByUsername($input['username']) && $input['username'] !== $existingUser['username']) {
            $errors['username'] = 'Username already exists.';
        }
        if ($this->users->findByEmail($input['email']) && $input['email'] !== $existingUser['email']) {
            $errors['email'] = 'Email already exists.';
        }
        if ($errors !== []) {
            $user = array_merge($existingUser, $input);
            $this->view('users.edit', ['user' => $user, 'errors' => $errors]);
            return;
        }

        $updateData = [
            'username' => $input['username'],
            'email' => $input['email'],
            'role' => $input['role'],
        ];
        if ($input['password'] !== '') {
            $updateData['password_hash'] = password_hash($input['password'], PASSWORD_DEFAULT);
        } else {
            $updateData['password_hash'] = $existingUser['password_hash'];
        }

        try {
            $this->users->update($userId, $updateData);
            flash('success', 'User updated successfully.');
            redirect('/users');
        } catch (PDOException $e) {
            if ($this->users->isUniqueConstraintViolation($e)) {
                flash('error', 'Username or email already exists.');
                $user = array_merge($existingUser, $input);
                $this->view('users.edit', ['user' => $user, 'errors' => []]);
            } else {
                throw $e;
            }
        }
    }

    public function destroy(Request $request, string $id): void
    {
        \App\Middleware\RoleMiddleware::ensureRole('Admin');
        $userId = (int) $id;
        $user = $this->users->findById($userId);
        if (!$user) {
            flash('error', 'User not found.');
            redirect('/users');
            return;
        }
        if ($user['id'] === auth_user_id()) {
            flash('error', 'Cannot delete your own account.');
            redirect('/users');
            return;
        }
        $this->users->delete($userId);
        flash('success', 'User deleted successfully.');
        redirect('/users');
    }
}