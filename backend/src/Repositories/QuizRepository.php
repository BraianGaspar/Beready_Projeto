<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\QuizRepositoryInterface;
use Cake\ORM\TableRegistry;

class QuizRepository implements QuizRepositoryInterface
{
    private $quizesTable;

    public function __construct()
    {
        $this->quizesTable = TableRegistry::getTableLocator()->get('Quizes');
    }

    public function findByUsuarioId(int $usuarioId): array
    {
        $quizes = $this->quizesTable->find()
            ->select(['id', 'usuario_id', 'titulo', 'descricao', 'tipo_criacao', 'nivel_dificuldade', 'total_questoes', 'tempo_limite', 'publico', 'criado_em', 'atualizado_em'])
            ->where(['usuario_id' => $usuarioId])
            ->orderBy(['criado_em' => 'DESC'])
            ->all();

        return array_map(fn($q) => $q->toArray(), $quizes->toArray());
    }

    public function findById(int $id): ?array
    {
        $quiz = $this->quizesTable->find()->where(['id' => $id])->first();

        return $quiz ? $quiz->toArray() : null;
    }

    public function create(array $data): array
    {
        $quiz = $this->quizesTable->newEntity($data);
        $this->quizesTable->saveOrFail($quiz);
        return $quiz->toArray();
    }

    public function update(int $id, array $data): array
    {
        $quiz = $this->quizesTable->get($id);
        $quiz = $this->quizesTable->patchEntity($quiz, $data);
        $this->quizesTable->saveOrFail($quiz);
        return $quiz->toArray();
    }

    public function delete(int $id): bool
    {
        $quiz = $this->quizesTable->get($id);
        return $this->quizesTable->delete($quiz);
    }
}
