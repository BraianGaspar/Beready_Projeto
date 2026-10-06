<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Traits\ResourceErrorTrait;
use App\Services\QuizService;
use App\Repositories\QuizRepository;

class QuizesController extends AppController
{
    use ResourceErrorTrait;

    private QuizService $quizService;

    public function initialize(): void
    {
        parent::initialize();
        $this->quizService = new QuizService(new QuizRepository());
    }

    // GET /quizes
    public function index()
    {
        try {
            $quizzes = $this->quizService->getQuizzesByUsuario($this->currentUserId());
            return $this->jsonSuccess($quizzes);
        } catch (\Exception $e) {
            return $this->errorResponse($e, 'Erro ao carregar quizzes');
        }
    }

    // GET /quizes/view/{id}
    public function view($id = null)
    {
        $quizId = $id ?? $this->request->getParam('id') ?? $this->request->getQuery('id');

        if (!$quizId) {
            return $this->jsonError('ID do quiz não informado', 400);
        }

        try {
            $quiz = $this->quizService->getQuizById((int)$quizId);
            // Quiz público pode ser visualizado por qualquer usuário autenticado; alterar, só o dono.
            if (empty($quiz['publico']) && !$this->canAccessUser((int)$quiz['usuario_id'])) {
                return $this->jsonError('Quiz não encontrado', 404);
            }
            return $this->jsonSuccess($quiz);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    // POST /quizes
    public function add()
    {
        try {
            $quiz = $this->quizService->createQuiz($this->currentUserId(), $this->getRequestData());
            return $this->jsonSuccess($quiz, 'Quiz criado com sucesso', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e, 'Erro ao criar quiz');
        }
    }

    // PUT /quizes/{id}
    public function edit($id = null)
    {
        $quizId = $id ?? $this->request->getParam('id') ?? $this->request->getData('id');

        if (!$quizId) {
            return $this->jsonError('ID do quiz não informado', 400);
        }

        try {
            $this->findOwnedQuiz((int)$quizId);
            $quiz = $this->quizService->updateQuiz((int)$quizId, $this->getRequestData());
            return $this->jsonSuccess($quiz, 'Quiz atualizado com sucesso');
        } catch (\Exception $e) {
            return $this->errorResponse($e, 'Erro ao atualizar quiz');
        }
    }

    // DELETE /quizes/{id}
    public function delete($id = null)
    {
        $quizId = $id ?? $this->request->getParam('id') ?? $this->request->getData('id');

        if (!$quizId) {
            return $this->jsonError('ID do quiz não informado', 400);
        }

        try {
            $this->findOwnedQuiz((int)$quizId);
            $this->quizService->deleteQuiz((int)$quizId);
            return $this->jsonSuccess(null, 'Quiz excluído com sucesso');
        } catch (\Exception $e) {
            return $this->errorResponse($e, 'Erro ao excluir quiz');
        }
    }

    /**
     * Quiz de outro usuário responde 404 (admin acessa todos).
     */
    private function findOwnedQuiz(int $id): array
    {
        $quiz = $this->quizService->getQuizById($id);
        if (!$this->canAccessUser((int)$quiz['usuario_id'])) {
            throw new \RuntimeException('Quiz não encontrado', 404);
        }

        return $quiz;
    }
}
