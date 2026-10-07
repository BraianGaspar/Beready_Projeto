<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\FlashcardRepositoryInterface;
use Cake\Datasource\EntityInterface;
use Cake\I18n\DateTime;
use Cake\ORM\TableRegistry;

class FlashcardRepository implements FlashcardRepositoryInterface
{
    private const CAMPOS_LISTAGEM = [
        'id', 'usuario_id', 'frente', 'verso', 'nivel_dificuldade', 'criado_em', 'atualizado_em',
        'repeticoes', 'intervalo_dias', 'fator_ease', 'proxima_revisao', 'ultima_revisao',
    ];

    private const CAMPOS_AGENDAMENTO = ['repeticoes', 'intervalo_dias', 'fator_ease', 'proxima_revisao', 'ultima_revisao'];

    private $flashcardsTable;

    public function __construct()
    {
        $this->flashcardsTable = TableRegistry::getTableLocator()->get('Flashcards');
    }

    public function findByUsuarioId(int $usuarioId): array
    {
        $flashcards = $this->flashcardsTable->find()
            ->select(self::CAMPOS_LISTAGEM)
            ->where(['usuario_id' => $usuarioId])
            ->orderBy(['criado_em' => 'DESC'])
            ->all();

        return array_map(fn($f) => $this->toArray($f), $flashcards->toArray());
    }

    public function findById(int $id): ?array
    {
        $flashcard = $this->flashcardsTable->find()->where(['id' => $id])->first();

        return $flashcard ? $this->toArray($flashcard) : null;
    }

    public function findByUuid(string $uuid): ?array
    {
        $flashcard = $this->flashcardsTable->find()->where(['uuid' => $uuid])->first();

        return $flashcard ? $this->toArray($flashcard) : null;
    }

    public function create(array $data): array
    {
        $flashcard = $this->flashcardsTable->newEntity($data);
        // Card novo já fica devido (relógio da aplicação, coerente com a listagem dos devidos)
        $flashcard->set('proxima_revisao', DateTime::now());
        $this->flashcardsTable->saveOrFail($flashcard);

        // Recarrega para trazer os padrões do banco (uuid, proxima_revisao...)
        return $this->findById((int)$flashcard->id) ?? $this->toArray($flashcard);
    }

    public function update(int $id, array $data): array
    {
        $flashcard = $this->flashcardsTable->get($id);
        $flashcard = $this->flashcardsTable->patchEntity($flashcard, $data);
        $this->flashcardsTable->saveOrFail($flashcard);
        return $this->toArray($flashcard);
    }

    public function delete(int $id): bool
    {
        $flashcard = $this->flashcardsTable->get($id);
        return $this->flashcardsTable->delete($flashcard);
    }

    public function findDevidos(int $usuarioId, \DateTimeInterface $ate, ?int $limite = null): array
    {
        $query = $this->flashcardsTable->find()
            ->select(self::CAMPOS_LISTAGEM)
            ->where(['usuario_id' => $usuarioId, 'proxima_revisao <=' => $ate])
            ->orderBy(['proxima_revisao' => 'ASC', 'id' => 'ASC']);

        if ($limite !== null) {
            $query->limit($limite);
        }

        return array_map(fn($f) => $this->toArray($f), $query->all()->toArray());
    }

    public function countDevidos(int $usuarioId, \DateTimeInterface $ate): int
    {
        return $this->flashcardsTable->find()
            ->where(['usuario_id' => $usuarioId, 'proxima_revisao <=' => $ate])
            ->count();
    }

    public function salvarAgendamento(int $id, array $agendamento): array
    {
        $flashcard = $this->flashcardsTable->get($id);
        foreach (self::CAMPOS_AGENDAMENTO as $campo) {
            if (array_key_exists($campo, $agendamento)) {
                $flashcard->set($campo, $agendamento[$campo]);
            }
        }
        $this->flashcardsTable->saveOrFail($flashcard);

        return $this->toArray($flashcard);
    }

    /**
     * fator_ease é numeric no Postgres (string no PHP); a API devolve número.
     */
    private function toArray(EntityInterface $flashcard): array
    {
        $data = $flashcard->toArray();
        if (array_key_exists('fator_ease', $data) && $data['fator_ease'] !== null) {
            $data['fator_ease'] = (float)$data['fator_ease'];
        }

        return $data;
    }
}
