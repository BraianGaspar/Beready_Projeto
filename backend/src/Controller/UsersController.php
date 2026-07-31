<?php

declare(strict_types=1);

namespace App\Controller;

use App\Services\UserService;
use App\Services\JwtService;
use App\Repositories\UserRepository;
use App\Exceptions\EmailAlreadyExistsException;
use App\Exceptions\WeakPasswordException;
use App\Exceptions\InvalidTokenException;
use App\Exceptions\UserNotFoundException;
use Cake\Http\Client;
use App\Services\PermissionService;

class UsersController extends AppController
{
    private UserService $userService;
    private JwtService $jwtService;
    private PermissionService $permissionService;

    public function initialize(): void
    {
        parent::initialize();
        $this->userService = new UserService(new UserRepository());
        $this->jwtService = new JwtService();
        $this->permissionService = new PermissionService();
    }

    /**
     * GET /user/permissions
     * Retorna as permissões do usuário autenticado
     */
    public function permissions()
    {
        $this->request->allowMethod(['get']);
        
        $token = $this->jwtService->getTokenFromRequest($this->request);
        if (!$token) {
            return $this->jsonError('Token não informado', 401);
        }

        $payload = $this->jwtService->validateToken($token);
        if (!$payload) {
            return $this->jsonError('Token inválido ou expirado', 401);
        }

        try {
            $user = $this->userService->getUserById($payload['sub']);
            
            // Se for admin, retorna todas as permissões
            if ($user['role'] === 'admin') {
                $allPermissions = $this->permissionService->getAllPermissions();
                return $this->jsonSuccess($allPermissions);
            }

            // Busca permissões do usuário
            $permissions = $this->permissionService->getUserPermissions($user['id']);
            return $this->jsonSuccess($permissions);
        } catch (UserNotFoundException $e) {
            return $this->jsonError('Usuário não encontrado', 404);
        } catch (\Exception $e) {
            return $this->jsonError('Erro ao carregar permissões: ' . $e->getMessage(), 500);
        }
    }

    public function health()
    {
        return $this->jsonSuccess(['timestamp' => date('Y-m-d H:i:s')], 'API funcionando!');
    }

    public function register()
    {
        $data = $this->getRequestData();
        try {
            $user = $this->userService->register($data);
            return $this->jsonSuccess(['user' => $user], 'Registro realizado com sucesso', 201);
        } catch (EmailAlreadyExistsException | WeakPasswordException | \InvalidArgumentException $e) {
            return $this->jsonError($e->getMessage(), $e->getCode() ?: 400);
        } catch (\Exception $e) {
            return $this->jsonError('Erro interno: ' . $e->getMessage(), 500);
        }
    }

    public function login()
    {
        $data = $this->getRequestData();
        
        if (empty($data['email']) || empty($data['password'])) {
            return $this->jsonError('E-mail e senha são obrigatórios', 400);
        }

        // VALIDA O reCAPTCHA
        $recaptchaToken = $data['recaptcha_token'] ?? null;
        
        if (!$recaptchaToken) {
            return $this->jsonError('Token de segurança não enviado', 400);
        }
        
        $isValid = $this->validateRecaptcha($recaptchaToken);
        
        if (!$isValid) {
            return $this->jsonError('Falha na verificação de segurança. Tente novamente.', 400);
        }

        try {
            $user = $this->userService->login($data['email'], $data['password']);
            $tokens = $this->jwtService->generateTokens($user);

            return $this->jsonSuccess([
                'user' => $user,
                'tokens' => $tokens
            ], 'Login realizado com sucesso');
        } catch (\RuntimeException $e) {
            return $this->jsonError($e->getMessage(), $e->getCode() ?: 401);
        } catch (\Exception $e) {
            return $this->jsonError('Erro interno', 500);
        }
    }

    /**
     * Valida o token do reCAPTCHA v3
     */
    private function validateRecaptcha(string $token): bool
    {
        $secretKey = env('RECAPTCHA_SECRET_KEY');
        $verifyUrl = env('RECAPTCHA_VERIFY_URL');
        
        if (empty($secretKey)) {
            return false;
        }

        $http = new Client();
        $response = $http->post($verifyUrl, [
            'secret' => $secretKey,
            'response' => $token,
        ]);

        if (!$response->isOk()) {
            return false;
        }

        $data = $response->getJson();

        if (!$data['success']) {
            $errorCodes = implode(', ', $data['error-codes'] ?? []);
            return false;
        }

        if (isset($data['score']) && $data['score'] < 0.5) {
            return false;
        }

        return true;
    }

    public function refresh()
    {
        $data = $this->getRequestData();
        $refreshToken = $data['refresh_token'] ?? null;

        if (!$refreshToken) {
            return $this->jsonError('Refresh token não informado', 400);
        }

        $newTokens = $this->jwtService->refreshAccessToken($refreshToken);

        if (!$newTokens) {
            return $this->jsonError('Refresh token inválido ou expirado', 401);
        }

        return $this->jsonSuccess($newTokens, 'Token renovado com sucesso');
    }

    public function me()
    {
        $token = $this->jwtService->getTokenFromRequest($this->request);

        if (!$token) {
            return $this->jsonError('Token não informado', 401);
        }

        $payload = $this->jwtService->validateToken($token);

        if (!$payload) {
            return $this->jsonError('Token inválido ou expirado', 401);
        }

        try {
            $user = $this->userService->getUserById($payload['sub']);
            return $this->jsonSuccess($user);
        } catch (UserNotFoundException $e) {
            return $this->jsonError('Usuário não encontrado', 404);
        }
    }

    public function logout()
    {
        // JWT é stateless, o logout é feito no frontend removendo os tokens
        return $this->jsonSuccess(null, 'Logout realizado com sucesso');
    }

    public function view($id = null)
    {
        $userId = $id ?? $this->request->getParam('id');
        if (!$userId) {
            return $this->jsonError('ID do usuário não informado', 400);
        }
        try {
            $user = $this->userService->getUserById((int)$userId);
            return $this->jsonSuccess(['user' => $user]);
        } catch (UserNotFoundException $e) {
            return $this->jsonError($e->getMessage(), 404);
        } catch (\Exception $e) {
            return $this->jsonError('Erro interno', 500);
        }
    }

    public function viewByUuid($uuid)
    {
        try {
            $user = $this->userService->getUserByUuid($uuid);
            return $this->jsonSuccess(['user' => $user]);
        } catch (UserNotFoundException $e) {
            return $this->jsonError($e->getMessage(), 404);
        } catch (\Exception $e) {
            return $this->jsonError('Erro interno', 500);
        }
    }

    public function update($id = null)
    {
        $userId = $id ?? $this->request->getParam('id');
        if (!$userId) {
            return $this->jsonError('ID do usuário não informado', 400);
        }
        $data = $this->getRequestData();
        try {
            $user = $this->userService->updateUser((int)$userId, $data);
            return $this->jsonSuccess(['user' => $user], 'Perfil atualizado com sucesso');
        } catch (UserNotFoundException $e) {
            return $this->jsonError($e->getMessage(), 404);
        } catch (EmailAlreadyExistsException | WeakPasswordException | \InvalidArgumentException $e) {
            return $this->jsonError($e->getMessage(), $e->getCode() ?: 400);
        } catch (\Exception $e) {
            return $this->jsonError('Erro interno: ' . $e->getMessage(), 500);
        }
    }

    public function delete($id = null)
    {
        $userId = $id ?? $this->request->getParam('id');
        if (!$userId) {
            return $this->jsonError('ID do usuário não informado', 400);
        }
        if ($userId == 1) {
            return $this->jsonError('Não é possível excluir o usuário administrador', 403);
        }
        try {
            $this->userService->deleteUser((int)$userId);
            return $this->jsonSuccess(null, 'Conta excluída com sucesso');
        } catch (UserNotFoundException $e) {
            return $this->jsonError($e->getMessage(), 404);
        } catch (\Exception $e) {
            return $this->jsonError('Erro interno', 500);
        }
    }

    public function forgotPassword()
    {
        $this->request->allowMethod(['post']);
        $data = $this->getRequestData();
        if (empty($data['email'])) {
            return $this->jsonError('E-mail é obrigatório', 400);
        }
        try {
            $this->userService->forgotPassword($data['email']);
            return $this->jsonSuccess(null, 'Se o e-mail existir, você receberá um link de recuperação');
        } catch (\Exception $e) {
            return $this->jsonError('Erro ao processar solicitação', 500);
        }
    }

    public function resetPassword($token = null)
    {
        $this->request->allowMethod(['post']);
        
        if (empty($token)) {
            return $this->jsonError('Token não fornecido', 400);
        }
        
        $data = $this->getRequestData();
        $newPassword = $data['senha'] ?? $data['password'] ?? '';
        
        if (empty($newPassword)) {
            return $this->jsonError('Nova senha é obrigatória', 400);
        }
        
        try {
            $this->userService->resetPassword($token, $newPassword);
            return $this->jsonSuccess(null, 'Senha redefinida com sucesso');
        } catch (InvalidTokenException $e) {
            return $this->jsonError($e->getMessage(), 400);
        } catch (WeakPasswordException $e) {
            return $this->jsonError($e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->jsonError('Erro interno ao redefinir senha', 500);
        }
    }

    public function notFound()
    {
        return $this->jsonError('Rota não encontrada', 404);
    }

    private function getRequestData(): array
    {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        if (!$data) {
            $data = $this->request->getData();
        }
        return $data;
    }
}
