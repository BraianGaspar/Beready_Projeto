<?php

declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Services\JwtService;
use App\Test\TestCase\ApiTestCase;
use Cake\Core\Configure;
use Firebase\JWT\JWT;

/**
 * JwtAuthMiddleware, rotas públicas e o ciclo refresh/logout do UsersController.
 */
class AuthenticationTest extends ApiTestCase
{
    public function testRotaProtegidaSemTokenRetorna401(): void
    {
        $this->withBearer(null);

        foreach (['/users/me', '/flashcards', '/user/assinatura', '/user/permissions'] as $url) {
            $this->get($url);
            $this->assertResponseCode(401, $url);
            $this->assertFalse($this->responseJson()['success']);
        }
    }

    public function testTokenMalformadoRetorna401(): void
    {
        $this->withBearer('isto.nao.e-um-jwt');
        $this->get('/users/me');

        $this->assertResponseCode(401);
    }

    public function testTokenAssinadoComOutroSegredoRetorna401(): void
    {
        $user = $this->createUser();
        $token = JWT::encode(
            ['sub' => $user->id, 'type' => JwtService::TYPE_ACCESS, 'iat' => time(), 'exp' => time() + 3600],
            'segredo-de-outro-sistema-com-32-bytes-ou-mais',
            Configure::read('Jwt.algorithm')
        );

        $this->withBearer($token);
        $this->get('/users/me');

        $this->assertResponseCode(401);
    }

    public function testTokenExpiradoRetorna401(): void
    {
        $user = $this->createUser();
        $token = JWT::encode(
            ['sub' => $user->id, 'type' => JwtService::TYPE_ACCESS, 'iat' => time() - 7200, 'exp' => time() - 3600],
            Configure::read('Jwt.secret'),
            Configure::read('Jwt.algorithm')
        );

        $this->withBearer($token);
        $this->get('/users/me');

        $this->assertResponseCode(401);
    }

    public function testRefreshTokenNaoServeComoAccessToken(): void
    {
        $user = $this->createUser();

        $this->withBearer($this->tokensFor($user)['refresh_token']);
        $this->get('/users/me');

        $this->assertResponseCode(401);
    }

    public function testAccessTokenValidoAcessaRotaProtegida(): void
    {
        $user = $this->createUser();

        $this->actingAs($user);
        $this->get('/users/me');

        $this->assertResponseOk();
        $body = $this->responseJson();
        $this->assertSame($user->id, $body['data']['id']);
        $this->assertArrayNotHasKey('senha_hash', $body['data']);
    }

    public function testListagemDePlanosEhPublica(): void
    {
        $this->withBearer(null);
        $this->get('/planos');

        $this->assertResponseOk();
        $nomes = array_column($this->responseJson()['data'], 'nome');
        $this->assertSame(['Gratuito', 'Trial', 'Premium'], $nomes);
    }

    public function testApenasGetDePlanosEhPublico(): void
    {
        $this->withBearer(null);
        $this->postJson('/planos/' . self::PLANO_GRATUITO . '/assinar', ['ciclo' => 'mensal']);

        $this->assertResponseCode(401);
    }

    public function testRefreshSemCookieRetorna401(): void
    {
        $this->withBearer(null);
        $this->post('/auth/refresh');

        $this->assertResponseCode(401);
    }

    public function testRefreshComCookieValidoRetornaNovoAccessToken(): void
    {
        $user = $this->createUser();

        $this->withBearer(null);
        $this->cookie('refresh_token', $this->tokensFor($user)['refresh_token']);
        $this->post('/auth/refresh');

        $this->assertResponseOk();
        $data = $this->responseJson()['data'];
        $this->assertNotEmpty($data['access_token']);
        $this->assertSame('Bearer', $data['token_type']);
        $this->assertSame($user->id, $data['user']['id']);
        $this->assertArrayNotHasKey('senha_hash', $data['user']);

        // O novo access token funciona nas rotas protegidas
        $payload = (new JwtService())->validateToken($data['access_token'], JwtService::TYPE_ACCESS);
        $this->assertSame($user->id, (int)$payload['sub']);
    }

    public function testRefreshComAccessTokenNoCookieRetorna401(): void
    {
        $user = $this->createUser();

        $this->withBearer(null);
        $this->cookie('refresh_token', $this->tokensFor($user)['access_token']);
        $this->post('/auth/refresh');

        $this->assertResponseCode(401);
    }

    public function testRefreshDeUsuarioBloqueadoRetorna401(): void
    {
        $user = $this->createUser('user', ['status' => 'bloqueado']);

        $this->withBearer(null);
        $this->cookie('refresh_token', $this->tokensFor($user)['refresh_token']);
        $this->post('/auth/refresh');

        $this->assertResponseCode(401);
    }

    public function testLogoutExpiraOCookieDeRefresh(): void
    {
        $this->withBearer(null);
        $this->post('/auth/logout');

        $this->assertResponseOk();
        $cookie = $this->_response->getCookieCollection()->get('refresh_token');
        $this->assertSame('', $cookie->getValue());
        $this->assertTrue($cookie->isExpired());
        $this->assertSame('/auth', $cookie->getPath());
        $this->assertTrue($cookie->isHttpOnly());
    }

    public function testSocialExchangeComCodigoInvalidoRetorna400(): void
    {
        $this->withBearer(null);

        // Formato inválido e formato válido mas inexistente
        foreach (['abc', str_repeat('a', 64)] as $code) {
            $this->postJson('/auth/social/exchange', ['code' => $code]);
            $this->assertResponseCode(400);
        }
    }

    public function testRegistroGravaSenhaIgnoraCamposProtegidosEAtribuiPlanoGratuito(): void
    {
        $this->withBearer(null);
        $this->postJson('/auth/register', [
            'nome' => 'Novo Usuário',
            'email' => 'novo@teste.local',
            'senha' => 'senhaForte1',
            'role' => 'admin',
            'status' => 'bloqueado',
            'senha_hash' => 'hash-forjado',
        ]);

        $this->assertResponseCode(201);
        $id = $this->responseJson()['data']['user']['id'];
        $this->assertArrayNotHasKey('senha_hash', $this->responseJson()['data']['user']);

        $user = $this->fetch('Users', $id);
        $this->assertSame('user', $user->role);
        $this->assertSame('ativo', $user->status);
        $this->assertTrue(password_verify('senhaForte1', $user->senha_hash));
        $this->assertNotEmpty($user->uuid);

        $assinatura = $this->getTableLocator()->get('Assinaturas')->find()
            ->where(['usuario_id' => $id, 'status' => 'active'])
            ->first();
        $this->assertSame(self::PLANO_GRATUITO, $assinatura->plano_id);

        $this->assertMailSentTo('novo@teste.local');
    }

    public function testRegistroComEmailDuplicadoRetorna409(): void
    {
        $existente = $this->createUser();

        $this->withBearer(null);
        $this->postJson('/auth/register', ['nome' => 'Outro', 'email' => $existente->email, 'senha' => 'senhaForte1']);

        $this->assertResponseCode(409);
    }

    public function testRateLimitDasRotasDeAutenticacao(): void
    {
        $this->withBearer(null);

        for ($i = 1; $i <= 10; $i++) {
            $this->post('/auth/refresh');
            $this->assertResponseCode(401);
        }

        $this->post('/auth/refresh');
        $this->assertResponseCode(429);
        $this->assertHeader('X-RateLimit-Remaining', '0');
    }
}
