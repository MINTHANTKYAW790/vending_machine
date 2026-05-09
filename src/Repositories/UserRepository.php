<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Services\Database;
use PDO;
use PDOException;

final class UserRepository
{
    private PDO $pdo;
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->pdo = $database->connection();
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
        $stmt->execute(['username' => $username]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM users ORDER BY id ASC');
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $executeInsert = function () use ($data): void {
            $stmt = $this->pdo->prepare(
                'INSERT INTO users (username, email, password_hash, role, created_at, updated_at)
                 VALUES (:username, :email, :password_hash, :role, NOW(), NOW())'
            );
            $stmt->execute([
                'username' => $data['username'],
                'email' => $data['email'],
                'password_hash' => $data['password_hash'],
                'role' => $data['role'] ?? 'User',
            ]);
        };

        try {
            $executeInsert();
        } catch (PDOException $exception) {
            if (!$this->isConnectionLost($exception)) {
                throw $exception;
            }

            $this->pdo = $this->database->reconnect();
            $executeInsert();
        }

        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE users
             SET username = :username,
                 email = :email,
                 password_hash = :password_hash,
                 role = :role,
                 updated_at = NOW()
             WHERE id = :id'
        );

        return $stmt->execute([
            'id' => $id,
            'username' => $data['username'],
            'email' => $data['email'],
            'password_hash' => $data['password_hash'],
            'role' => $data['role'] ?? 'User',
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    private function isConnectionLost(PDOException $exception): bool
    {
        $driverCode = isset($exception->errorInfo[1]) ? (int) $exception->errorInfo[1] : 0;
        if (in_array($driverCode, [2006, 2013], true)) {
            return true;
        }

        $message = strtolower($exception->getMessage());
        return str_contains($message, 'server has gone away')
            || str_contains($message, 'lost connection');
    }

    public function isUniqueConstraintViolation(PDOException $exception): bool
    {
        $driverCode = isset($exception->errorInfo[1]) ? (int) $exception->errorInfo[1] : 0;
        return $driverCode === 1062;
    }
}
