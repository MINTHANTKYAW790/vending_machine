<?php

declare(strict_types=1);

namespace App\Repositories;

interface ProductRepositoryInterface
{
    public function paginate(int $page, int $perPage, string $sort, string $dir): array;

    public function countAll(): int;

    public function findById(int $id): ?array;

    public function create(array $data): int;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function decrementQuantity(int $id, int $amount): bool;
}
