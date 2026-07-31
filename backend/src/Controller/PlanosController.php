<?php

namespace App\Controller;

use Cake\ORM\TableRegistry;
use Cake\Log\Log;

class PlanosController extends AppController
{
    private $planosTable;

    public function initialize(): void
    {
        parent::initialize();
        $this->planosTable = TableRegistry::getTableLocator()->get('Planos');
    }

    public function index()
    {
        try {
            $planos = $this->planosTable->find()
                ->contain(['Roles'])
                ->where(['Planos.is_ativo' => true])
                ->orderBy(['Planos.ordem' => 'ASC'])
                ->toArray();

            return $this->jsonSuccess($planos);
        } catch (\Exception $e) {
            error_log('ERRO PlanosController: ' . $e->getMessage());
            return $this->jsonError('Erro: ' . $e->getMessage(), 500);
        }
    }
}