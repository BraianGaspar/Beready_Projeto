<?php

declare(strict_types=1);

namespace App\Contracts;

interface UserRepositoryInterface
{
    public function findById(int $id): ?array;
    public function findByEmail(string $email): ?array;

    /**
     * @param array $data Campos atribuíveis em massa (ver User::$_accessible)
     * @param array $protected Campos protegidos (senha_hash, role, status...) definidos explicitamente
     */
    public function create(array $data, array $protected = []): array;

    /**
     * @param array $data Campos atribuíveis em massa (ver User::$_accessible)
     * @param array $protected Campos protegidos (senha_hash, role, status...) definidos explicitamente
     */
    public function update(int $id, array $data, array $protected = []): array;

    public function delete(int $id): bool;
    public function emailExists(string $email, ?int $excludeId = null): bool;
    public function findByUuid(string $uuid): ?array;
    public function findByResetTokenHash(string $tokenHash): ?array;
    public function updateResetToken(int $id, ?string $tokenHash, ?string $expires): bool;
}
