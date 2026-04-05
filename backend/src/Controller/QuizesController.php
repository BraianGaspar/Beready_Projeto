<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\ORM\TableRegistry;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Response;

class QuizesController extends AppController
{
    private $QuizesTable;

    public function initialize(): void
    {
        parent::initialize();
        $this->QuizesTable = TableRegistry::getTableLocator()->get('Quizes');
    }

    /**
     * Método auxiliar para enviar resposta JSON
     */
    private function jsonResponse($data, int $statusCode = 200): Response
    {
        $this->response = $this->response->withStatus($statusCode);
        $this->response = $this->response->withType('application/json');
        $this->response = $this->response->withStringBody(json_encode($data, JSON_UNESCAPED_UNICODE));
        return $this->response;
    }

    /**
     * GET /quizes - Lista todos os quizes
     */
    public function index(): Response
    {
        try {
            $quizes = $this->QuizesTable->find()
                ->select(['id', 'usuario_id', 'titulo', 'descricao', 'nivel_dificuldade', 'total_questoes', 'tempo_limite', 'publico', 'criado_em', 'atualizado_em'])
                ->orderBy(['criado_em' => 'DESC'])
                ->all();
            
            return $this->jsonResponse([
                'success' => true,
                'data' => $quizes->toArray()
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao carregar quizzes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /quizes - Cria um novo quiz
     */
    public function add(): Response
    {
        try {
            $input = $this->request->getInput();
            $data = json_decode($input, true);
            
            if (!$data) {
                $data = $this->request->getData();
            }
            
            if (empty($data['titulo'])) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Título é obrigatório'
                ], 400);
            }
            
            if (empty($data['usuario_id'])) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'ID do usuário é obrigatório'
                ], 400);
            }
            
            $quizData = [
                'usuario_id' => $data['usuario_id'],
                'titulo' => $data['titulo'],
                'descricao' => $data['descricao'] ?? null,
                'tipo_criacao' => $data['tipo_criacao'] ?? 'manual',
                'nivel_dificuldade' => $data['nivel_dificuldade'] ?? 'iniciante',
                'total_questoes' => (int)($data['total_questoes'] ?? 0),
                'tempo_limite' => !empty($data['tempo_limite']) ? (int)$data['tempo_limite'] : null,
                'publico' => !empty($data['publico']),
            ];
            
            $quiz = $this->QuizesTable->newEntity($quizData);
            
            if ($this->QuizesTable->save($quiz)) {
                return $this->jsonResponse([
                    'success' => true,
                    'message' => 'Quiz criado com sucesso',
                    'data' => $quiz
                ], 201);
            } else {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Erro ao criar quiz',
                    'errors' => $quiz->getErrors()
                ], 422);
            }
        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao criar quiz: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * PUT /quizes/{id} - Atualiza um quiz
     */
    public function edit($id = null): Response
    {
        if (!$id) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'ID do quiz não informado'
            ], 400);
        }
        
        try {
            $input = $this->request->getInput();
            $data = json_decode($input, true);
            
            if (!$data) {
                $data = $this->request->getData();
            }
            
            $quiz = $this->QuizesTable->get($id);
            unset($data['usuario_id']);
            
            $quiz = $this->QuizesTable->patchEntity($quiz, $data);
            
            if ($this->QuizesTable->save($quiz)) {
                return $this->jsonResponse([
                    'success' => true,
                    'message' => 'Quiz atualizado com sucesso',
                    'data' => $quiz
                ]);
            } else {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Erro ao atualizar quiz',
                    'errors' => $quiz->getErrors()
                ], 422);
            }
        } catch (RecordNotFoundException $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Quiz não encontrado'
            ], 404);
        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao atualizar quiz: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * DELETE /quizes/{id} - Remove um quiz
     */
    public function delete($id = null): Response
    {
        if (!$id) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'ID do quiz não informado'
            ], 400);
        }
        
        try {
            $quiz = $this->QuizesTable->get($id);
            
            if ($this->QuizesTable->delete($quiz)) {
                return $this->jsonResponse([
                    'success' => true,
                    'message' => 'Quiz excluído com sucesso'
                ]);
            } else {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Erro ao excluir quiz'
                ], 500);
            }
        } catch (RecordNotFoundException $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Quiz não encontrado'
            ], 404);
        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao excluir quiz: ' . $e->getMessage()
            ], 500);
        }
    }
}