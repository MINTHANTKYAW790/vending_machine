<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Middleware\RoleMiddleware;
use App\Repositories\TransactionRepository;

final class TransactionsController extends Controller
{
    public function __construct(private readonly TransactionRepository $transactions)
    {
    }

    public function index(Request $request): void
    {
        RoleMiddleware::ensureAnyRole(['Admin', 'User']);

        $userId = (int) auth_user_id();
        $isAdmin = is_admin();
        $rows = $isAdmin
            ? $this->transactions->findAllWithDetails()
            : $this->transactions->findByUserIdWithDetails($userId);

        $this->view('transactions.index', [
            'transactions' => $rows,
            'isAdmin' => $isAdmin,
        ]);
    }
}
