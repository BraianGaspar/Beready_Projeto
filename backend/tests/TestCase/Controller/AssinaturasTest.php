<?php

declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Test\TestCase\ApiTestCase;
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
