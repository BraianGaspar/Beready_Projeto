<?php

declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Test\TestCase\ApiTestCase;
use Cake\Datasource\EntityInterface;

/**
 * AdminMiddleware: o escopo /admin exige role admin no banco (não na role do token),
 * e a action não chega a executar para quem não é admin.
 */
class AdminAccessTest extends ApiTestCase
{
    private const ADMIN_GET_URLS = ['/admin/planos', '/admin/roles', '/admin/users', '/admin/stats', '/admin/permissions'];

    private EntityInterface $admin;
    private EntityInterface $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->createUser('admin');
        $this->user = $this->createUser();
    }

    public function testSemTokenRetorna401(): void
    {
        $this->withBearer(null);

        foreach (self::ADMIN_GET_URLS as $url) {
            $this->get($url);
            $this->assertResponseCode(401, $url);
        }
    }

    public function testUsuarioComumRecebe403SemDados(): void
    {
        $this->actingAs($this->user);

        foreach (self::ADMIN_GET_URLS as $url) {
            $this->get($url);
            $this->assertResponseCode(403, $url);

            $body = $this->responseJson();
            $this->assertFalse($body['success'], $url);
            $this->assertArrayNotHasKey('data', $body, $url);
        }
    }

    public function testRoleAdminNoTokenNaoBastaSemRoleAdminNoBanco(): void
    {
        $this->actingAs($this->user, 'admin');

        $this->get('/admin/users');

        $this->assertResponseCode(403);
        $this->assertArrayNotHasKey('data', $this->responseJson());
    }

    public function testUsuarioComumNaoExecutaActionsDeEscrita(): void
    {
        $this->actingAs($this->user);

        // Tentativa de se promover: a action não pode rodar
        $this->postJson('/admin/users/role', ['user_id' => $this->user->id, 'role' => 'admin']);
        $this->assertResponseCode(403);
        $this->assertSame('user', $this->fetch('Users', $this->user->id)->role);

        $this->postJson('/admin/planos', ['nome' => 'Plano Pirata', 'preco_mensal' => 0]);
        $this->assertResponseCode(403);
        $this->assertSame(0, $this->getTableLocator()->get('Planos')->find()->where(['nome' => 'Plano Pirata'])->count());

        $this->postJson('/admin/roles', ['nome' => 'role-pirata']);
        $this->assertResponseCode(403);
        $this->assertSame(0, $this->getTableLocator()->get('Roles')->find()->where(['nome' => 'role-pirata'])->count());

        $this->delete('/admin/planos/delete/' . self::PLANO_PREMIUM);
        $this->assertResponseCode(403);
        $this->assertNotNull($this->fetch('Planos', self::PLANO_PREMIUM));
    }

    public function testAdminAcessaAsRotasAdministrativas(): void
    {
        $this->actingAs($this->admin);

        $this->get('/admin/planos');
        $this->assertResponseOk();
        $this->assertCount(3, $this->responseJson()['data']);

        $this->get('/admin/roles');
        $this->assertResponseOk();
        $this->assertContains('admin', array_column($this->responseJson()['data'], 'nome'));

        $this->get('/admin/users');
        $this->assertResponseOk();
        $ids = array_column($this->responseJson()['data'], 'id');
        $this->assertSame([$this->admin->id, $this->user->id], $ids);
        $this->assertArrayNotHasKey('senha_hash', $this->responseJson()['data'][0]);
    }

    public function testAdminAlteraRoleDeOutroUsuario(): void
    {
        $this->actingAs($this->admin);

        $this->postJson('/admin/users/role', ['user_id' => $this->user->id, 'role' => 'admin']);

        $this->assertResponseOk();
        $this->assertSame('admin', $this->fetch('Users', $this->user->id)->role);
    }

    public function testAdminNaoPodeRebaixarASiMesmo(): void
    {
        $this->actingAs($this->admin);

        $this->postJson('/admin/users/role', ['user_id' => $this->admin->id, 'role' => 'user']);
        $this->assertResponseCode(403);
        $this->assertSame('admin', $this->fetch('Users', $this->admin->id)->role);

        $this->putJson('/users/update/' . $this->admin->id, ['role' => 'user']);
        $this->assertResponseCode(403);
        $this->assertSame('admin', $this->fetch('Users', $this->admin->id)->role);
    }

    public function testAdminAlteraRoleEStatusPeloUpdateDeUsuario(): void
    {
        $this->actingAs($this->admin);

        $this->putJson('/users/update/' . $this->user->id, ['status' => 'bloqueado']);

        $this->assertResponseOk();
        $this->assertSame('bloqueado', $this->fetch('Users', $this->user->id)->status);
    }

    public function testRebaixamentoValeNaHoraMesmoComTokenAntigo(): void
    {
        $tokenAntigo = $this->tokensFor($this->admin)['access_token'];
        $this->getTableLocator()->get('Users')->updateAll(['role' => 'user'], ['id' => $this->admin->id]);

        $this->withBearer($tokenAntigo);
        $this->get('/admin/users');

        $this->assertResponseCode(403);
    }
}
