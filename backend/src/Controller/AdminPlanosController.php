<?php

namespace App\Controller;

use Cake\ORM\TableRegistry;
use App\Services\JwtService;
use Cake\Log\Log;

class AdminPlanosController extends AppController
{
    private $planosTable;

public function initialize(): void
    {
        parent::initialize();
        
        $this->planosTable = TableRegistry::getTableLocator()->get('Planos');

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
            // Mostra todos os planos
            $planos = $this->planosTable->find()
                ->contain(['Roles'])
                ->orderBy(['ordem' => 'ASC'])
                ->toArray();

            // Converter preços para float
            foreach ($planos as $plano) {
                $plano->preco_mensal = (float) $plano->preco_mensal;
                $plano->preco_anual = (float) $plano->preco_anual;
            }

            return $this->jsonSuccess($planos);
        } catch (\Exception $e) {
            Log::error('ERRO AdminPlanosController::index: ' . $e->getMessage());
            return $this->jsonError($e->getMessage(), 500);
        }
    }

    public function add()
    {
        try {
            $data = $this->request->getData();
            
            if (isset($data['recursos']) && is_string($data['recursos'])) {
                $data['recursos'] = array_map('trim', explode(',', $data['recursos']));
            }
            
            if (isset($data['limites']) && is_string($data['limites'])) {
                $data['limites'] = json_decode($data['limites'], true);
            }

            $plano = $this->planosTable->newEntity($data);
            
            if ($this->planosTable->save($plano)) {
                $saved = $this->planosTable->get($plano->id, ['contain' => ['Roles']]);
                $saved->preco_mensal = (float) $saved->preco_mensal;
                $saved->preco_anual = (float) $saved->preco_anual;
                return $this->jsonSuccess($saved, 'Plano criado com sucesso');
            }

            $errors = $plano->getErrors();
            $errorMessages = [];
            foreach ($errors as $field => $fieldErrors) {
                $errorMessages[] = $field . ': ' . implode(', ', $fieldErrors);
            }
            return $this->jsonError(implode('; ', $errorMessages), 400);
            
        } catch (\Exception $e) {
            Log::error('ERRO AdminPlanosController::add: ' . $e->getMessage());
            return $this->jsonError($e->getMessage(), 500);
        }
    }

    public function edit($id)
    {
        try {
            Log::info('=== EDIT PLANO ===');
            Log::info('ID: ' . $id);
            Log::info('DATA: ' . json_encode($this->request->getData()));
            
            $plano = $this->planosTable->get($id);
            $data = $this->request->getData();
            
            if (isset($data['recursos']) && is_string($data['recursos'])) {
                $data['recursos'] = array_map('trim', explode(',', $data['recursos']));
            }
            
            if (isset($data['limites']) && is_string($data['limites'])) {
                $data['limites'] = json_decode($data['limites'], true);
            }

            $plano = $this->planosTable->patchEntity($plano, $data);
            
            if ($this->planosTable->save($plano)) {
                $saved = $this->planosTable->get($plano->id, ['contain' => ['Roles']]);
                $saved->preco_mensal = (float) $saved->preco_mensal;
                $saved->preco_anual = (float) $saved->preco_anual;
                return $this->jsonSuccess($saved, 'Plano atualizado com sucesso');
            }

            $errors = $plano->getErrors();
            $errorMessages = [];
            foreach ($errors as $field => $fieldErrors) {
                $errorMessages[] = $field . ': ' . implode(', ', $fieldErrors);
            }
            return $this->jsonError(implode('; ', $errorMessages), 400);
            
        } catch (\Exception $e) {
            Log::error('ERRO AdminPlanosController::edit: ' . $e->getMessage());
            return $this->jsonError($e->getMessage(), 500);
        }
    }

    public function delete($id)
    {
        try {
            $plano = $this->planosTable->get($id);
            
            if ($this->planosTable->delete($plano)) {
                return $this->jsonSuccess(null, 'Plano excluído com sucesso');
            }

            return $this->jsonError('Erro ao excluir plano', 500);
        } catch (\Exception $e) {
            Log::error('ERRO AdminPlanosController::delete: ' . $e->getMessage());
            return $this->jsonError($e->getMessage(), 500);
        }
    }
}