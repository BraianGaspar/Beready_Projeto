<?php

declare(strict_types=1);

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Cake\Core\Configure;
use Psr\Http\Message\ServerRequestInterface;

class JwtService
{
    public const TYPE_ACCESS = 'access';
    public const TYPE_REFRESH = 'refresh';

    private string $secret;
    private string $algorithm;
    private int $expires;
    private int $refreshExpires;

    public function __construct()
    {
        $this->secret = Configure::read('Jwt.secret');
        $this->algorithm = Configure::read('Jwt.algorithm');
        $this->expires = Configure::read('Jwt.expires');
        $this->refreshExpires = Configure::read('Jwt.refresh_expires');
    }

    /**
     * Gera o par access + refresh. O refresh token deve ir só para o cookie httpOnly.
     */
    public function generateTokens(array $user): array
    {
        $issuedAt = time();

        $refreshToken = JWT::encode(
            [
                'sub' => $user['id'],
                'iat' => $issuedAt,
                'exp' => $issuedAt + $this->refreshExpires,
                'type' => self::TYPE_REFRESH,
            ],
            $this->secret,
            $this->algorithm
        );

        return $this->generateAccessToken($user) + ['refresh_token' => $refreshToken];
    }

    public function generateAccessToken(array $user): array
    {
        $issuedAt = time();

        $accessToken = JWT::encode(
            [
                'sub' => $user['id'],
                'email' => $user['email'],
                'nome' => $user['nome'],
                'role' => $user['role'] ?? 'user',
                'iat' => $issuedAt,
                'exp' => $issuedAt + $this->expires,
                'type' => self::TYPE_ACCESS,
            ],
            $this->secret,
            $this->algorithm
        );

        return [
            'access_token' => $accessToken,
            'expires_in' => $this->expires,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Retorna o payload se a assinatura, a validade e o tipo (access/refresh) conferirem.
     */
    public function validateToken(string $token, string $expectedType): ?array
    {
        try {
            $payload = (array)JWT::decode($token, new Key($this->secret, $this->algorithm));
        } catch (\Exception $e) {
            return null;
        }

        if (($payload['type'] ?? null) !== $expectedType || empty($payload['sub'])) {
            return null;
        }

        return $payload;
    }

    public function getRefreshExpires(): int
    {
        return $this->refreshExpires;
    }

    public function getTokenFromRequest(ServerRequestInterface $request): ?string
    {
        if (preg_match('/^Bearer\s+(\S+)$/', $request->getHeaderLine('Authorization'), $matches)) {
            return $matches[1];
        }

        return null;
    }
}
