<?php

declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Contracts\StripeGatewayInterface;
use App\Test\TestCase\ApiTestCase;
use App\Test\TestCase\FakeStripeGateway;
use Cake\Datasource\EntityInterface;

/**
 * PlanosController::assinar/cancelar e AssinaturasController::current.
 * Planos pagos (Stripe) são cobertos pelo PaymentsWebhookTest, sem chamar a API do Stripe.
 */
class AssinaturasTest extends ApiTestCase
{
    private EntityInterface $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->actingAs($this->user);
    }

    public function testAssinarPlanoGratuitoAtivaAssinatura(): void
    {
        $this->postJson('/planos/' . self::PLANO_GRATUITO . '/assinar', ['ciclo' => 'mensal']);

        $this->assertResponseOk();
        $data = $this->responseJson()['data'];
        $this->assertFalse($data['requires_payment']);
        $this->assertNull($data['checkout_url']);

        $ativa = $this->assinaturaAtiva();
        $this->assertSame(self::PLANO_GRATUITO, $ativa->plano_id);
        $this->assertNull($ativa->data_fim);
        $this->assertNull($ativa->payment_id);
    }

    public function testAssinarTrialAtivaComDataFimEEncerraAnterior(): void
    {
        $gratuita = $this->criarAssinatura(self::PLANO_GRATUITO);

        $this->postJson('/planos/' . self::PLANO_TRIAL . '/assinar', ['ciclo' => 'mensal']);

        $this->assertResponseOk();
        $ativa = $this->assinaturaAtiva();
        $this->assertSame(self::PLANO_TRIAL, $ativa->plano_id);
        $this->assertNotNull($ativa->data_fim);
        $diasRestantes = (int)round(($ativa->data_fim->getTimestamp() - time()) / 86400);
        $this->assertSame(7, $diasRestantes);

        $anterior = $this->fetch('Assinaturas', $gratuita->id);
        $this->assertSame('canceled', $anterior->status);
        $this->assertFalse($anterior->is_ativo);
        $this->assertSame(1, $this->contarAtivas());
    }

    public function testTrialNaoPodeSerUsadoDuasVezes(): void
    {
        $this->postJson('/planos/' . self::PLANO_TRIAL . '/assinar', ['ciclo' => 'mensal']);
        $this->assertResponseOk();

        $this->postJson('/planos/' . self::PLANO_GRATUITO . '/assinar', ['ciclo' => 'mensal']);
        $this->assertResponseOk();

        $this->postJson('/planos/' . self::PLANO_TRIAL . '/assinar', ['ciclo' => 'mensal']);
        $this->assertResponseCode(409);
        $this->assertSame(self::PLANO_GRATUITO, $this->assinaturaAtiva()->plano_id);
    }

    public function testAssinarOPlanoAtualRetorna409(): void
    {
        $this->criarAssinatura(self::PLANO_GRATUITO);

        $this->postJson('/planos/' . self::PLANO_GRATUITO . '/assinar', ['ciclo' => 'anual']);

        $this->assertResponseCode(409);
        $this->assertSame(1, $this->getTableLocator()->get('Assinaturas')->find()->count());
    }

    public function testCicloAusenteOuInvalidoRetorna400(): void
    {
        $this->postJson('/planos/' . self::PLANO_GRATUITO . '/assinar');
        $this->assertResponseCode(400);

        $this->postJson('/planos/' . self::PLANO_GRATUITO . '/assinar', ['ciclo' => 'semanal']);
        $this->assertResponseCode(400);

        $this->assertSame(0, $this->getTableLocator()->get('Assinaturas')->find()->count());
    }

    public function testPlanoInexistenteRetorna404(): void
    {
        $this->postJson('/planos/999/assinar', ['ciclo' => 'mensal']);

        $this->assertResponseCode(404);
    }

    public function testPlanoInativoRetorna400(): void
    {
        // planos não é limpa entre testes (vem dos seeds): remove o registro extra no fim
        $plano = $this->insert('Planos', ['nome' => 'Plano Antigo', 'preco_mensal' => 0, 'preco_anual' => 0, 'is_ativo' => false]);

        try {
            $this->postJson('/planos/' . $plano->id . '/assinar', ['ciclo' => 'mensal']);
            $this->assertResponseCode(400);
        } finally {
            $this->getTableLocator()->get('Planos')->delete($plano);
        }
    }

    public function testCancelarSemPlanoPagoRetorna400(): void
    {
        // Sem assinatura nenhuma
        $this->postJson('/planos/cancelar');
        $this->assertResponseCode(400);

        // Só com o gratuito
        $gratuita = $this->criarAssinatura(self::PLANO_GRATUITO);
        $this->postJson('/planos/cancelar');
        $this->assertResponseCode(400);
        $this->assertSame('active', $this->fetch('Assinaturas', $gratuita->id)->status);
    }

    public function testCancelarPlanoPagoVoltaParaOGratuito(): void
    {
        $premium = $this->criarAssinatura(self::PLANO_PREMIUM, [
            'data_fim' => date('Y-m-d H:i:s', strtotime('+1 month')),
            'payment_id' => 'cs_test_cancelar',
            'payment_gateway' => 'stripe',
        ]);

        $this->postJson('/planos/cancelar');

        $this->assertResponseOk();
        $cancelada = $this->fetch('Assinaturas', $premium->id);
        $this->assertSame('canceled', $cancelada->status);
        $this->assertFalse($cancelada->is_ativo);
        $this->assertNotNull($cancelada->data_cancelamento);

        $this->assertSame(self::PLANO_GRATUITO, $this->assinaturaAtiva()->plano_id);
        $this->assertSame(1, $this->contarAtivas());
    }

    public function testAssinaturaAtualAtribuiOGratuitoAQuemNaoTem(): void
    {
        $this->get('/user/assinatura');

        $this->assertResponseOk();
        $data = $this->responseJson()['data'];
        $this->assertSame(self::PLANO_GRATUITO, $data['plano_id']);
        $this->assertSame('Gratuito', $data['plano']['nome']);
        $this->assertSame(1, $this->contarAtivas());

        // Chamar de novo não cria outra assinatura
        $this->get('/user/assinatura');
        $this->assertResponseOk();
        $this->assertSame(1, $this->getTableLocator()->get('Assinaturas')->find()->count());
    }

    public function testAssinaturaAtualDevolveAAssinaturaVigente(): void
    {
        $premium = $this->criarAssinatura(self::PLANO_PREMIUM, ['data_fim' => date('Y-m-d H:i:s', strtotime('+10 days'))]);

        $this->get('/user/assinatura');

        $this->assertResponseOk();
        $this->assertSame($premium->id, $this->responseJson()['data']['id']);
    }

    public function testAssinaturaComDataFimNoPassadoEhTratadaComoExpirada(): void
    {
        $vencida = $this->criarAssinatura(self::PLANO_PREMIUM, [
            'data_fim' => date('Y-m-d H:i:s', strtotime('-1 day')),
        ]);

        $this->get('/user/assinatura');

        $this->assertResponseOk();
        $this->assertSame(self::PLANO_GRATUITO, $this->responseJson()['data']['plano_id']);

        $expirada = $this->fetch('Assinaturas', $vencida->id);
        $this->assertSame('expired', $expirada->status);
        $this->assertFalse($expirada->is_ativo);
    }

    public function testAssinaturaExpiradaNaoPodeSerCancelada(): void
    {
        $this->criarAssinatura(self::PLANO_PREMIUM, ['data_fim' => date('Y-m-d H:i:s', strtotime('-1 day'))]);

        $this->postJson('/planos/cancelar');

        $this->assertResponseCode(400);
    }

    // ---- Premium recorrente (Stripe), com o gateway falso: nada chega à API do Stripe ----

    public function testAssinarPremiumCriaCheckoutRecorrenteEReutilizaOCustomer(): void
    {
        $stripe = $this->fakeStripe();

        $this->postJson('/planos/' . self::PLANO_PREMIUM . '/assinar', ['ciclo' => 'anual']);

        $this->assertResponseOk();
        $data = $this->responseJson()['data'];
        $this->assertTrue($data['requires_payment']);
        $this->assertStringStartsWith('https://checkout.stripe.test/', $data['checkout_url']);

        $this->assertCount(1, $stripe->chamadasDe('criarCustomer'));
        $customerId = $this->fetch('Users', $this->user->id)->stripe_customer_id;
        $this->assertNotEmpty($customerId);

        $params = $stripe->chamadasDe('criarCheckoutAssinatura')[0];
        $this->assertSame('subscription', $params['mode']);
        $this->assertSame($customerId, $params['customer']);
        $this->assertSame(['interval' => 'year'], $params['line_items'][0]['price_data']['recurring']);
        $this->assertSame(29990, $params['line_items'][0]['price_data']['unit_amount']);
        $metadata = ['user_id' => (string)$this->user->id, 'plan_id' => (string)self::PLANO_PREMIUM, 'ciclo' => 'anual'];
        $this->assertSame($metadata, $params['metadata']);
        $this->assertSame($metadata, $params['subscription_data']['metadata']);

        // Segundo checkout (ex.: desistiu e voltou, agora mensal): mesmo customer
        $this->postJson('/planos/' . self::PLANO_PREMIUM . '/assinar', ['ciclo' => 'mensal']);
        $this->assertResponseOk();
        $this->assertCount(1, $stripe->chamadasDe('criarCustomer'));
        $params = $stripe->chamadasDe('criarCheckoutAssinatura')[1];
        $this->assertSame($customerId, $params['customer']);
        $this->assertSame(['interval' => 'month'], $params['line_items'][0]['price_data']['recurring']);

        // Checkout não ativa nada: quem ativa é o webhook
        $this->assertSame(0, $this->contarAtivas());
    }

    public function testErroDoStripeNoCheckoutRetorna502(): void
    {
        $this->fakeStripe()->erro = new \Stripe\Exception\ApiConnectionException('sem rede');

        $this->postJson('/planos/' . self::PLANO_PREMIUM . '/assinar', ['ciclo' => 'mensal']);

        $this->assertResponseCode(502);
    }

    public function testComPremiumRecorrenteNaoTrocaDePlanoPeloAssinar(): void
    {
        $stripe = $this->fakeStripe();
        $premium = $this->premiumRecorrente();

        $this->postJson('/planos/' . self::PLANO_GRATUITO . '/assinar', ['ciclo' => 'mensal']);

        $this->assertResponseCode(409);
        $this->assertSame('active', $this->fetch('Assinaturas', $premium->id)->status);
        $this->assertSame([], $stripe->chamadas);
    }

    public function testCancelarPremiumRecorrenteAgendaNoFimDoPeriodo(): void
    {
        $stripe = $this->fakeStripe();
        $premium = $this->premiumRecorrente();

        $this->postJson('/planos/cancelar');

        $this->assertResponseOk();
        $this->assertSame([['subscriptionId' => 'sub_test_cancelar']], $stripe->chamadasDe('cancelarNoFimDoPeriodo'));

        $body = $this->responseJson();
        $this->assertTrue($body['data']['cancelamento_agendado']);
        $this->assertSame($premium->data_fim->getTimestamp(), strtotime($body['data']['ativo_ate']));
        $this->assertStringContainsString($premium->data_fim->format('d/m/Y'), $body['message']);
        $this->assertTrue($body['data']['assinatura']['cancelar_no_fim_periodo']);

        // Continua Premium até data_fim
        $atual = $this->fetch('Assinaturas', $premium->id);
        $this->assertSame('active', $atual->status);
        $this->assertTrue($atual->cancelar_no_fim_periodo);
        $this->assertSame($premium->id, $this->assinaturaAtiva()->id);

        // Cancelar de novo: já agendado
        $this->postJson('/planos/cancelar');
        $this->assertResponseCode(400);
        $this->assertCount(1, $stripe->chamadasDe('cancelarNoFimDoPeriodo'));
    }

    public function testCancelamentoAgendadoExpiraNoFimDoPeriodoSemCarencia(): void
    {
        $this->premiumRecorrente([
            'data_fim' => date('Y-m-d H:i:s', strtotime('-1 hour')),
            'cancelar_no_fim_periodo' => true,
        ]);

        $this->get('/user/assinatura');

        $this->assertSame(self::PLANO_GRATUITO, $this->responseJson()['data']['plano_id']);
    }

    public function testCancelarComErroDoStripeRetorna502ENaoAgenda(): void
    {
        $this->fakeStripe()->erro = new \Stripe\Exception\InvalidRequestException('No such subscription');
        $premium = $this->premiumRecorrente();

        $this->postJson('/planos/cancelar');

        $this->assertResponseCode(502);
        $this->assertFalse($this->fetch('Assinaturas', $premium->id)->cancelar_no_fim_periodo);
    }

    public function testPortalDevolveAUrlDoStripe(): void
    {
        $stripe = $this->fakeStripe();
        $this->setEnv('APP_BASE_URL', 'http://front.test/');
        $this->darCustomer('cus_test_portal');

        $this->postJson('/planos/portal');

        $this->assertResponseOk();
        $this->assertStringStartsWith('https://billing.stripe.test/', $this->responseJson()['data']['url']);
        $this->assertSame(
            [['customerId' => 'cus_test_portal', 'returnUrl' => 'http://front.test/planos']],
            $stripe->chamadasDe('criarSessaoPortal')
        );
    }

    public function testPortalSemCustomerRetorna400(): void
    {
        $stripe = $this->fakeStripe();

        $this->postJson('/planos/portal');

        $this->assertResponseCode(400);
        $this->assertSame([], $stripe->chamadas);
    }

    public function testPortalNaoConfiguradoNoStripeRetornaMensagemClara(): void
    {
        $this->fakeStripe()->erro = new \Stripe\Exception\InvalidRequestException(
            'No configuration provided and your test mode default configuration has not been created.'
        );
        $this->darCustomer('cus_test_sem_portal');

        $this->postJson('/planos/portal');

        $this->assertResponseCode(502);
        $this->assertStringContainsString('portal de pagamentos', $this->responseJson()['message']);
    }

    public function testCustomerIdNaoEhExpostoNemAtribuivel(): void
    {
        $this->darCustomer('cus_test_secreto');

        $this->get('/users/me');
        $this->assertStringNotContainsString('cus_test_secreto', (string)$this->_response->getBody());

        $user = $this->getTableLocator()->get('Users')->patchEntity(
            $this->fetch('Users', $this->user->id),
            ['stripe_customer_id' => 'cus_pirata']
        );
        $this->assertSame('cus_test_secreto', $user->stripe_customer_id);
    }

    private function fakeStripe(): FakeStripeGateway
    {
        $fake = new FakeStripeGateway();
        $this->mockService(StripeGatewayInterface::class, fn () => $fake);

        return $fake;
    }

    private function premiumRecorrente(array $data = []): EntityInterface
    {
        return $this->criarAssinatura(self::PLANO_PREMIUM, $data + [
            'data_fim' => date('Y-m-d H:i:s', strtotime('+20 days')),
            'payment_id' => 'cs_test_recorrente',
            'payment_gateway' => 'stripe',
            'stripe_subscription_id' => 'sub_test_cancelar',
            'stripe_status' => 'active',
            'cancelar_no_fim_periodo' => false,
        ]);
    }

    private function darCustomer(string $customerId): void
    {
        $users = $this->getTableLocator()->get('Users');
        $user = $this->fetch('Users', $this->user->id);
        $user->stripe_customer_id = $customerId;
        $users->saveOrFail($user);
    }

    private function criarAssinatura(int $planoId, array $data = []): EntityInterface
    {
        return $this->insert('Assinaturas', $data + [
            'usuario_id' => $this->user->id,
            'plano_id' => $planoId,
            'status' => 'active',
            'is_ativo' => true,
            'data_inicio' => date('Y-m-d H:i:s', strtotime('-1 day')),
        ]);
    }

    private function assinaturaAtiva(): ?EntityInterface
    {
        return $this->getTableLocator()->get('Assinaturas')->find()
            ->where(['usuario_id' => $this->user->id, 'status' => 'active'])
            ->first();
    }

    private function contarAtivas(): int
    {
        return $this->getTableLocator()->get('Assinaturas')->find()
            ->where(['usuario_id' => $this->user->id, 'status' => 'active'])
            ->count();
    }
}
