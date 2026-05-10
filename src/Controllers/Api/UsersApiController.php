<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Controllers\Controller;
use App\Core\Request;
use App\Middleware\JwtMiddleware;
use App\Repositories\UserRepository;
use App\Services\UserValidator;
use PDOException;

final class UsersApiController extends Controller
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly UserValidator $validator,
        private readonly JwtMiddleware $jwtMiddleware
    ) {
    }

    private function auth(Request $request): ?array
    {
        $payload = $this->jwtMiddleware->authenticate($request);
        if (!$payload) {
            $this->json(['message' => 'Unauthorized'], 401);
            return null;
        }
        return $payload;
    }

    private function requireAdmin(array $payload): bool
    {
        if (($payload['role'] ?? null) !== 'Admin') {
            $this->json(['message' => 'Forbidden'], 403);
            return false;
        }
        return true;
    }

    public function index(Request $request): void
    {
        $payload = $this->auth($request);
        if (!$payload || !$this->requireAdmin($payload)) {
            return;
        }
        $users = $this->users->findAll();
        // Remove password_hash from response
        $users = array_map(function ($user) {
            unset($user['password_hash']);
            return $user;
        }, $users);
        $this->json(['data' => $users]);
    }

    public function show(Request $request, string $id): void
    {
        $payload = $this->auth($request);
        if (!$payload || !$this->requireAdmin($payload)) {
            return;
        }
        $user = $this->users->findById((int) $id);
        if (!$user) {
            $this->json(['message' => 'Not found'], 404);
            return;
        }
        unset($user['password_hash']);
        $this->json(['data' => $user]);
    }

    public function store(Request $request): void
    {
        $payload = $this->auth($request);
        if (!$payload || !$this->requireAdmin($payload)) {
            return;
        }

        $data = $request->all();
        $errors = $this->validator->validateCreate($data);
        if ($errors) {
            $this->json(['errors' => $errors], 422);
            return;
        }

        // Check uniqueness
        if ($this->users->findByUsername($data['username'])) {
            $errors['username'] = 'Username already exists.';
        }
        if ($this->users->findByEmail($data['email'])) {
            $errors['email'] = 'Email already exists.';
        }
        if ($errors) {
            $this->json(['errors' => $errors], 422);
            return;
        }

        try {
            $id = $this->users->create([
                'username' => $data['username'],
                'email' => $data['email'],
                'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
                'role' => $data['role'] ?? 'User',
            ]);
            $this->json(['id' => $id], 201);
        } catch (PDOException $e) {
            if ($this->users->isUniqueConstraintViolation($e)) {
                $this->json(['errors' => ['username' => 'Username or email already exists.']], 422);
            } else {
                throw $e;
            }
        }
    }

    public function update(Request $request, string $id): void
    {
        $payload = $this->auth($request);
        if (!$payload || !$this->requireAdmin($payload)) {
            return;
        }

        $userId = (int) $id;
        $existingUser = $this->users->findById($userId);
        if (!$existingUser) {
            $this->json(['message' => 'Not found'], 404);
            return;
        }

        $data = $request->all();
        $errors = $this->validator->validateUpdate($data);
        if ($errors) {
            $this->json(['errors' => $errors], 422);
            return;
        }

        // Check uniqueness, excluding current
        if ($this->users->findByUsername($data['username']) && $data['username'] !== $existingUser['username']) {
            $errors['username'] = 'Username already exists.';
        }
        if ($this->users->findByEmail($data['email']) && $data['email'] !== $existingUser['email']) {
            $errors['email'] = 'Email already exists.';
        }
        if ($errors) {
            $this->json(['errors' => $errors], 422);
            return;
        }

        $updateData = [
            'username' => $data['username'],
            'email' => $data['email'],
            'role' => $data['role'] ?? 'User',
        ];
        if (!empty($data['password'])) {
            $updateData['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            $updateData['password_hash'] = $existingUser['password_hash'];
        }

        try {
            $this->users->update($userId, $updateData);
            $this->json(['message' => 'Updated']);
        } catch (PDOException $e) {
            if ($this->users->isUniqueConstraintViolation($e)) {
                $this->json(['errors' => ['username' => 'Username or email already exists.']], 422);
            } else {
                throw $e;
            }
        }
    }

    public function destroy(Request $request, string $id): void
    {
        $payload = $this->auth($request);
        if (!$payload || !$this->requireAdmin($payload)) {
            return;
        }
        $userId = (int) $id;
        $user = $this->users->findById($userId);
        if (!$user) {
            $this->json(['message' => 'Not found'], 404);
            return;
        }
        if ($user['id'] === (int) $payload['sub']) {
            $this->json(['message' => 'Cannot delete your own account'], 422);
            return;
        }
        $this->users->delete($userId);
        $this->json(['message' => 'Deleted']);
    }
}