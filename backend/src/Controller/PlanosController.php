<?php

namespace App\Controller;

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use App\Services\AssinaturaService;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PlanosController extends AppController
{
    private $planosTable;
    private AssinaturaService $assinaturaService;

    public function initialize(): void
    {
        parent::initialize();
        $this->planosTable = TableRegistry::getTableLocator()->get('Planos');
        $this->assinaturaService = new AssinaturaService();
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
            Log::error('PlanosController::index: ' . $e->getMessage());
            return $this->jsonError('Erro ao listar planos', 500);
        }
    }

    /**
     * POST /planos/{id}/assinar
     * Planos sem custo são ativados na hora; planos pagos geram um checkout no Stripe
     * e a assinatura é ativada pelo webhook (PaymentsController::webhook).
     */
    public function assinar($id = null)
    {
        $this->request->allowMethod(['post']);

        $userId = $this->currentUserId();

        $ciclo = $this->request->getData('ciclo');
        if (!in_array($ciclo, AssinaturaService::CICLOS, true)) {
            return $this->jsonError('Ciclo de cobrança inválido. Use: ' . implode(', ', AssinaturaService::CICLOS), 400);
        }

        try {
            $plano = $this->planosTable->get((int)$id);
        } catch (RecordNotFoundException $e) {
            return $this->jsonError('Plano não encontrado', 404);
        }

        if (!$plano->is_ativo) {
            return $this->jsonError('Plano indisponível', 400);
        }

        $atual = $this->assinaturaService->getAtiva($userId);
        if ($atual && (int)$atual->plano_id === (int)$plano->id) {
            return $this->jsonError('Você já possui este plano', 409);
        }

        if ($this->assinaturaService->jaUsouTrial($userId, $plano)) {
            return $this->jsonError('Você já utilizou o período de teste deste plano', 409);
        }

        $preco = $this->assinaturaService->getPreco($plano, $ciclo);

        if ($preco <= 0) {
            try {
                $assinatura = $this->assinaturaService->ativar($userId, $plano, $ciclo);
            } catch (\Exception $e) {
                Log::error('ERRO ao ativar plano: ' . $e->getMessage());
                return $this->jsonError('Erro ao ativar plano', 500);
            }

            return $this->jsonSuccess([
                'requires_payment' => false,
                'checkout_url' => null,
                'assinatura' => $assinatura,
            ], 'Plano ativado com sucesso!');
        }

        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
        $frontendUrl = env('APP_BASE_URL');

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'brl',
                        'product_data' => [
                            'name' => sprintf('%s (%s)', $plano->nome, $ciclo),
                        ],
                        'unit_amount' => (int)round($preco * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'client_reference_id' => (string)$userId,
                'success_url' => $frontendUrl . 'dashboard?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $frontendUrl . 'planos?canceled=true',
                'metadata' => [
                    'user_id' => (string)$userId,
                    'plan_id' => (string)$plano->id,
                    'ciclo' => $ciclo,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('ERRO STRIPE: ' . $e->getMessage());
            return $this->jsonError('Erro ao gerar pagamento', 502);
        }

        return $this->jsonSuccess([
            'requires_payment' => true,
            'checkout_url' => $session->url,
        ], 'Redirecionando para o pagamento...');
    }

    /**
     * POST /planos/cancelar
     * Cancela a assinatura paga ativa e devolve o usuário ao plano gratuito.
     */
    public function cancelar()
    {
        $this->request->allowMethod(['post']);

        $userId = $this->currentUserId();

        try {
            $assinatura = $this->assinaturaService->cancelar($userId);
        } catch (\DomainException $e) {
            return $this->jsonError($e->getMessage(), 400);
        } catch (\Exception $e) {
            Log::error('ERRO ao cancelar assinatura: ' . $e->getMessage());
            return $this->jsonError('Erro ao cancelar assinatura', 500);
        }

        return $this->jsonSuccess(['assinatura' => $assinatura], 'Assinatura cancelada com sucesso');
    }
}
