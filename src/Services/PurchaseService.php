<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Repositories\TransactionRepository;
use PDO;
use RuntimeException;

class PurchaseService
{
    private PDO $pdo;

    public function __construct(
        Database $database,
        private readonly ProductRepository $products,
        private readonly TransactionRepository $transactions
    ) {
        $this->pdo = $database->connection();
    }

    public function purchase(int $userId, int $productId, int $quantity): array
    {
        if ($quantity <= 0) {
            throw new RuntimeException('Purchase quantity must be positive.');
        }

        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM products WHERE id = :id FOR UPDATE');
            $stmt->execute(['id' => $productId]);
            $product = $stmt->fetch();
            if (!$product) {
                throw new RuntimeException('Product not found.');
            }

            if ((int) $product['quantity_available'] < $quantity) {
                throw new RuntimeException('Insufficient stock.');
            }

            $ok = $this->products->decrementQuantity($productId, $quantity);
            if (!$ok) {
                throw new RuntimeException('Failed to update inventory.');
            }

            $unitPrice = (float) $product['price'];
            $totalPrice = $unitPrice * $quantity;
            $txRef = 'TX-' . date('YmdHis') . '-' . random_int(1000, 9999);
            $this->transactions->create([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'unit_price' => number_format($unitPrice, 3, '.', ''),
                'total_price' => number_format($totalPrice, 3, '.', ''),
                'transaction_ref' => $txRef,
            ]);

            $this->pdo->commit();

            return [
                'transaction_ref' => $txRef,
                'product_name' => $product['name'],
                'quantity' => $quantity,
                'total_price' => number_format($totalPrice, 3, '.', ''),
            ];
        } catch (\Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $exception;
        }
    }
}
