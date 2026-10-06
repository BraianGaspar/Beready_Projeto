<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\PromptRepositoryInterface;

class PromptService
{
    private PromptRepositoryInterface $repository;

    public function __construct(PromptRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getPromptById(int $id): array
    {
        $prompt = $this->repository->findById($id);
        if (!$prompt) {
            throw new \RuntimeException('Prompt não encontrado', 404);
        }
        return $prompt;
    }

    public function getPromptsByUsuario(int $usuarioId): array
    {
        return $this->repository->findByUsuarioId($usuarioId);
    }

    public function createPrompt(int $usuarioId, array $data): array
    {
        if (empty($data['texto_original'])) {
            throw new \InvalidArgumentException('Texto original é obrigatório');
        }

        $data['usuario_id'] = $usuarioId;

        return $this->repository->create($data);
    }

    /**
     * O chamador deve validar o dono antes (getPromptById).
     */
    public function updatePrompt(int $id, array $data): array
    {
        unset($data['usuario_id']);

        return $this->repository->update($id, $data);
    }

    public function deletePrompt(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
