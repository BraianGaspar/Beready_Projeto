<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\FlashcardRepositoryInterface;
use Cake\ORM\TableRegistry;

class FlashcardRepository implements FlashcardRepositoryInterface
{
    private $flashcardsTable;

    public function __construct()
    {
        $this->flashcardsTable = TableRegistry::getTableLocator()->get('Flashcards');
    }

    public function findByUsuarioId(int $usuarioId): array
    {
        $flashcards = $this->flashcardsTable->find()
            ->select(['id', 'usuario_id', 'frente', 'verso', 'nivel_dificuldade', 'criado_em', 'atualizado_em'])
            ->where(['usuario_id' => $usuarioId])
            ->orderBy(['criado_em' => 'DESC'])
            ->all();

        return array_map(fn($f) => $f->toArray(), $flashcards->toArray());
    }

    public function findById(int $id): ?array
    {
        $flashcard = $this->flashcardsTable->find()->where(['id' => $id])->first();

        return $flashcard ? $flashcard->toArray() : null;
    }

    public function findByUuid(string $uuid): ?array
    {
        $flashcard = $this->flashcardsTable->find()->where(['uuid' => $uuid])->first();

        return $flashcard ? $flashcard->toArray() : null;
    }

    public function create(array $data): array
    {
        $flashcard = $this->flashcardsTable->newEntity($data);
        $this->flashcardsTable->saveOrFail($flashcard);
        return $flashcard->toArray();
    }

    public function update(int $id, array $data): array
    {
        $flashcard = $this->flashcardsTable->get($id);
        $flashcard = $this->flashcardsTable->patchEntity($flashcard, $data);
        $this->flashcardsTable->saveOrFail($flashcard);
        return $flashcard->toArray();
    }

    public function delete(int $id): bool
    {
        $flashcard = $this->flashcardsTable->get($id);
        return $this->flashcardsTable->delete($flashcard);
    }
}
