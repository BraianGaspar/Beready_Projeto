<?php

namespace App\Controller;

use Cake\ORM\TableRegistry;
use App\Services\JwtService;

class AdminRolesController extends AppController
{
    private $rolesTable;
    private $permissoesTable;
    private $rolePermissoesTable;

    public function initialize(): void
    {
        parent::initialize();
        
        $this->rolesTable = TableRegistry::getTableLocator()->get('Roles');
        $this->permissoesTable = TableRegistry::getTableLocator()->get('Permissoes');
        $this->rolePermissoesTable = TableRegistry::getTableLocator()->get('RolePermissoes');

        $authHeader = $this->request->getHeaderLine('Authorization');
        $role = 'user';

        if (preg_match('/Bearer\s+(.+)/', $authHeader, $matches)) {
            $token = $matches[1];
            $jwtService = new JwtService();
            $payload = $jwtService->validateToken($token);
            $role = $payload['role'] ?? 'user';
        }

        if ($role !== 'admin') {
            $this->response = $this->response->withStatus(403);
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Acesso negado. Área administrativa.'
            ]));
            $this->response = $this->response->withType('application/json');
            $this->autoRender = false;
            return;
        }
    }

    public function index()
    {
        try {
            $roles = $this->rolesTable->find()
                ->contain(['Permissoes'])
                ->where(['Roles.is_ativo' => true])
                ->orderBy(['Roles.nivel' => 'DESC'])
                ->toArray();

            return $this->jsonSuccess($roles);
        } catch (\Exception $e) {
            return $this->jsonError($e->getMessage(), 500);
        }
    }

    public function add()
    {
        try {
            $data = $this->request->getData();
            $permissionIds = $data['permission_ids'] ?? [];
            unset($data['permission_ids']);

            $role = $this->rolesTable->newEntity($data);
            
            if ($this->rolesTable->save($role)) {
                if (!empty($permissionIds)) {
                    foreach ($permissionIds as $permId) {
                        $this->rolePermissoesTable->save(
                            $this->rolePermissoesTable->newEntity([
                                'role_id' => $role->id,
                                'permissao_id' => $permId,
                            ])
                        );
                    }
                }

                return $this->jsonSuccess(
                    $this->rolesTable->get($role->id, ['contain' => ['Permissoes']]),
                    'Role criada com sucesso'
                );
            }

            $errors = $role->getErrors();
            $errorMessages = [];
            foreach ($errors as $field => $fieldErrors) {
                $errorMessages[] = $field . ': ' . implode(', ', $fieldErrors);
            }
            return $this->jsonError(implode('; ', $errorMessages), 400);
            
        } catch (\Exception $e) {
            return $this->jsonError($e->getMessage(), 500);
        }
    }

    public function edit($id)
    {
        try {
            $role = $this->rolesTable->get($id);
            $data = $this->request->getData();
            $permissionIds = $data['permission_ids'] ?? null;
            unset($data['permission_ids']);

            $role = $this->rolesTable->patchEntity($role, $data);
            
            if ($this->rolesTable->save($role)) {
                if ($permissionIds !== null) {
                    $this->rolePermissoesTable->deleteAll(['role_id' => $role->id]);
                    
                    foreach ($permissionIds as $permId) {
                        $this->rolePermissoesTable->save(
                            $this->rolePermissoesTable->newEntity([
                                'role_id' => $role->id,
                                'permissao_id' => $permId,
                            ])
                        );
                    }
                }

                return $this->jsonSuccess(
                    $this->rolesTable->get($role->id, ['contain' => ['Permissoes']]),
                    'Role atualizada com sucesso'
                );
            }

            $errors = $role->getErrors();
            $errorMessages = [];
            foreach ($errors as $field => $fieldErrors) {
                $errorMessages[] = $field . ': ' . implode(', ', $fieldErrors);
            }
            return $this->jsonError(implode('; ', $errorMessages), 400);
            
        } catch (\Exception $e) {
            return $this->jsonError($e->getMessage(), 500);
        }
    }

    public function delete($id)
    {
        try {
            $role = $this->rolesTable->get($id);
            
            if ($this->rolesTable->delete($role)) {
                return $this->jsonSuccess(null, 'Role excluída com sucesso');
            }

            return $this->jsonError('Erro ao excluir role', 500);
        } catch (\Exception $e) {
            return $this->jsonError($e->getMessage(), 500);
        }
    }
}