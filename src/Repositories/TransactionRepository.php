<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Services\Database;
use PDO;

final class TransactionRepository
{
    private PDO $pdo;

    public function __construct(Database $database)
    {
        $this->pdo = $database->connection();
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO transactions (
                user_id, product_id, quantity, unit_price, total_price, transaction_ref, created_at
            ) VALUES (
                :user_id, :product_id, :quantity, :unit_price, :total_price, :transaction_ref, NOW()
            )'
        );

        $stmt->execute([
            'user_id' => $data['user_id'],
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
            'unit_price' => $data['unit_price'],
            'total_price' => $data['total_price'],
            'transaction_ref' => $data['transaction_ref'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM transactions WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM transactions ORDER BY created_at DESC, id DESC');
        return $stmt->fetchAll();
    }

    public function findAllWithDetails(): array
    {
        $stmt = $this->pdo->query(
            'SELECT
                t.*,
                u.username AS username,
                p.name AS product_name
             FROM transactions t
             INNER JOIN users u ON u.id = t.user_id
             INNER JOIN products p ON p.id = t.product_id
             ORDER BY t.created_at DESC, t.id DESC'
        );
        return $stmt->fetchAll();
    }

    public function findByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM transactions WHERE user_id = :user_id ORDER BY created_at DESC, id DESC'
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function findByUserIdWithDetails(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT
                t.*,
                u.username AS username,
                p.name AS product_name
             FROM transactions t
             INNER JOIN users u ON u.id = t.user_id
             INNER JOIN products p ON p.id = t.product_id
             WHERE t.user_id = :user_id
             ORDER BY t.created_at DESC, t.id DESC'
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE transactions
             SET user_id = :user_id,
                 product_id = :product_id,
                 quantity = :quantity,
                 unit_price = :unit_price,
                 total_price = :total_price,
                 transaction_ref = :transaction_ref
             WHERE id = :id'
        );

        return $stmt->execute([
            'id' => $id,
            'user_id' => $data['user_id'],
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
            'unit_price' => $data['unit_price'],
            'total_price' => $data['total_price'],
            'transaction_ref' => $data['transaction_ref'],
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM transactions WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
}
