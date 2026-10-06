<?php

namespace App\Controller;

use App\Services\AssinaturaService;

class AssinaturasController extends AppController
{
    /**
     * GET /user/assinatura
     */
    public function current()
    {
        $this->request->allowMethod(['get']);

        $assinatura = (new AssinaturaService())->getAtivaOuGratuita($this->currentUserId());

        if (!$assinatura) {
            return $this->jsonError('Nenhuma assinatura ativa encontrada', 404);
        }

        return $this->jsonSuccess($assinatura);
    }
}
