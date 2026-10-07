<?php

declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Test\TestCase\ApiTestCase;
use Cake\Chronos\Chronos;
use Cake\Datasource\EntityInterface;
use Cake\I18n\DateTime;

/**
 * Repetição espaçada: POST /flashcards/{id}/revisao e GET /flashcards/revisao.
 */
class FlashcardRevisaoTest extends ApiTestCase
{
    protected array $fixtures = [
        'app.Users',
        'app.Assinaturas',
        'app.Flashcards',
        'app.Quizes',
        'app.Prompts',
        'app.Tags',
        'app.ProgressoUsuario',
        'app.UsuarioRoles',
        'app.LogsPermissoes',
        'app.RespostasUsuario',
    ];

    private EntityInterface $userA;
    private EntityInterface $userB;
    private ?Chronos $testNowOriginal = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testNowOriginal = Chronos::getTestNow();
        $this->userA = $this->createUser();
        $this->userB = $this->createUser();
        $this->actingAs($this->userA);
    }

    protected function tearDown(): void
    {
        Chronos::setTestNow($this->testNowOriginal);

        parent::tearDown();
    }

    private function flashcard(EntityInterface $dono, array $data = []): EntityInterface
    {
        return $this->insert('Flashcards', $data + [
            'usuario_id' => $dono->id,
            'frente' => 'Frente',
            'verso' => 'Verso',
            // Relógio da aplicação (congelado no bootstrap), não o now() do banco
            'proxima_revisao' => DateTime::now(),
        ]);
    }

    public function testFlashcardNovoJaFicaDevido(): void
    {
        $this->postJson('/flashcards', ['frente' => 'Hello', 'verso' => 'Olá']);
        $this->assertResponseCode(201);
        $criado = $this->responseJson()['data'];
        $this->assertSame(0, $criado['repeticoes']);
        $this->assertSame(2.5, $criado['fator_ease']);
        $this->assertNotEmpty($criado['proxima_revisao']);

        $this->get('/flashcards/revisao');

        $this->assertResponseOk();
        $data = $this->responseJson()['data'];
        $this->assertSame(1, $data['total']);
        $this->assertSame([$criado['id']], array_column($data['flashcards'], 'id'));
    }

    public function testDevidosTrazSoOsDoUsuarioVencidosEOrdenados(): void
    {
        $agora = DateTime::now();
        $maisAntigo = $this->flashcard($this->userA, ['proxima_revisao' => $agora->subDays(3)]);
        $recente = $this->flashcard($this->userA, ['proxima_revisao' => $agora->subHours(1)]);
        $this->flashcard($this->userA, ['proxima_revisao' => $agora->addDays(2)]);
        $this->flashcard($this->userB, ['proxima_revisao' => $agora->subDays(5)]);

        $this->get('/flashcards/revisao');

        $this->assertResponseOk();
        $data = $this->responseJson()['data'];
        $this->assertSame(2, $data['total']);
        $this->assertSame([$maisAntigo->id, $recente->id], array_column($data['flashcards'], 'id'));

        // O limite corta a lista, mas a contagem continua total
        $this->get('/flashcards/revisao?limite=1');
        $data = $this->responseJson()['data'];
        $this->assertSame(2, $data['total']);
        $this->assertCount(1, $data['flashcards']);
    }

    public function testRevisaoBomReagendaRegistraRespostaEProgresso(): void
    {
        $flashcard = $this->flashcard($this->userA);

        $this->postJson("/flashcards/{$flashcard->id}/revisao", ['nota' => 'bom']);

        $this->assertResponseOk();
        $data = $this->responseJson()['data'];
        $this->assertSame(1, $data['repeticoes']);
        $this->assertSame(1, $data['intervalo_dias']);
        $this->assertSame(2.5, $data['fator_ease']);

        $atual = $this->fetch('Flashcards', $flashcard->id);
        $amanha = DateTime::now()->addDays(1)->startOfDay();
        $this->assertSame($amanha->format('Y-m-d H:i'), $atual->proxima_revisao->format('Y-m-d H:i'));
        $this->assertNotNull($atual->ultima_revisao);

        $resposta = $this->getTableLocator()->get('RespostasUsuario')->find()
            ->where(['usuario_id' => $this->userA->id])->first();
        $this->assertSame('flashcard', $resposta->tipo);
        $this->assertSame($flashcard->id, $resposta->referencia_id);
        $this->assertTrue($resposta->correto);

        $progresso = $this->getTableLocator()->get('ProgressoUsuario')->find()
            ->where(['usuario_id' => $this->userA->id])->first();
        $this->assertSame(1, $progresso->flashcards_concluidos);

        // Deixou de estar devido
        $this->get('/flashcards/revisao');
        $this->assertSame(0, $this->responseJson()['data']['total']);
    }

    public function testErreiVoltaNoMesmoDiaERegistraErro(): void
    {
        $flashcard = $this->flashcard($this->userA, ['repeticoes' => 4, 'intervalo_dias' => 20]);

        $this->postJson("/flashcards/{$flashcard->id}/revisao", ['nota' => 'errei']);

        $this->assertResponseOk();
        $data = $this->responseJson()['data'];
        $this->assertSame(0, $data['repeticoes']);
        $this->assertSame(0, $data['intervalo_dias']);

        $resposta = $this->getTableLocator()->get('RespostasUsuario')->find()
            ->where(['usuario_id' => $this->userA->id])->first();
        $this->assertFalse($resposta->correto);

        // Ainda não está devido agora, mas fica 11 minutos depois
        $this->get('/flashcards/revisao');
        $this->assertSame(0, $this->responseJson()['data']['total']);

        Chronos::setTestNow(Chronos::now()->addMinutes(11));
        $this->get('/flashcards/revisao');
        $this->assertSame(1, $this->responseJson()['data']['total']);
    }

    public function testNotaInvalidaResponde422(): void
    {
        $flashcard = $this->flashcard($this->userA);

        $this->postJson("/flashcards/{$flashcard->id}/revisao", ['nota' => 'dificil']);
        $this->assertResponseCode(422);
        $this->assertArrayHasKey('nota', $this->responseJson()['errors']);

        $this->postJson("/flashcards/{$flashcard->id}/revisao", []);
        $this->assertResponseCode(422);

        $this->assertSame(0, $this->fetch('Flashcards', $flashcard->id)->repeticoes);
    }

    public function testRevisaoDeFlashcardDeOutroUsuarioResponde404EFicaIntacto(): void
    {
        $flashcard = $this->flashcard($this->userB);

        $this->postJson("/flashcards/{$flashcard->id}/revisao", ['nota' => 'facil']);

        $this->assertResponseCode(404);
        $this->assertArrayNotHasKey('data', $this->responseJson());
        $atual = $this->fetch('Flashcards', $flashcard->id);
        $this->assertSame(0, $atual->repeticoes);
        $this->assertNull($atual->ultima_revisao);
        $this->assertSame(0, $this->getTableLocator()->get('RespostasUsuario')->find()->count());
    }

    public function testNemAdminRevisaFlashcardDeOutroUsuario(): void
    {
        $flashcard = $this->flashcard($this->userB);
        $this->actingAs($this->createUser('admin'));

        $this->postJson("/flashcards/{$flashcard->id}/revisao", ['nota' => 'bom']);

        $this->assertResponseCode(404);
        $this->assertSame(0, $this->fetch('Flashcards', $flashcard->id)->repeticoes);
    }

    public function testRevisaoDeFlashcardInexistenteResponde404(): void
    {
        $this->postJson('/flashcards/999999/revisao', ['nota' => 'bom']);

        $this->assertResponseCode(404);
    }

    public function testCamposDeAgendamentoNaoSaoAlteradosPeloCorpo(): void
    {
        $flashcard = $this->flashcard($this->userA);

        $this->putJson("/flashcards/{$flashcard->id}", [
            'frente' => 'Nova frente',
            'repeticoes' => 99,
            'intervalo_dias' => 365,
            'proxima_revisao' => '2099-01-01 00:00:00',
        ]);

        $this->assertResponseOk();
        $atual = $this->fetch('Flashcards', $flashcard->id);
        $this->assertSame('Nova frente', $atual->frente);
        $this->assertSame(0, $atual->repeticoes);
        $this->assertSame(0, $atual->intervalo_dias);
        $this->assertLessThanOrEqual(DateTime::now()->getTimestamp(), $atual->proxima_revisao->getTimestamp());
    }

    public function testSemTokenResponde401(): void
    {
        $this->withBearer(null);

        $this->get('/flashcards/revisao');
        $this->assertResponseCode(401);
    }
}
