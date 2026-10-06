<?php

declare(strict_types=1);

namespace App\Controller\Traits;

use App\Repositories\PromptRepository;

/**
 * Traduções, imagens e frases não têm usuario_id: o dono é o do prompt pai.
 * Usar apenas em subclasses de AppController.
 */
trait PromptOwnershipTrait
{
    /**
     * Prompt existe e pertence ao usuário atual (ou o usuário é admin).
     */
    protected function canAccessPrompt(int $promptId): bool
    {
        $prompt = (new PromptRepository())->findById($promptId);

        return $prompt !== null && $this->canAccessUser((int)$prompt['usuario_id']);
    }

    /**
     * Em uma edição, impede mover o registro para um prompt de outro usuário.
     */
    protected function canMoveToPrompt(array $registroAtual, array $data): bool
    {
        if (empty($data['prompt_id']) || (int)$data['prompt_id'] === (int)$registroAtual['prompt_id']) {
            return true;
        }

        return $this->canAccessPrompt((int)$data['prompt_id']);
    }
}
