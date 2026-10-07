<?php

declare(strict_types=1);

namespace App\Contracts;

interface FlashcardRepositoryInterface
{
    public function findByUsuarioId(int $usuarioId): array;
    public function findById(int $id): ?array;
    public function findByUuid(string $uuid): ?array;
    public function create(array $data): array;
    public function update(int $id, array $data): array;
    public function delete(int $id): bool;

    /**
     * Flashcards do usuário com revisão vencida (proxima_revisao <= $ate), mais antigos primeiro.
     */
    public function findDevidos(int $usuarioId, \DateTimeInterface $ate, ?int $limite = null): array;

    public function countDevidos(int $usuarioId, \DateTimeInterface $ate): int;

    /**
     * Grava os campos de agendamento (fora do _accessible: o corpo das requisições não os altera).
     */
    public function salvarAgendamento(int $id, array $agendamento): array;
}
