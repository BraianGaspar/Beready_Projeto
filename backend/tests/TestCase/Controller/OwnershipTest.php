<?php

declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Test\TestCase\ApiTestCase;
use Cake\Datasource\EntityInterface;

/**
 * IDOR: um usuário comum (A) não pode ler/alterar/excluir dados de outro usuário (B).
 * Usuários respondem 403; recursos de outro dono respondem 404, como se não existissem.
 */
class OwnershipTest extends ApiTestCase
{
    private EntityInterface $userA;
    private EntityInterface $userB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userA = $this->createUser();
        $this->userB = $this->createUser();
        $this->actingAs($this->userA);
    }

    // ---------------------------------------------------------------- usuários

    public function testNaoVeOutroUsuario(): void
    {
        $this->get('/users/' . $this->userB->id);
        $this->assertResponseCode(403);
        $this->assertArrayNotHasKey('data', $this->responseJson());

        $this->get('/users/view/' . $this->userB->id);
        $this->assertResponseCode(403);

        $this->get('/users/' . $this->userB->uuid);
        $this->assertResponseCode(403);
    }

    public function testNaoEditaOutroUsuario(): void
    {
        $this->putJson('/users/update/' . $this->userB->id, ['nome' => 'Invadido', 'email' => 'invadido@teste.local']);

        $this->assertResponseCode(403);
        $userB = $this->fetch('Users', $this->userB->id);
        $this->assertSame($this->userB->nome, $userB->nome);
        $this->assertSame($this->userB->email, $userB->email);
    }

    public function testNaoExcluiOutroUsuario(): void
    {
        $this->delete('/users/delete/' . $this->userB->id);

        $this->assertResponseCode(403);
        $this->assertNotNull($this->fetch('Users', $this->userB->id));
    }

    public function testVeOProprioUsuario(): void
    {
        $this->get('/users/' . $this->userA->id);

        $this->assertResponseOk();
        $this->assertSame($this->userA->id, $this->responseJson()['data']['user']['id']);
    }

    public function testUpdateDoProprioUsuarioIgnoraCamposProtegidos(): void
    {
        $senhaHashOriginal = $this->userA->senha_hash;

        $this->putJson('/users/update/' . $this->userA->id, [
            'nome' => 'Nome Novo',
            'role' => 'admin',
            'status' => 'bloqueado',
            'senha_hash' => 'hash-forjado',
            'uuid' => '00000000-0000-4000-8000-000000000000',
        ]);

        $this->assertResponseOk();
        $user = $this->fetch('Users', $this->userA->id);
        $this->assertSame('Nome Novo', $user->nome);
        $this->assertSame('user', $user->role);
        $this->assertSame('ativo', $user->status);
        $this->assertSame($senhaHashOriginal, $user->senha_hash);
        $this->assertSame($this->userA->uuid, $user->uuid);
    }

    public function testUpdateDoProprioUsuarioTrocaSenhaPeloCampoSenha(): void
    {
        $this->putJson('/users/update/' . $this->userA->id, ['senha' => 'novaSenha123']);

        $this->assertResponseOk();
        $user = $this->fetch('Users', $this->userA->id);
        $this->assertTrue(password_verify('novaSenha123', $user->senha_hash));
        $this->assertArrayNotHasKey('senha_hash', $this->responseJson()['data']['user']);
    }

    // -------------------------------------------------------------- flashcards

    public function testFlashcardDeOutroUsuarioResponde404EFicaIntacto(): void
    {
        $flashcard = $this->insert('Flashcards', [
            'usuario_id' => $this->userB->id,
            'frente' => 'Frente B',
            'verso' => 'Verso B',
        ]);
        $uuid = $this->fetch('Flashcards', $flashcard->id)->uuid;

        $this->get('/flashcards/' . $flashcard->id);
        $this->assertResponseCode(404);
        $this->assertArrayNotHasKey('data', $this->responseJson());

        $this->get('/flashcards/' . $uuid);
        $this->assertResponseCode(404);

        $this->putJson('/flashcards/' . $flashcard->id, ['frente' => 'Alterada']);
        $this->assertResponseCode(404);

        $this->delete('/flashcards/' . $flashcard->id);
        $this->assertResponseCode(404);

        $atual = $this->fetch('Flashcards', $flashcard->id);
        $this->assertNotNull($atual);
        $this->assertSame('Frente B', $atual->frente);
        $this->assertSame($this->userB->id, $atual->usuario_id);
    }

    public function testListagemDeFlashcardsTrazSoOsDoUsuario(): void
    {
        $this->insert('Flashcards', ['usuario_id' => $this->userA->id, 'frente' => 'A1', 'verso' => 'v']);
        $this->insert('Flashcards', ['usuario_id' => $this->userA->id, 'frente' => 'A2', 'verso' => 'v']);
        $this->insert('Flashcards', ['usuario_id' => $this->userB->id, 'frente' => 'B1', 'verso' => 'v']);

        $this->get('/flashcards');

        $this->assertResponseOk();
        $data = $this->responseJson()['data'];
        $this->assertCount(2, $data);
        $this->assertSame([$this->userA->id], array_values(array_unique(array_column($data, 'usuario_id'))));
    }

    public function testCriarFlashcardIgnoraUsuarioIdDoCorpo(): void
    {
        $this->postJson('/flashcards', [
            'frente' => 'Pergunta',
            'verso' => 'Resposta',
            'usuario_id' => $this->userB->id,
        ]);

        $this->assertResponseCode(201);
        $flashcard = $this->fetch('Flashcards', $this->responseJson()['data']['id']);
        $this->assertSame($this->userA->id, $flashcard->usuario_id);
    }

    public function testEditarFlashcardProprioNaoTransfereParaOutroUsuario(): void
    {
        $flashcard = $this->insert('Flashcards', ['usuario_id' => $this->userA->id, 'frente' => 'A', 'verso' => 'v']);

        $this->putJson('/flashcards/' . $flashcard->id, ['frente' => 'A editada', 'usuario_id' => $this->userB->id]);

        $this->assertResponseOk();
        $atual = $this->fetch('Flashcards', $flashcard->id);
        $this->assertSame('A editada', $atual->frente);
        $this->assertSame($this->userA->id, $atual->usuario_id);
    }

    // ------------------------------------------------------------------ quizes

    public function testQuizPrivadoDeOutroUsuarioResponde404EFicaIntacto(): void
    {
        $quiz = $this->insert('Quizes', [
            'usuario_id' => $this->userB->id,
            'titulo' => 'Quiz B',
            'publico' => false,
        ]);

        $this->get('/quizes/' . $quiz->id);
        $this->assertResponseCode(404);

        $this->putJson('/quizes/' . $quiz->id, ['titulo' => 'Alterado']);
        $this->assertResponseCode(404);

        $this->delete('/quizes/' . $quiz->id);
        $this->assertResponseCode(404);

        $atual = $this->fetch('Quizes', $quiz->id);
        $this->assertNotNull($atual);
        $this->assertSame('Quiz B', $atual->titulo);
    }

    public function testQuizPublicoDeOutroUsuarioPodeSerLidoMasNaoAlterado(): void
    {
        $quiz = $this->insert('Quizes', [
            'usuario_id' => $this->userB->id,
            'titulo' => 'Quiz público B',
            'publico' => true,
        ]);

        $this->get('/quizes/' . $quiz->id);
        $this->assertResponseOk();

        $this->putJson('/quizes/' . $quiz->id, ['titulo' => 'Alterado']);
        $this->assertResponseCode(404);

        $this->delete('/quizes/' . $quiz->id);
        $this->assertResponseCode(404);

        $this->assertSame('Quiz público B', $this->fetch('Quizes', $quiz->id)->titulo);
    }

    public function testListagemDeQuizesTrazSoOsDoUsuario(): void
    {
        $this->insert('Quizes', ['usuario_id' => $this->userA->id, 'titulo' => 'A1']);
        $this->insert('Quizes', ['usuario_id' => $this->userB->id, 'titulo' => 'B1', 'publico' => true]);

        $this->get('/quizes');

        $this->assertResponseOk();
        $this->assertSame(['A1'], array_column($this->responseJson()['data'], 'titulo'));
    }

    public function testCriarQuizIgnoraUsuarioIdDoCorpo(): void
    {
        $this->postJson('/quizes', ['titulo' => 'Meu quiz', 'usuario_id' => $this->userB->id]);

        $this->assertResponseCode(201);
        $quiz = $this->fetch('Quizes', $this->responseJson()['data']['id']);
        $this->assertSame($this->userA->id, $quiz->usuario_id);
    }

    // ----------------------------------------------------------------- prompts

    public function testPromptDeOutroUsuarioResponde404EFicaIntacto(): void
    {
        $prompt = $this->insert('Prompts', [
            'usuario_id' => $this->userB->id,
            'texto_original' => 'Texto B',
        ]);

        $this->get('/prompts/' . $prompt->id);
        $this->assertResponseCode(404);

        $this->putJson('/prompts/' . $prompt->id, ['texto_original' => 'Alterado']);
        $this->assertResponseCode(404);

        $this->delete('/prompts/' . $prompt->id);
        $this->assertResponseCode(404);

        $atual = $this->fetch('Prompts', $prompt->id);
        $this->assertNotNull($atual);
        $this->assertSame('Texto B', $atual->texto_original);
    }

    public function testPromptsPorUsuarioDeOutroUsuarioResponde403(): void
    {
        $this->insert('Prompts', ['usuario_id' => $this->userB->id, 'texto_original' => 'Texto B']);

        $this->get('/prompts/usuario/' . $this->userB->id);

        $this->assertResponseCode(403);
        $this->assertArrayNotHasKey('data', $this->responseJson());
    }

    public function testListagemDePromptsTrazSoOsDoUsuario(): void
    {
        $this->insert('Prompts', ['usuario_id' => $this->userA->id, 'texto_original' => 'A1']);
        $this->insert('Prompts', ['usuario_id' => $this->userB->id, 'texto_original' => 'B1']);

        $this->get('/prompts');

        $this->assertResponseOk();
        $this->assertSame(['A1'], array_column($this->responseJson()['data'], 'texto_original'));
    }

    public function testCriarPromptIgnoraUsuarioIdDoCorpo(): void
    {
        $this->postJson('/prompts', ['texto_original' => 'Olá', 'usuario_id' => $this->userB->id]);

        $this->assertResponseCode(201);
        $prompt = $this->fetch('Prompts', $this->responseJson()['data']['id']);
        $this->assertSame($this->userA->id, $prompt->usuario_id);
    }

    // -------------------------------------------------------------------- tags

    public function testTagDeOutroUsuarioResponde404EFicaIntacta(): void
    {
        $tag = $this->insert('Tags', ['criado_por' => $this->userB->id, 'nome' => 'tag-b', 'tag_sistema' => false]);

        $this->get('/tags/' . $tag->id);
        $this->assertResponseCode(404);

        $this->putJson('/tags/' . $tag->id, ['nome' => 'alterada']);
        $this->assertResponseCode(404);

        $this->delete('/tags/' . $tag->id);
        $this->assertResponseCode(404);

        $atual = $this->fetch('Tags', $tag->id);
        $this->assertNotNull($atual);
        $this->assertSame('tag-b', $atual->nome);
    }

    public function testTagsPorUsuarioDeOutroUsuarioResponde403(): void
    {
        $this->insert('Tags', ['criado_por' => $this->userB->id, 'nome' => 'tag-b']);

        $this->get('/tags/usuario/' . $this->userB->id);

        $this->assertResponseCode(403);
        $this->assertArrayNotHasKey('data', $this->responseJson());
    }

    public function testListagemDeTagsTrazAsDoUsuarioEAsDeSistema(): void
    {
        $this->insert('Tags', ['criado_por' => $this->userA->id, 'nome' => 'tag-a']);
        $this->insert('Tags', ['criado_por' => $this->userB->id, 'nome' => 'tag-b']);
        $this->insert('Tags', ['criado_por' => null, 'nome' => 'tag-sistema', 'tag_sistema' => true]);

        $this->get('/tags');

        $this->assertResponseOk();
        $this->assertSame(['tag-a', 'tag-sistema'], array_column($this->responseJson()['data'], 'nome'));
    }

    public function testCriarTagIgnoraCriadoPorETagSistemaDoCorpo(): void
    {
        $this->postJson('/tags', ['nome' => 'minha-tag', 'criado_por' => $this->userB->id, 'tag_sistema' => true]);

        $this->assertResponseCode(201);
        $tag = $this->fetch('Tags', $this->responseJson()['data']['id']);
        $this->assertSame($this->userA->id, $tag->criado_por);
        $this->assertFalse((bool)$tag->tag_sistema);
    }

    public function testNomeDeTagEUnicoPorUsuario(): void
    {
        $this->insert('Tags', ['criado_por' => $this->userB->id, 'nome' => 'verbos']);

        // Mesmo nome da tag de outro usuário: permitido
        $this->postJson('/tags', ['nome' => 'verbos']);
        $this->assertResponseCode(201);

        // Repetir o próprio nome: conflito
        $this->postJson('/tags', ['nome' => 'verbos']);
        $this->assertResponseCode(409);
    }

    // --------------------------------------------------------------- progresso

    public function testProgressoDeOutroUsuarioResponde403(): void
    {
        $this->get('/progresso/usuario/' . $this->userB->id);

        $this->assertResponseCode(403);
        $this->assertArrayNotHasKey('data', $this->responseJson());
    }

    public function testProgressoDoProprioUsuario(): void
    {
        $this->get('/progresso/usuario/' . $this->userA->id);

        $this->assertResponseOk();
    }

    // ------------------------------------------------------------------- admin

    public function testAdminAcessaRecursosDeOutroUsuario(): void
    {
        $admin = $this->createUser('admin');
        $flashcard = $this->insert('Flashcards', ['usuario_id' => $this->userB->id, 'frente' => 'B', 'verso' => 'v']);

        $this->actingAs($admin);

        $this->get('/users/' . $this->userB->id);
        $this->assertResponseOk();

        $this->get('/flashcards/' . $flashcard->id);
        $this->assertResponseOk();

        $this->get('/tags/usuario/' . $this->userB->id);
        $this->assertResponseOk();
    }
}
