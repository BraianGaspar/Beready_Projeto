<?php
declare(strict_types=1);

namespace App\Controller;

use App\Services\SocialAuthService;
use Cake\Http\Client;
use Cake\Http\Cookie\Cookie;
use Cake\Log\Log;

/**
 * Login social (Google, Facebook, LinkedIn) via OAuth 2.0 authorization code.
 *
 * GET /auth/login/{provider}          -> gera o "state", guarda em cookie httpOnly e redireciona ao provedor
 * GET /social-auth/callback/{provider} -> valida o "state", obtém o perfil e redireciona ao frontend
 *                                        com um código de uso único (trocado em POST /auth/social/exchange)
 */
class SocialAuthController extends AppController
{
    private const STATE_COOKIE = 'oauth_state';
    private const STATE_TTL = 600;

    private const SCOPES = [
        'google' => 'openid email profile',
        'facebook' => 'email,public_profile',
        'linkedin' => 'openid profile email',
    ];

    private SocialAuthService $socialAuthService;

    public function initialize(): void
    {
        parent::initialize();
        $this->socialAuthService = new SocialAuthService();
    }

    public function login(string $provider)
    {
        $state = bin2hex(random_bytes(32));
        $prefix = strtoupper($provider);

        $authUrl = env($prefix . '_AUTH_URL') . '?' . http_build_query([
            'client_id' => env($prefix . '_CLIENT_ID'),
            'redirect_uri' => env($prefix . '_REDIRECT_URI'),
            'response_type' => 'code',
            'scope' => self::SCOPES[$provider],
            'state' => $state,
        ]);

        return $this->redirect($authUrl)
            ->withCookie($this->stateCookie($state, new \DateTimeImmutable('+' . self::STATE_TTL . ' seconds')));
    }

    public function callback(string $provider)
    {
        $expectedState = (string)$this->request->getCookie(self::STATE_COOKIE);
        $state = (string)$this->request->getQuery('state');
        $code = (string)$this->request->getQuery('code');

        if ($expectedState === '' || $state === '' || !hash_equals($expectedState, $state) || $code === '') {
            return $this->failureRedirect('state inválido ou código ausente (' . $provider . ')');
        }

        try {
            $userInfo = $this->fetchUserInfo($provider, $code);
            if (empty($userInfo['email'])) {
                return $this->failureRedirect('perfil sem e-mail (' . $provider . ')');
            }

            $userId = $this->socialAuthService->findOrCreateUser($userInfo);
            $loginCode = $this->socialAuthService->createLoginCode($userId);
        } catch (\Exception $e) {
            return $this->failureRedirect($e->getMessage());
        }

        return $this->redirect(env('APP_BASE_URL') . 'oauth-callback?code=' . $loginCode)
            ->withExpiredCookie($this->stateCookie(''));
    }

    private function failureRedirect(string $reason)
    {
        Log::warning('Falha no login social: ' . $reason);

        return $this->redirect(env('APP_BASE_URL') . 'login?error=social_auth_failed')
            ->withExpiredCookie($this->stateCookie(''));
    }

    private function stateCookie(string $value, ?\DateTimeInterface $expires = null): Cookie
    {
        return Cookie::create(self::STATE_COOKIE, $value, [
            'expires' => $expires,
            'path' => '/social-auth',
            'httponly' => true,
            'secure' => filter_var(env('REFRESH_COOKIE_SECURE'), FILTER_VALIDATE_BOOLEAN),
            // Lax é necessário para o cookie voltar no redirecionamento (GET top-level) do provedor
            'samesite' => 'Lax',
        ]);
    }

    private function fetchUserInfo(string $provider, string $code): ?array
    {
        $tokenData = match ($provider) {
            'google' => $this->getGoogleAccessToken($code),
            'facebook' => $this->getFacebookAccessToken($code),
            'linkedin' => $this->getLinkedInAccessToken($code),
        };

        if (empty($tokenData['access_token'])) {
            return null;
        }

        return match ($provider) {
            'google' => $this->getGoogleUserInfo($tokenData['access_token']),
            'facebook' => $this->getFacebookUserInfo($tokenData['access_token']),
            'linkedin' => $this->getLinkedInUserInfo($tokenData['access_token']),
        };
    }

    // GOOGLE
    private function getGoogleAccessToken(string $code): ?array
    {
        $http = new Client();
        $response = $http->post(env('GOOGLE_TOKEN_URL'), [
            'code' => $code,
            'client_id' => env('GOOGLE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_CLIENT_SECRET'),
            'redirect_uri' => env('GOOGLE_REDIRECT_URI'),
            'grant_type' => 'authorization_code',
        ]);

        if ($response->isOk()) {
            return $response->getJson();
        }

        Log::error('Erro ao obter token Google: ' . $response->getStatusCode());
        return null;
    }

    private function getGoogleUserInfo(string $accessToken): ?array
    {
        $http = new Client();
        $response = $http->get(
            env('GOOGLE_USERINFO_URL'),
            [],
            ['headers' => ['Authorization' => 'Bearer ' . $accessToken]]
        );

        if ($response->isOk()) {
            return $response->getJson();
        }

        Log::error('Erro ao obter user info Google: ' . $response->getStatusCode());
        return null;
    }

    // FACEBOOK
    private function getFacebookAccessToken(string $code): ?array
    {
        $http = new Client();
        $response = $http->get(env('FACEBOOK_TOKEN_URL'), [
            'client_id' => env('FACEBOOK_CLIENT_ID'),
            'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
            'redirect_uri' => env('FACEBOOK_REDIRECT_URI'),
            'code' => $code,
        ]);

        if ($response->isOk()) {
            return $response->getJson();
        }

        Log::error('Erro ao obter token Facebook: ' . $response->getStatusCode());
        return null;
    }

    private function getFacebookUserInfo(string $accessToken): ?array
    {
        $http = new Client();
        $response = $http->get(env('FACEBOOK_USERINFO_URL'), [
            'fields' => 'id,name,email,picture',
            'access_token' => $accessToken,
        ]);

        if ($response->isOk()) {
            $data = $response->getJson();
            if (isset($data['picture']['data']['url'])) {
                $data['picture'] = $data['picture']['data']['url'];
            }
            return $data;
        }

        Log::error('Erro ao obter user info Facebook: ' . $response->getStatusCode());
        return null;
    }

    // LINKEDIN
    private function getLinkedInAccessToken(string $code): ?array
    {
        $http = new Client();
        $response = $http->post(env('LINKEDIN_TOKEN_URL'), [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => env('LINKEDIN_CLIENT_ID'),
            'client_secret' => env('LINKEDIN_CLIENT_SECRET'),
            'redirect_uri' => env('LINKEDIN_REDIRECT_URI'),
        ], ['headers' => ['Content-Type' => 'application/x-www-form-urlencoded']]);

        if ($response->isOk()) {
            return $response->getJson();
        }

        Log::error('Erro ao obter token LinkedIn: ' . $response->getStatusCode());
        return null;
    }

    private function getLinkedInUserInfo(string $accessToken): ?array
    {
        $http = new Client();
        $response = $http->get(
            env('LINKEDIN_USERINFO_URL'),
            [],
            ['headers' => ['Authorization' => 'Bearer ' . $accessToken]]
        );

        if ($response->isOk()) {
            return $response->getJson();
        }

        Log::error('Erro ao obter user info LinkedIn: ' . $response->getStatusCode());
        return null;
    }
}
