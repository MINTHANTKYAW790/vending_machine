<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Services\Database;
use PDO;

final class ProductRepository implements ProductRepositoryInterface
{
    private PDO $pdo;

    public function __construct(Database $database)
    {
        $this->pdo = $database->connection();
    }

    public function paginate(int $page, int $perPage, string $sort, string $dir): array
    {
        $allowedSort = ['id', 'name', 'price', 'quantity_available', 'created_at'];
        if (!in_array($sort, $allowedSort, true)) {
            $sort = 'id';
        }

        $dir = strtoupper($dir) === 'DESC' ? 'DESC' : 'ASC';
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT * FROM products ORDER BY {$sort} {$dir} LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countAll(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM products WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO products (name, price, quantity_available, created_at, updated_at)
             VALUES (:name, :price, :quantity_available, NOW(), NOW())'
        );
        $stmt->execute([
            'name' => $data['name'],
            'price' => $data['price'],
            'quantity_available' => $data['quantity_available'],
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE products
             SET name = :name, price = :price, quantity_available = :quantity_available, updated_at = NOW()
             WHERE id = :id'
        );
        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'price' => $data['price'],
            'quantity_available' => $data['quantity_available'],
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM products WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function decrementQuantity(int $id, int $amount): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE products SET quantity_available = quantity_available - :decrement_amount, updated_at = NOW()
             WHERE id = :id AND quantity_available >= :required_amount'
        );
        return $stmt->execute([
            'id' => $id,
            'decrement_amount' => $amount,
            'required_amount' => $amount,
        ]) && $stmt->rowCount() === 1;
    }
}
