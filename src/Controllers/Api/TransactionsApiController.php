<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Controllers\Controller;
use App\Core\Request;
use App\Middleware\JwtMiddleware;
use App\Repositories\TransactionRepository;

final class TransactionsApiController extends Controller
{
    public function __construct(
        private readonly TransactionRepository $transactions,
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

    public function index(Request $request): void
    {
        $payload = $this->auth($request);
        if (!$payload) {
            return;
        }

        $isAdmin = ($payload['role'] ?? null) === 'Admin';
        $userId = (int) ($payload['sub'] ?? 0);

        $rows = $isAdmin
            ? $this->transactions->findAllWithDetails()
            : $this->transactions->findByUserIdWithDetails($userId);

        $this->json(['data' => $rows]);
    }

    public function show(Request $request, string $id): void
    {
        $payload = $this->auth($request);
        if (!$payload) {
            return;
        }

        $row = $this->transactions->findById((int) $id);
        if (!$row) {
            $this->json(['message' => 'Not found'], 404);
            return;
        }

        $isAdmin = ($payload['role'] ?? null) === 'Admin';
        $currentUserId = (int) ($payload['sub'] ?? 0);
        if (!$isAdmin && (int) $row['user_id'] !== $currentUserId) {
            $this->json(['message' => 'Forbidden'], 403);
            return;
        }

        $this->json(['data' => $row]);
    }
}
