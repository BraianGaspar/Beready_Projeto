<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;

/**
 * Acesso restrito a admins pelo AdminMiddleware (escopo /admin).
 */
class AdminController extends AppController
{
    private $usersTable;

    public function initialize(): void
    {
        parent::initialize();
        $this->usersTable = TableRegistry::getTableLocator()->get('Users');
    }

    // GET /admin/users - Lista todos os usuários
    public function users()
    {
        try {
            $users = $this->usersTable->find()
                ->select(['id', 'nome', 'email', 'role', 'status', 'criado_em', 'ultimo_login'])
                ->orderBy(['id' => 'ASC'])
                ->all();

            return $this->jsonSuccess($users->toArray());
        } catch (\Exception $e) {
            Log::error('Erro ao listar usuários (admin): ' . $e->getMessage());
            return $this->jsonError('Erro ao listar usuários', 500);
        }
    }

    // POST /admin/users/role  { user_id, role }
    public function updateRole()
    {
        $data = $this->getRequestData();
        $userId = (int)($data['user_id'] ?? 0);
        $newRole = $data['role'] ?? null;

        if (!$userId) {
            return $this->jsonError('ID do usuário não informado', 400);
        }

        if (!in_array($newRole, ['user', 'admin'], true)) {
            return $this->jsonError('Role inválida. Use "user" ou "admin"', 400);
        }

        if ($userId === $this->currentUserId() && $newRole !== 'admin') {
            return $this->jsonError('Você não pode rebaixar seu próprio nível de acesso', 403);
        }

        try {
            $user = $this->usersTable->get($userId);
        } catch (RecordNotFoundException $e) {
            return $this->jsonError('Usuário não encontrado', 404);
        }

        $user->set('role', $newRole);

        if (!$this->usersTable->save($user)) {
            return $this->jsonError('Erro ao atualizar role', 500);
        }

        return $this->jsonSuccess([
            'id' => $user->id,
            'nome' => $user->nome,
            'role' => $user->role
        ], 'Role atualizada com sucesso');
    }

    // GET /admin/stats
    public function stats()
    {
        try {
            $locator = TableRegistry::getTableLocator();

            $stats = [
                'total_users' => $this->usersTable->find()->count(),
                'total_flashcards' => $locator->get('Flashcards')->find()->count(),
                'total_quizes' => $locator->get('Quizes')->find()->count(),
                'total_prompts' => $locator->get('Prompts')->find()->count(),
                'total_tags' => $locator->get('Tags')->find()->count(),
                'total_traducoes' => $locator->get('Traducoes')->find()->count(),
                'total_imagens' => $locator->get('ImagensGeradas')->find()->count(),
                'admin_count' => $this->usersTable->find()->where(['role' => 'admin'])->count(),
                'user_count' => $this->usersTable->find()->where(['role' => 'user'])->count(),
            ];

            return $this->jsonSuccess($stats);
        } catch (\Exception $e) {
            Log::error('Erro ao carregar estatísticas (admin): ' . $e->getMessage());
            return $this->jsonError('Erro ao carregar estatísticas', 500);
        }
    }
}
