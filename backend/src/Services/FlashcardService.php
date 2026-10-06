<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\FlashcardRepositoryInterface;

class FlashcardService
{
    private FlashcardRepositoryInterface $flashcardRepository;

    public function __construct(FlashcardRepositoryInterface $flashcardRepository)
    {
        $this->flashcardRepository = $flashcardRepository;
    }

    public function getFlashcardsByUsuario(int $usuarioId): array
    {
        return $this->flashcardRepository->findByUsuarioId($usuarioId);
    }

    public function getFlashcardById(int $id): array
    {
        $flashcard = $this->flashcardRepository->findById($id);
        if (!$flashcard) {
            throw new \RuntimeException('Flashcard não encontrado', 404);
        }
        return $flashcard;
    }

    public function getFlashcardByUuid(string $uuid): array
    {
        $flashcard = $this->flashcardRepository->findByUuid($uuid);
        if (!$flashcard) {
            throw new \RuntimeException('Flashcard não encontrado', 404);
        }
        return $flashcard;
    }

    public function createFlashcard(int $usuarioId, array $data): array
    {
        if (empty($data['frente'])) {
            throw new \InvalidArgumentException('A pergunta (frente) é obrigatória');
        }

        if (empty($data['verso'])) {
            throw new \InvalidArgumentException('A resposta (verso) é obrigatória');
        }

        $data['usuario_id'] = $usuarioId;

        return $this->flashcardRepository->create($data);
    }

    /**
     * O chamador deve validar o dono antes (getFlashcardById).
     */
    public function updateFlashcard(int $id, array $data): array
    {
        unset($data['usuario_id']);

        return $this->flashcardRepository->update($id, $data);
    }

    public function deleteFlashcard(int $id): bool
    {
        return $this->flashcardRepository->delete($id);
    }
}
