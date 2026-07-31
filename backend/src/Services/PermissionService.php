<?php

namespace App\Services;

use Cake\ORM\TableRegistry;
use Cake\Log\Log;

class PermissionService
{
    private $rolesTable;
    private $permissoesTable;
    private $rolePermissoesTable;
    private $usuarioRolesTable;
    private $assinaturasTable;
    private $planosTable;
    private $usersTable;

    public function __construct()
    {
        $this->rolesTable = TableRegistry::getTableLocator()->get('Roles');
        $this->permissoesTable = TableRegistry::getTableLocator()->get('Permissoes');
        $this->rolePermissoesTable = TableRegistry::getTableLocator()->get('RolePermissoes');
        $this->usuarioRolesTable = TableRegistry::getTableLocator()->get('UsuarioRoles');
        $this->assinaturasTable = TableRegistry::getTableLocator()->get('Assinaturas');
        $this->planosTable = TableRegistry::getTableLocator()->get('Planos');
        $this->usersTable = TableRegistry::getTableLocator()->get('Users');
    }

    /**
     * Obtém todas as permissões do sistema
     */
    public function getAllPermissions(): array
    {
        $permissoes = $this->permissoesTable->find()
            ->where(['is_ativo' => true])
            ->toArray();
        
        return array_map(function($perm) {
            return $perm->nome;
        }, $permissoes);
    }

    /**
     * Obtém todas as roles com permissões
     */
    public function getRolesWithPermissions(): array
    {
        return $this->rolesTable->getWithPermissions();
    }

    /**
     * Obtém permissões de um usuário
     */
    public function getUserPermissions(int $usuarioId): array
    {
        $permissions = [];

        // Se for admin, retorna todas as permissões
        try {
            $user = $this->usersTable->get($usuarioId);
            if ($user->role === 'admin') {
                return $this->getAllPermissions();
            }
        } catch (\Exception $e) {
            // Usuário não encontrado
            return [];
        }

        // 1. Verificar roles manuais
        $usuarioRoles = $this->usuarioRolesTable->find()
            ->contain(['Roles.Permissoes'])
            ->where([
                'usuario_id' => $usuarioId,
                'is_manual' => true,
            ])
            ->toArray();

        foreach ($usuarioRoles as $ur) {
            if ($ur->role && $ur->role->permissoes) {
                foreach ($ur->role->permissoes as $perm) {
                    $permissions[] = $perm->nome;
                }
            }
        }

        // 2. Verificar role da assinatura ativa
        $assinatura = $this->assinaturasTable->find()
            ->where([
                'usuario_id' => $usuarioId,
                'status' => 'active',
                'is_ativo' => true,
            ])
            ->contain(['Plano.Role.Permissoes'])
            ->first();

        if ($assinatura && $assinatura->plano && $assinatura->plano->role) {
            foreach ($assinatura->plano->role->permissoes as $perm) {
                $permissions[] = $perm->nome;
            }
        }

        return array_values(array_unique($permissions));
    }

    /**
     * Verifica se usuário tem permissão
     */
    public function hasPermission(int $usuarioId, string $permission): bool
    {
        if ($this->isAdmin($usuarioId)) {
            return true;
        }

        $permissions = $this->getUserPermissions($usuarioId);
        return in_array($permission, $permissions);
    }

    /**
     * Verifica se usuário é admin
     */
    public function isAdmin(int $usuarioId): bool
    {
        try {
            $user = $this->usersTable->get($usuarioId);
            return $user->role === 'admin';
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Atribui role a um usuário
     */
    public function assignRole(int $usuarioId, int $roleId, ?int $atribuidoPor = null): bool
    {
        $existing = $this->usuarioRolesTable->find()
            ->where([
                'usuario_id' => $usuarioId,
                'role_id' => $roleId,
            ])
            ->first();

        if ($existing) {
            return true;
        }

        $entity = $this->usuarioRolesTable->newEntity([
            'usuario_id' => $usuarioId,
            'role_id' => $roleId,
            'atribuido_por' => $atribuidoPor,
            'is_manual' => true,
        ]);

        return $this->usuarioRolesTable->save($entity) !== false;
    }

    /**
     * Remove role de um usuário
     */
    public function removeRole(int $usuarioId, int $roleId): bool
    {
        $entity = $this->usuarioRolesTable->find()
            ->where([
                'usuario_id' => $usuarioId,
                'role_id' => $roleId,
            ])
            ->first();

        if ($entity) {
            return $this->usuarioRolesTable->delete($entity) !== false;
        }

        return true;
    }

    /**
     * Cria nova role
     */
    public function createRole(array $data): array
    {
        $role = $this->rolesTable->newEntity($data);
        
        if ($this->rolesTable->save($role)) {
            if (!empty($data['permission_ids'])) {
                foreach ($data['permission_ids'] as $permId) {
                    $this->rolePermissoesTable->save(
                        $this->rolePermissoesTable->newEntity([
                            'role_id' => $role->id,
                            'permissao_id' => $permId,
                        ])
                    );
                }
            }

            return ['success' => true, 'data' => $role];
        }

        return ['success' => false, 'errors' => $role->getErrors()];
    }

    /**
     * Atualiza role
     */
    public function updateRole(int $roleId, array $data): array
    {
        $role = $this->rolesTable->get($roleId);
        $role = $this->rolesTable->patchEntity($role, $data);
        
        if ($this->rolesTable->save($role)) {
            if (isset($data['permission_ids'])) {
                $this->rolePermissoesTable->deleteAll(['role_id' => $roleId]);
                
                foreach ($data['permission_ids'] as $permId) {
                    $this->rolePermissoesTable->save(
                        $this->rolePermissoesTable->newEntity([
                            'role_id' => $roleId,
                            'permissao_id' => $permId,
                        ])
                    );
                }
            }

            return ['success' => true, 'data' => $role];
        }

        return ['success' => false, 'errors' => $role->getErrors()];
    }

    /**
     * Exclui role (apenas se não for do sistema)
     */
    public function deleteRole(int $roleId): array
    {
        if ($this->rolesTable->isSistema($roleId)) {
            return ['success' => false, 'error' => 'Não é possível excluir uma role do sistema'];
        }

        $role = $this->rolesTable->get($roleId);
        
        if ($this->rolesTable->delete($role)) {
            return ['success' => true];
        }

        return ['success' => false];
    }

    /**
     * Cria log de auditoria
     */
    public function logAudit(int $usuarioId, string $acao, string $entidade, int $entidadeId, $oldData, $newData): void
    {
        $logsTable = TableRegistry::getTableLocator()->get('LogsPermissoes');
        
        $log = $logsTable->newEntity([
            'usuario_id' => $usuarioId,
            'acao' => $acao,
            'entidade' => $entidade,
            'entidade_id' => $entidadeId,
            'dados_anteriores' => $oldData ? json_encode($oldData) : null,
            'dados_novos' => $newData ? json_encode($newData) : null,
        ]);
        
        $logsTable->save($log);
    }
}