<?php

namespace App\Controller;

use Cake\Datasource\EntityInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;

/**
 * Acesso restrito a admins pelo AdminMiddleware (escopo /admin).
 */
class AdminPlanosController extends AppController
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
            // Mostra todos os planos, inclusive inativos
            $planos = $this->planosTable->find()
                ->contain(['Roles'])
                ->orderBy(['ordem' => 'ASC'])
                ->toArray();

            foreach ($planos as $plano) {
                $this->castPrecos($plano);
            }

            return $this->jsonSuccess($planos);
        } catch (\Exception $e) {
            Log::error('AdminPlanosController::index: ' . $e->getMessage());
            return $this->jsonError('Erro ao listar planos', 500);
        }
    }

    public function add()
    {
        try {
            $plano = $this->planosTable->newEntity($this->normalizeData($this->getRequestData()));

            if (!$this->planosTable->save($plano)) {
                return $this->validationError($plano);
            }

            return $this->jsonSuccess($this->getSaved($plano->id), 'Plano criado com sucesso');
        } catch (\Exception $e) {
            Log::error('AdminPlanosController::add: ' . $e->getMessage());
            return $this->jsonError('Erro ao criar plano', 500);
        }
    }

    public function edit($id)
    {
        try {
            $plano = $this->planosTable->get($id);
            $plano = $this->planosTable->patchEntity($plano, $this->normalizeData($this->getRequestData()));

            if (!$this->planosTable->save($plano)) {
                return $this->validationError($plano);
            }

            return $this->jsonSuccess($this->getSaved($plano->id), 'Plano atualizado com sucesso');
        } catch (RecordNotFoundException $e) {
            return $this->jsonError('Plano não encontrado', 404);
        } catch (\Exception $e) {
            Log::error('AdminPlanosController::edit: ' . $e->getMessage());
            return $this->jsonError('Erro ao atualizar plano', 500);
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
        } catch (RecordNotFoundException $e) {
            return $this->jsonError('Plano não encontrado', 404);
        } catch (\Exception $e) {
            Log::error('AdminPlanosController::delete: ' . $e->getMessage());
            return $this->jsonError('Erro ao excluir plano', 500);
        }
    }

    /**
     * Aceita "recursos" como texto separado por vírgula e "limites" como JSON em texto.
     */
    private function normalizeData(array $data): array
    {
        if (isset($data['recursos']) && is_string($data['recursos'])) {
            $data['recursos'] = array_map('trim', explode(',', $data['recursos']));
        }

        if (isset($data['limites']) && is_string($data['limites'])) {
            $data['limites'] = json_decode($data['limites'], true);
        }

        return $data;
    }

    private function getSaved(int $id): EntityInterface
    {
        return $this->castPrecos($this->planosTable->get($id, contain: ['Roles']));
    }

    private function castPrecos(EntityInterface $plano): EntityInterface
    {
        $plano->preco_mensal = (float)$plano->preco_mensal;
        $plano->preco_anual = (float)$plano->preco_anual;

        return $plano;
    }

    private function validationError(EntityInterface $entity)
    {
        $errorMessages = [];
        foreach ($entity->getErrors() as $field => $fieldErrors) {
            $errorMessages[] = $field . ': ' . implode(', ', $fieldErrors);
        }

        return $this->jsonError(implode('; ', $errorMessages), 400, $entity->getErrors());
    }
}
