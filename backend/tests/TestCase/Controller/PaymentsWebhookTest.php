<?php

declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Test\TestCase\ApiTestCase;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Query\SelectQuery;

/**
 * POST /payments/webhook com eventos assinados localmente (HMAC), sem chamar a API do Stripe.
 */
class PaymentsWebhookTest extends ApiTestCase
{
    private const WEBHOOK_SECRET = 'whsec_test_beready_phpunit';

    private EntityInterface $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setEnv('STRIPE_WEBHOOK_SECRET', self::WEBHOOK_SECRET);
        $this->user = $this->createUser();
        $this->withBearer(null);
    }

    public function testCheckoutPagoAtivaOPremium(): void
    {
        $gratuita = $this->insert('Assinaturas', [
            'usuario_id' => $this->user->id,
            'plano_id' => self::PLANO_GRATUITO,
            'status' => 'active',
            'is_ativo' => true,
        ]);

        $this->sendWebhook($this->checkoutCompleted('cs_test_ativa', 'anual'));

        $this->assertResponseOk();
        $this->assertTrue($this->responseJson()['data']['received']);

        $premium = $this->assinaturasDoPagamento('cs_test_ativa')->firstOrFail();
        $this->assertSame(self::PLANO_PREMIUM, $premium->plano_id);
        $this->assertSame('active', $premium->status);
        $this->assertSame('stripe', $premium->payment_gateway);
        $diasRestantes = (int)round(($premium->data_fim->getTimestamp() - time()) / 86400);
        $this->assertGreaterThanOrEqual(365, $diasRestantes);

        $this->assertSame('canceled', $this->fetch('Assinaturas', $gratuita->id)->status);
    }

    public function testReenvioDoMesmoEventoNaoDuplicaAssinatura(): void
    {
        $payload = $this->checkoutCompleted('cs_test_idempotente', 'mensal');

        $this->sendWebhook($payload);
        $this->assertResponseOk();
        $primeira = $this->assinaturasDoPagamento('cs_test_idempotente')->firstOrFail();

        $this->sendWebhook($payload);
        $this->assertResponseOk();

        $this->assertSame(1, $this->assinaturasDoPagamento('cs_test_idempotente')->count());
        $this->assertSame(1, $this->getTableLocator()->get('Assinaturas')->find()
            ->where(['usuario_id' => $this->user->id, 'status' => 'active'])
            ->count());
        $this->assertSame('active', $this->fetch('Assinaturas', $primeira->id)->status);
    }

    public function testAssinaturaInvalidaRetorna400(): void
    {
        $payload = $this->checkoutCompleted('cs_test_forjado', 'mensal');

        // Assinado com outro segredo
        $this->sendWebhook($payload, 'whsec_de_um_atacante');
        $this->assertResponseCode(400);

        // Sem o header Stripe-Signature
        $this->post('/payments/webhook', $payload);
        $this->assertResponseCode(400);

        // Payload alterado depois de assinado
        $assinatura = $this->signature($payload, self::WEBHOOK_SECRET);
        $this->configRequest(['headers' => ['Stripe-Signature' => $assinatura]]);
        $this->post('/payments/webhook', str_replace('cs_test_forjado', 'cs_test_trocado', $payload));
        $this->assertResponseCode(400);

        $this->assertSame(0, $this->getTableLocator()->get('Assinaturas')->find()->count());
    }

    public function testEventoAntigoForaDaToleranciaRetorna400(): void
    {
        $payload = $this->checkoutCompleted('cs_test_antigo', 'mensal');

        $this->sendWebhook($payload, self::WEBHOOK_SECRET, time() - 3600);

        $this->assertResponseCode(400);
        $this->assertSame(0, $this->getTableLocator()->get('Assinaturas')->find()->count());
    }

    public function testCheckoutNaoPagoNaoAtivaAssinatura(): void
    {
        $this->sendWebhook($this->checkoutCompleted('cs_test_pendente', 'mensal', 'unpaid'));

        $this->assertResponseOk();
        $this->assertSame(0, $this->getTableLocator()->get('Assinaturas')->find()->count());
    }

    public function testOutrosEventosSaoIgnorados(): void
    {
        $payload = (string)json_encode([
            'id' => 'evt_test_outro',
            'object' => 'event',
            'type' => 'payment_intent.created',
            'data' => ['object' => ['id' => 'pi_test', 'object' => 'payment_intent']],
        ]);

        $this->sendWebhook($payload);

        $this->assertResponseOk();
        $this->assertSame(0, $this->getTableLocator()->get('Assinaturas')->find()->count());
    }

    private function checkoutCompleted(string $sessionId, string $ciclo, string $paymentStatus = 'paid'): string
    {
        return (string)json_encode([
            'id' => 'evt_' . $sessionId,
            'object' => 'event',
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => $sessionId,
                    'object' => 'checkout.session',
                    'payment_status' => $paymentStatus,
                    'client_reference_id' => (string)$this->user->id,
                    'metadata' => [
                        'user_id' => (string)$this->user->id,
                        'plan_id' => (string)self::PLANO_PREMIUM,
                        'ciclo' => $ciclo,
                    ],
                ],
            ],
        ]);
    }

    private function sendWebhook(string $payload, string $secret = self::WEBHOOK_SECRET, ?int $timestamp = null): void
    {
        $this->configRequest(['headers' => ['Stripe-Signature' => $this->signature($payload, $secret, $timestamp)]]);
        $this->post('/payments/webhook', $payload);
    }

    /**
     * Mesmo formato do header Stripe-Signature: t=<timestamp>,v1=<hmac_sha256("<t>.<payload>", secret)>.
     */
    private function signature(string $payload, string $secret, ?int $timestamp = null): string
    {
        $timestamp ??= time();

        return sprintf('t=%d,v1=%s', $timestamp, hash_hmac('sha256', "{$timestamp}.{$payload}", $secret));
    }

    private function assinaturasDoPagamento(string $paymentId): SelectQuery
    {
        return $this->getTableLocator()->get('Assinaturas')->find()->where(['payment_id' => $paymentId]);
    }
}
