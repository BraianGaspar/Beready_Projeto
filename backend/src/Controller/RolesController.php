<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Services\PermissionService;

class RolesController extends AppController
{
    private $permissionService;

    public function initialize(): void
    {
        parent::initialize();
        $this->loadModel('Roles');
        $this->loadModel('Permissoes');
        $this->loadModel('RolePermissoes');
        
        $this->permissionService = new PermissionService();
        
        // Verificar se é admin
        $user = $this->request->getAttribute('user');
        if (!$user || !$this->permissionService->isAdmin($user->id)) {
            throw new \Cake\Http\Exception\ForbiddenException('Acesso negado');
        }
    }

    /**
     * GET /api/admin/roles
     * Listar todas as roles
     */
    public function index()
    {
        $this->request->allowMethod(['get']);
        
        $roles = $this->Roles->find()
            ->contain(['Permissoes'])
            ->order(['nivel' => 'DESC'])
            ->toArray();

        $this->set([
            'success' => true,
            'data' => $roles,
        ]);
        $this->viewBuilder()->setOption('serialize', ['success', 'data']);
    }

    /**
     * POST /api/admin/roles
     * Criar nova role
     */
    public function add()
    {
        $this->request->allowMethod(['post']);
        
        $data = $this->request->getData();
        $result = $this->permissionService->createRole($data);
        
        if ($result['success']) {
            $this->permissionService->logAudit(
                $this->request->getAttribute('user')->id,
                'created',
                'role',
                $result['data']->id,
                null,
                $result['data']->toArray()
            );
        }

        $this->set($result);
        $this->viewBuilder()->setOption('serialize', ['success', 'data', 'errors']);
    }

    /**
     * PUT /api/admin/roles/{id}
     * Atualizar role
     */
    public function edit($id)
    {
        $this->request->allowMethod(['put', 'patch']);
        
        $oldRole = $this->Roles->get($id, ['contain' => ['Permissoes']]);
        $oldData = $oldRole->toArray();
        
        $data = $this->request->getData();
        $result = $this->permissionService->updateRole($id, $data);
        
        if ($result['success']) {
            $this->permissionService->logAudit(
                $this->request->getAttribute('user')->id,
                'updated',
                'role',
                $id,
                $oldData,
                $result['data']->toArray()
            );
        }

        $this->set($result);
        $this->viewBuilder()->setOption('serialize', ['success', 'data', 'errors']);
    }

    /**
     * DELETE /api/admin/roles/{id}
     * Excluir role
     */
    public function delete($id)
    {
        $this->request->allowMethod(['delete']);
        
        $oldRole = $this->Roles->get($id);
        $oldData = $oldRole->toArray();
        
        $result = $this->permissionService->deleteRole($id);
        
        if ($result['success']) {
            $this->permissionService->logAudit(
                $this->request->getAttribute('user')->id,
                'deleted',
                'role',
                $id,
                $oldData,
                null
            );
        }

        $this->set($result);
        $this->viewBuilder()->setOption('serialize', ['success', 'error']);
    }

    /**
     * GET /api/admin/permissions
     * Listar todas as permissões
     */
    public function permissions()
    {
        $this->request->allowMethod(['get']);
        
        $permissoes = $this->Permissoes->find()
            ->where(['is_ativo' => true])
            ->order(['recurso' => 'ASC', 'acao' => 'ASC'])
            ->toArray();

        $this->set([
            'success' => true,
            'data' => $permissoes,
        ]);
        $this->viewBuilder()->setOption('serialize', ['success', 'data']);
    }

    /**
     * POST /api/admin/permissions
     * Criar nova permissão
     */
    public function addPermission()
    {
        $this->request->allowMethod(['post']);
        
        $data = $this->request->getData();
        $permissao = $this->Permissoes->newEntity($data);
        
        if ($this->Permissoes->save($permissao)) {
            $this->permissionService->logAudit(
                $this->request->getAttribute('user')->id,
                'created',
                'permissao',
                $permissao->id,
                null,
                $permissao->toArray()
            );

            $this->set([
                'success' => true,
                'data' => $permissao,
            ]);
        } else {
            $this->set([
                'success' => false,
                'errors' => $permissao->getErrors(),
            ]);
        }
        
        $this->viewBuilder()->setOption('serialize', ['success', 'data', 'errors']);
    }
}