<?php
declare(strict_types=1);

namespace App\Controller;

use App\Services\UserService;
use App\Repositories\UserRepository;
use Cake\ORM\TableRegistry;
use Cake\Mailer\MailerAwareTrait;

class UsersController extends AppController
{
    private $UsersTable;
    private UserService $userService;

    public function initialize(): void
    {
        parent::initialize();
        
        $this->UsersTable = TableRegistry::getTableLocator()->get('Users');
        $this->userService = new UserService(new UserRepository());
        
        $this->autoRender = false;
        $this->response = $this->response->withHeader('Content-Type', 'application/json');
    }

    public function health()
    {
        $this->response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'API funcionando!',
            'timestamp' => date('Y-m-d H:i:s')
        ]));
        return $this->response;
    }

    // REGISTER - Usando Clean Architecture
    public function register()
    {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (!$data) {
            $data = $this->request->getData();
        }
        
        try {
            $user = $this->userService->register($data);
            
            $this->response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Registro realizado com sucesso',
                'user' => $user
            ]));
            return $this->response;
            
        } catch (\InvalidArgumentException $e) {
            $this->response = $this->response->withStatus(400);
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]));
            return $this->response;
        } catch (\RuntimeException $e) {
            $code = $e->getCode() ?: 409;
            $this->response = $this->response->withStatus($code);
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]));
            return $this->response;
        } catch (\Exception $e) {
            $this->response = $this->response->withStatus(500);
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage()
            ]));
            return $this->response;
        }
    }
    
    // LOGIN - Usando Clean Architecture
    public function login()
    {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (!$data) {
            $data = $this->request->getData();
        }
        
        if (empty($data['email']) || empty($data['password'])) {
            $this->response = $this->response->withStatus(400);
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'E-mail e senha são obrigatórios'
            ]));
            return $this->response;
        }

        try {
            $user = $this->userService->login($data['email'], $data['password']);
            
            $this->response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Login realizado com sucesso',
                'user' => $user
            ]));
            return $this->response;
            
        } catch (\RuntimeException $e) {
            $code = $e->getCode() ?: 401;
            $this->response = $this->response->withStatus($code);
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]));
            return $this->response;
        } catch (\Exception $e) {
            $this->response = $this->response->withStatus(500);
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage()
            ]));
            return $this->response;
        }
    }
    
    public function logout()
    {
        $this->response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Logout realizado com sucesso'
        ]));
        return $this->response;
    }
    
    // GET /users/view/{id} e GET /users/{id}
    public function view($id = null)
    {
        $userId = $id ?? $this->request->getParam('id') ?? $this->request->getQuery('id');
        
        error_log("=== VIEW USER ===");
        error_log("ID recebido: " . $userId);
        error_log("ID do parâmetro: " . ($id ?? 'null'));
        error_log("ID do request param: " . ($this->request->getParam('id') ?? 'null'));
        
        if (!$userId) {
            $this->response = $this->response->withStatus(400);
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'ID do usuário não informado'
            ]));
            return $this->response;
        }
        
        try {
            $user = $this->userService->getUserById((int)$userId);
            
            $this->response->getBody()->write(json_encode([
                'success' => true,
                'user' => $user
            ]));
            return $this->response;
            
        } catch (\RuntimeException $e) {
            $code = $e->getCode() ?: 404;
            $this->response = $this->response->withStatus($code);
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]));
            return $this->response;
        } catch (\Exception $e) {
            $this->response = $this->response->withStatus(500);
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage()
            ]));
            return $this->response;
        }
    }
    
    public function update($id = null)
    {
        $userId = $id ?? $this->request->getParam('id');
        
        if (!$userId) {
            $this->response = $this->response->withStatus(400);
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'ID do usuário não informado'
            ]));
            return $this->response;
        }
        
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (!$data) {
            $data = $this->request->getData();
        }
        
        try {
            $user = $this->userService->updateUser((int)$userId, $data);
            
            $this->response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Perfil atualizado com sucesso',
                'user' => $user
            ]));
            return $this->response;
            
        } catch (\RuntimeException $e) {
            $code = $e->getCode() ?: 404;
            $this->response = $this->response->withStatus($code);
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]));
            return $this->response;
        } catch (\InvalidArgumentException $e) {
            $this->response = $this->response->withStatus(400);
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]));
            return $this->response;
        } catch (\Exception $e) {
            $this->response = $this->response->withStatus(500);
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage()
            ]));
            return $this->response;
        }
    }
    
    public function delete($id = null)
    {
        $userId = $id ?? $this->request->getParam('id');
        
        if (!$userId) {
            $this->response = $this->response->withStatus(400);
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'ID do usuário não informado'
            ]));
            return $this->response;
        }
        
        if ($userId == 1) {
            $this->response = $this->response->withStatus(403);
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Não é possível excluir o usuário administrador'
            ]));
            return $this->response;
        }
        
        try {
            $this->userService->deleteUser((int)$userId);
            
            $this->response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Conta excluída com sucesso'
            ]));
            return $this->response;
            
        } catch (\RuntimeException $e) {
            $code = $e->getCode() ?: 404;
            $this->response = $this->response->withStatus($code);
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]));
            return $this->response;
        } catch (\Exception $e) {
            $this->response = $this->response->withStatus(500);
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage()
            ]));
            return $this->response;
        }
    }

    public function forgotPassword()
    {
        $this->request->allowMethod(['post']);
        
        $email = $this->request->getData('email');
        $user = $this->Users->find()->where(['email' => $email])->first();
        
        if (!$user) {
            return $this->jsonError('E-mail não encontrado', 404);
        }
        
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $user->reset_token = $token;
        $user->reset_token_expires = $expires;
        
        if ($this->Users->save($user)) {
            // Tenta enviar e-mail, mas não para o fluxo se falhar
            try {
                $this->getMailer('User')->resetPassword($user, $token);
            } catch (\Exception $e) {
                // Log do erro mas não interrompe
                error_log('Erro ao enviar e-mail: ' . $e->getMessage());
            }
            return $this->jsonSuccess(null, 'Link de recuperação enviado para seu e-mail');
        }
        
        return $this->jsonError('Erro ao gerar link de recuperação', 500);
    }
    
    public function resetPassword($token = null)
    {
        $this->request->allowMethod(['post']);
        
        $user = $this->Users->find()
            ->where([
                'reset_token' => $token,
                'reset_token_expires >' => date('Y-m-d H:i:s')
            ])
            ->first();
        
        if (!$user) {
            return $this->jsonError('Token inválido ou expirado', 400);
        }
        
        $newPassword = $this->request->getData('senha');
        $user->senha_hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $user->reset_token = null;
        $user->reset_token_expires = null;
        
        if ($this->Users->save($user)) {
            return $this->jsonSuccess(null, 'Senha redefinida com sucesso');
        }
        
        return $this->jsonError('Erro ao redefinir senha', 500);
    }
    
    public function notFound()
    {
        $this->response = $this->response->withStatus(404);
        $this->response->getBody()->write(json_encode([
            'success' => false,
            'message' => 'Rota não encontrada'
        ]));
        return $this->response;
    }
}