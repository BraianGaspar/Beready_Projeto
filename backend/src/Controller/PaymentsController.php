<?php
declare(strict_types=1);

namespace App\Controller;

use App\Services\AssinaturaService;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class PaymentsController extends AppController
{
    /**
     * POST /payments/webhook
     * Endpoint público chamado pelo Stripe quando um checkout é concluído.
     * O checkout é criado em PlanosController::assinar.
     */
    public function webhook()
    {
        $this->request->allowMethod(['post']);

        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');
        if (empty($endpointSecret)) {
            Log::error('STRIPE_WEBHOOK_SECRET não configurado');
            return $this->jsonError('Webhook não configurado', 500);
        }

        $payload = (string)$this->request->getBody();
        $sigHeader = $this->request->getHeaderLine('Stripe-Signature');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (SignatureVerificationException | \UnexpectedValueException $e) {
            return $this->jsonError('Assinatura do webhook inválida', 400);
        }

        if ($event->type !== 'checkout.session.completed') {
            return $this->jsonSuccess(['received' => true]);
        }

        $session = $event->data->object;
        if ($session->payment_status !== 'paid') {
            return $this->jsonSuccess(['received' => true]);
        }

        $metadata = $session->metadata->toArray();
        $userId = (int)($metadata['user_id'] ?? 0);
        $planId = (int)($metadata['plan_id'] ?? 0);
        $ciclo = $metadata['ciclo'] ?? null;

        if (!$userId || !$planId || !in_array($ciclo, AssinaturaService::CICLOS, true)) {
            Log::error('Webhook Stripe com metadata inválida: ' . $session->id);
            return $this->jsonSuccess(['received' => true]);
        }

        $assinaturaService = new AssinaturaService();

        // O Stripe pode reenviar o mesmo evento
        if ($assinaturaService->pagamentoJaProcessado($session->id)) {
            return $this->jsonSuccess(['received' => true]);
        }

        try {
            $plano = TableRegistry::getTableLocator()->get('Planos')->get($planId);
            $assinaturaService->ativar($userId, $plano, $ciclo, $session->id);
        } catch (RecordNotFoundException $e) {
            Log::error("Webhook Stripe: plano {$planId} não encontrado");
            return $this->jsonSuccess(['received' => true]);
        } catch (\Exception $e) {
            // Retorna 500 para o Stripe tentar reenviar o evento
            Log::error('Webhook Stripe: erro ao ativar assinatura: ' . $e->getMessage());
            return $this->jsonError('Erro ao processar pagamento', 500);
        }

        return $this->jsonSuccess(['received' => true]);
    }
}
