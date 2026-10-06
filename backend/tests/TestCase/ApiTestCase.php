<?php

declare(strict_types=1);

namespace App\Test\TestCase;

use App\Services\JwtService;
use Cake\Cache\Cache;
use Cake\Datasource\EntityInterface;
use Cake\TestSuite\EmailTrait;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * Base dos testes de integração da API.
 *
 * Roles, permissões e planos vêm dos seeds (config/Seeds), aplicados no tests/bootstrap.php.
 * Usuários e demais registros são criados pelos próprios testes; as fixtures limpam as tabelas.
 */
abstract class ApiTestCase extends TestCase
{
    // Troca os transports de e-mail por um transport de teste: nada é enviado de verdade
    use EmailTrait;
    use IntegrationTestTrait {
        _sendRequest as sendIntegrationRequest;
    }

    // IDs fixos de config/Seeds/PlanosSeed.php
    protected const PLANO_TRIAL = 1;
    protected const PLANO_GRATUITO = 2;
    protected const PLANO_PREMIUM = 3;

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
    ];

    /**
     * Variáveis de ambiente alteradas no teste, restauradas no tearDown.
     */
    private array $envBackup = [];

    /**
     * Headers enviados em todas as requisições do teste (ver withBearer).
     */
    private array $headers = [];

    protected function setUp(): void
    {
        parent::setUp();

        // Contadores do rate limit não podem vazar de um teste para outro
        Cache::clear('rate_limit');
        Cache::clear('social_login');
    }

    protected function tearDown(): void
    {
        foreach ($this->envBackup as $key => $value) {
            $this->writeEnv($key, $value);
        }
        $this->envBackup = [];

        parent::tearDown();
    }

    /**
     * Cria um usuário ativo com senha "secret123".
     */
    protected function createUser(string $role = 'user', array $data = []): EntityInterface
    {
        static $sequence = 0;
        static $senhaHash = null;
        $sequence++;
        // password_hash é lento; um hash só serve para todos os usuários de teste
        $senhaHash ??= password_hash('secret123', PASSWORD_DEFAULT);

        return $this->insert('Users', $data + [
            'nome' => "Usuário {$sequence}",
            'email' => "usuario{$sequence}_" . uniqid() . '@teste.local',
            'senha_hash' => $senhaHash,
            'role' => $role,
            'status' => 'ativo',
        ]);
    }

    /**
     * Insere um registro ignorando o _accessible da entidade (dados montados pelo próprio teste).
     */
    protected function insert(string $alias, array $data): EntityInterface
    {
        $table = $this->getTableLocator()->get($alias);
        $entity = $table->newEntity($data, ['accessibleFields' => ['*' => true]]);

        return $table->saveOrFail($entity);
    }

    protected function fetch(string $alias, int $id): ?EntityInterface
    {
        return $this->getTableLocator()->get($alias)->find()->where(['id' => $id])->first();
    }

    /**
     * Tokens gerados pelo JwtService real.
     *
     * @return array{access_token: string, refresh_token: string}
     */
    protected function tokensFor(EntityInterface $user, ?string $role = null): array
    {
        return (new JwtService())->generateTokens([
            'id' => $user->id,
            'email' => $user->email,
            'nome' => $user->nome,
            'role' => $role ?? $user->role,
        ]);
    }

    /**
     * Configura as próximas requisições como JSON, autenticadas com o access token do usuário.
     */
    protected function actingAs(EntityInterface $user, ?string $role = null): void
    {
        $this->withBearer($this->tokensFor($user, $role)['access_token']);
    }

    protected function withBearer(?string $token): void
    {
        $this->headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
        if ($token !== null) {
            $this->headers['Authorization'] = 'Bearer ' . $token;
        }
    }

    /**
     * O IntegrationTestTrait descarta os headers configurados após cada requisição;
     * reaplica os do withBearer() (headers extras de configRequest() têm prioridade).
     */
    protected function _sendRequest(array|string $url, string $method, array|string $data = []): void
    {
        $this->_request['headers'] = ($this->_request['headers'] ?? []) + $this->headers;

        $this->sendIntegrationRequest($url, $method, $data);
    }

    protected function postJson(string $url, array $data = []): void
    {
        $this->post($url, (string)json_encode($data));
    }

    protected function putJson(string $url, array $data = []): void
    {
        $this->put($url, (string)json_encode($data));
    }

    protected function responseJson(): array
    {
        return (array)json_decode((string)$this->_response->getBody(), true);
    }

    /**
     * Define uma variável de ambiente só durante o teste (env() lê $_SERVER, $_ENV e getenv).
     */
    protected function setEnv(string $key, ?string $value): void
    {
        if (!array_key_exists($key, $this->envBackup)) {
            $this->envBackup[$key] = $_SERVER[$key] ?? $_ENV[$key] ?? (getenv($key) === false ? null : getenv($key));
        }

        $this->writeEnv($key, $value);
    }

    private function writeEnv(string $key, ?string $value): void
    {
        if ($value === null) {
            unset($_SERVER[$key], $_ENV[$key]);
            putenv($key);

            return;
        }

        $_SERVER[$key] = $_ENV[$key] = $value;
        putenv("{$key}={$value}");
    }
}
