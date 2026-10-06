<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Traits\ResourceErrorTrait;
use Cake\ORM\TableRegistry;

class RespostasController extends AppController
{
    use ResourceErrorTrait;

    private $table;

    public function initialize(): void
    {
        parent::initialize();
        $this->table = TableRegistry::getTableLocator()->get('RespostasUsuario');
    }

    // POST /respostas
    // Body esperado: { tipo: 'flashcard'|'quiz', referencia_id, correto: true|false }
    // A resposta é sempre registrada para o usuário autenticado.
    public function save()
    {
        $data = $this->getRequestData();

        if (empty($data['tipo']) || empty($data['referencia_id']) || !isset($data['correto'])) {
            return $this->jsonError('Dados incompletos para registrar resposta', 400);
        }

        try {
            $data['usuario_id'] = $this->currentUserId();
            $entity = $this->table->newEntity($data);

            if ($this->table->save($entity)) {
                return $this->jsonSuccess($entity, 'Resposta registrada com sucesso');
            }

            return $this->jsonError('Erro ao registrar resposta', 422, $entity->getErrors());
        } catch (\Exception $e) {
            return $this->errorResponse($e, 'Erro ao registrar resposta');
        }
    }
}
