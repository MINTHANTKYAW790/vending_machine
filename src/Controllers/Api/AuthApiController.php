<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Controllers\Controller;
use App\Core\Request;
use App\Repositories\UserRepository;
use App\Services\JwtService;

final class AuthApiController extends Controller
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly JwtService $jwtService
    ) {
    }

    public function login(Request $request): void
    {
        $username = trim((string) $request->input('username'));
        $password = (string) $request->input('password');

        $user = $this->users->findByUsername($username);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->json(['message' => 'Invalid credentials'], 401);
            return;
        }

        $token = $this->jwtService->issueToken($user);
        $this->json([
            'token' => $token,
            'user' => [
                'id' => (int) $user['id'],
                'username' => $user['username'],
                'role' => $user['role'],
            ],
        ]);
    }
}
