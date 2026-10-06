<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Traits\PromptOwnershipTrait;
use App\Controller\Traits\ResourceErrorTrait;
use App\Repositories\TraducaoRepository;
use App\Services\TraducaoService;

class TraducoesController extends AppController
{
    use PromptOwnershipTrait;
    use ResourceErrorTrait;

    private TraducaoService $service;

    public function initialize(): void
    {
        parent::initialize();
        $this->service = new TraducaoService(new TraducaoRepository());
    }

    // GET /traducoes/prompt/{promptId}
    public function getByPrompt($promptId = null)
    {
        $promptId = $promptId ?? $this->request->getParam('promptId');

        if (!$promptId) {
            return $this->jsonError('ID do prompt não informado', 400);
        }

        try {
            if (!$this->canAccessPrompt((int)$promptId)) {
                return $this->jsonError('Prompt não encontrado', 404);
            }

            return $this->jsonSuccess($this->service->getTraducoesByPrompt((int)$promptId));
        } catch (\Exception $e) {
            return $this->errorResponse($e, 'Erro ao carregar traduções');
        }
    }

    // GET /traducoes/view/{id}
    public function view($id = null)
    {
        $traducaoId = $id ?? $this->request->getParam('id');

        if (!$traducaoId) {
            return $this->jsonError('ID da tradução não informado', 400);
        }

        try {
            return $this->jsonSuccess($this->findOwnedTraducao((int)$traducaoId));
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    // POST /traducoes
    public function add()
    {
        $data = $this->getRequestData();

        try {
            if (!empty($data['prompt_id']) && !$this->canAccessPrompt((int)$data['prompt_id'])) {
                return $this->jsonError('Prompt não encontrado', 404);
            }

            $traducao = $this->service->createTraducao($data);
            return $this->jsonSuccess($traducao, 'Tradução criada com sucesso', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e, 'Erro ao criar tradução');
        }
    }

    // PUT /traducoes/edit/{id}
    public function edit($id = null)
    {
        $traducaoId = $id ?? $this->request->getParam('id');

        if (!$traducaoId) {
            return $this->jsonError('ID da tradução não informado', 400);
        }

        $data = $this->getRequestData();

        try {
            $atual = $this->findOwnedTraducao((int)$traducaoId);
            if (!$this->canMoveToPrompt($atual, $data)) {
                return $this->jsonError('Prompt não encontrado', 404);
            }

            $traducao = $this->service->updateTraducao((int)$traducaoId, $data);
            return $this->jsonSuccess($traducao, 'Tradução atualizada com sucesso');
        } catch (\Exception $e) {
            return $this->errorResponse($e, 'Erro ao atualizar tradução');
        }
    }

    // DELETE /traducoes/delete/{id}
    public function delete($id = null)
    {
        $traducaoId = $id ?? $this->request->getParam('id');

        if (!$traducaoId) {
            return $this->jsonError('ID da tradução não informado', 400);
        }

        try {
            $this->findOwnedTraducao((int)$traducaoId);
            $this->service->deleteTraducao((int)$traducaoId);
            return $this->jsonSuccess(null, 'Tradução excluída com sucesso');
        } catch (\Exception $e) {
            return $this->errorResponse($e, 'Erro ao excluir tradução');
        }
    }

    private function findOwnedTraducao(int $id): array
    {
        $traducao = $this->service->getTraducaoById($id);
        if (!$this->canAccessPrompt((int)$traducao['prompt_id'])) {
            throw new \RuntimeException('Tradução não encontrada', 404);
        }

        return $traducao;
    }
}
