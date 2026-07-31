<?php

namespace App\Controller;

use App\Services\PermissionService;
use Cake\ORM\TableRegistry;

class UserPermissionsController extends AppController
{
    private $permissionService;

    public function initialize(): void
    {
        parent::initialize();
        $this->permissionService = new PermissionService();
        
        // Verifica se o usuário está autenticado via token
        $user = $this->request->getAttribute('user');
        if (!$user) {
            throw new \Cake\Http\Exception\UnauthorizedException('Usuário não autenticado');
        }
    }

    /**
     * GET /api/user/permissions
     * Retorna as permissões do usuário autenticado
     */
    public function index()
    {
        $this->request->allowMethod(['get']);
        
        $user = $this->request->getAttribute('user');
        $permissions = $this->permissionService->getUserPermissions($user->id);
        
        $this->set([
            'success' => true,
            'data' => $permissions,
        ]);
        $this->viewBuilder()->setOption('serialize', ['success', 'data']);
    }
}