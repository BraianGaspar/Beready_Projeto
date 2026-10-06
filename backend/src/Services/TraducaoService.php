<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\TraducaoRepositoryInterface;

/**
 * O dono de uma tradução é o dono do prompt: a verificação fica no controller.
 */
class TraducaoService
{
    private TraducaoRepositoryInterface $repository;

    public function __construct(TraducaoRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getTraducoesByPrompt(int $promptId): array
    {
        return $this->repository->findByPromptId($promptId);
    }

    public function getTraducaoById(int $id): array
    {
        $traducao = $this->repository->findById($id);
        if (!$traducao) {
            throw new \RuntimeException('Tradução não encontrada', 404);
        }
        return $traducao;
    }

    public function createTraducao(array $data): array
    {
        if (empty($data['prompt_id'])) {
            throw new \InvalidArgumentException('ID do prompt é obrigatório');
        }

        if (empty($data['texto_traduzido'])) {
            throw new \InvalidArgumentException('Texto traduzido é obrigatório');
        }

        return $this->repository->create($this->encodeAlternativas($data));
    }

    public function updateTraducao(int $id, array $data): array
    {
        return $this->repository->update($id, $this->encodeAlternativas($data));
    }

    public function deleteTraducao(int $id): bool
    {
        return $this->repository->delete($id);
    }

    private function encodeAlternativas(array $data): array
    {
        if (isset($data['traducoes_alternativas']) && is_array($data['traducoes_alternativas'])) {
            $data['traducoes_alternativas'] = json_encode($data['traducoes_alternativas']);
        }

        return $data;
    }
}
