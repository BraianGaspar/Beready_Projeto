<?php

namespace App\Controller;

use Cake\ORM\TableRegistry;
use App\Services\JwtService;

class AdminPermissionsController extends AppController
{
    private $permissoesTable;

    public function initialize(): void
    {
        parent::initialize();
        
        $this->permissoesTable = TableRegistry::getTableLocator()->get('Permissoes');

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
            $permissoes = $this->permissoesTable->find()
                ->where(['is_ativo' => true])
                ->order(['recurso' => 'ASC', 'acao' => 'ASC'])
                ->toArray();

            return $this->jsonSuccess($permissoes);
        } catch (\Exception $e) {
            return $this->jsonError($e->getMessage(), 500);
        }
    }
}