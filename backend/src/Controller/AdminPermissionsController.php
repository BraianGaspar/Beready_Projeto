<?php

namespace App\Controller;

use Cake\Log\Log;
use Cake\ORM\TableRegistry;

/**
 * Acesso restrito a admins pelo AdminMiddleware (escopo /admin).
 */
class AdminPermissionsController extends AppController
{
    private $permissoesTable;

    public function initialize(): void
    {
        parent::initialize();
        $this->permissoesTable = TableRegistry::getTableLocator()->get('Permissoes');
    }

    public function index()
    {
        try {
            $permissoes = $this->permissoesTable->find()
                ->where(['is_ativo' => true])
                ->orderBy(['recurso' => 'ASC', 'acao' => 'ASC'])
                ->toArray();

            return $this->jsonSuccess($permissoes);
        } catch (\Exception $e) {
            Log::error('Erro ao listar permissões (admin): ' . $e->getMessage());
            return $this->jsonError('Erro ao listar permissões', 500);
        }
    }
}
