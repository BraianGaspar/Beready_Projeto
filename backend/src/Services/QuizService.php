<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\QuizRepositoryInterface;

class QuizService
{
    private QuizRepositoryInterface $quizRepository;

    public function __construct(QuizRepositoryInterface $quizRepository)
    {
        $this->quizRepository = $quizRepository;
    }

    public function getQuizzesByUsuario(int $usuarioId): array
    {
        return $this->quizRepository->findByUsuarioId($usuarioId);
    }

    public function getQuizById(int $id): array
    {
        $quiz = $this->quizRepository->findById($id);
        if (!$quiz) {
            throw new \RuntimeException('Quiz não encontrado', 404);
        }
        return $quiz;
    }

    public function createQuiz(int $usuarioId, array $data): array
    {
        if (empty($data['titulo'])) {
            throw new \InvalidArgumentException('Título é obrigatório');
        }

        $data['usuario_id'] = $usuarioId;
        $data['tipo_criacao'] = $data['tipo_criacao'] ?? 'manual';
        $data['nivel_dificuldade'] = $data['nivel_dificuldade'] ?? 'iniciante';
        $data['total_questoes'] = (int)($data['total_questoes'] ?? 0);
        $data['publico'] = !empty($data['publico']);

        return $this->quizRepository->create($data);
    }

    /**
     * O chamador deve validar o dono antes (getQuizById).
     */
    public function updateQuiz(int $id, array $data): array
    {
        unset($data['usuario_id']);

        return $this->quizRepository->update($id, $data);
    }

    public function deleteQuiz(int $id): bool
    {
        return $this->quizRepository->delete($id);
    }
}
