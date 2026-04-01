<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\Utility\Security;
use Cake\Mailer\Mailer;
use Cake\Routing\Router;
use Cake\ORM\TableRegistry;

class UsersController extends AppController
{
    // NÃO TEM initialize() - isso evita loop! O AppController já cuida disso.
    
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        // As ações permitidas já estão configuradas no AppController
    }

    // ============================================
    // ENDPOINT DE TESTE DA API
    // ============================================
    
    public function test()
    {
        $this->response = $this->response->withType('application/json');
        $this->response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'API do Beready está funcionando!',
            'timestamp' => date('Y-m-d H:i:s')
        ]));
        return $this->response;
    }

    // ============================================
    // API: REGISTRO DE USUÁRIO
    // ============================================
    
    public function register()
    {
        $this->request->allowMethod(['post']);
        
        // Pega os dados do JSON
        $data = $this->request->getData();
        
        // Validações básicas
        if (empty($data['nome']) || empty($data['email']) || empty($data['senha'])) {
            $this->response = $this->response->withStatus(400);
            $this->response = $this->response->withType('application/json');
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Nome, e-mail e senha são obrigatórios'
            ]));
            return $this->response;
        }
        
        // Verifica se email já existe
        $existingUser = $this->Users->find()->where(['email' => $data['email']])->first();
        if ($existingUser) {
            $this->response = $this->response->withStatus(409);
            $this->response = $this->response->withType('application/json');
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Este e-mail já está cadastrado'
            ]));
            return $this->response;
        }
        
        // Prepara os dados
        $userData = [
            'nome' => $data['nome'],
            'email' => $data['email'],
            'senha_hash' => (new \Authentication\PasswordHasher\DefaultPasswordHasher())->hash($data['senha']),
            'telefone' => $data['telefone'] ?? null,
            'nivel_ingles' => $data['nivel_ingles'] ?? 'iniciante',
            'idioma_preferido' => $data['idioma_preferido'] ?? 'pt-BR',
            'objetivos_aprendizado' => $data['objetivos_aprendizado'] ?? null,
            'status' => 'ativo'
        ];
        
        $user = $this->Users->newEntity($userData);
        
        if ($this->Users->save($user)) {
            // Remove dados sensíveis
            unset($user->senha_hash);
            
            $this->response = $this->response->withType('application/json');
            $this->response->getBody()->write(json_encode([
                'success' => true,
                'data' => $user,
                'message' => 'Usuário cadastrado com sucesso!'
            ]));
            return $this->response;
        } else {
            $this->response = $this->response->withStatus(422);
            $this->response = $this->response->withType('application/json');
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Erro ao cadastrar usuário',
                'errors' => $user->getErrors()
            ]));
            return $this->response;
        }
    }

    // ============================================
    // API: LOGIN
    // ============================================
    
    public function login()
    {
        $this->request->allowMethod(['post']);

        $data = $this->request->getData();
        
        // Verifica se email e senha foram enviados
        if (empty($data['email']) || empty($data['password'])) {
            $this->response = $this->response->withStatus(400);
            $this->response = $this->response->withType('application/json');
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'E-mail e senha são obrigatórios'
            ]));
            return $this->response;
        }

        $user = $this->Auth->identify();
        
        if ($user) {
            $this->Auth->setUser($user);

            // Atualiza último login
            $userEntity = $this->Users->get($user['id']);
            $userEntity->ultimo_login = new \DateTime();
            $this->Users->save($userEntity);

            // Remove dados sensíveis
            unset($user['senha_hash']);
            unset($user['token']);
            unset($user['token_expires']);

            $this->response = $this->response->withType('application/json');
            $this->response->getBody()->write(json_encode([
                'success' => true,
                'data' => $user,
                'message' => 'Login realizado com sucesso'
            ]));
            return $this->response;
        } else {
            $this->response = $this->response->withStatus(401);
            $this->response = $this->response->withType('application/json');
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'E-mail ou senha inválidos'
            ]));
            return $this->response;
        }
    }

    // ============================================
    // API: LOGOUT
    // ============================================
    
    public function logout()
    {
        $this->Auth->logout();
        
        $this->response = $this->response->withType('application/json');
        $this->response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Você saiu com sucesso'
        ]));
        return $this->response;
    }

    // ============================================
    // API: VER PERFIL DO USUÁRIO LOGADO
    // ============================================
    
    public function profile()
    {
        $userId = $this->Auth->user('id');
        
        if (!$userId) {
            $this->response = $this->response->withStatus(401);
            $this->response = $this->response->withType('application/json');
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Usuário não autenticado'
            ]));
            return $this->response;
        }
        
        return $this->view($userId);
    }

    // ============================================
    // API: VER USUÁRIO POR ID
    // ============================================
    
    public function view($id = null)
    {
        $userId = $id ?? $this->Auth->user('id');
        
        if (!$userId) {
            $this->response = $this->response->withStatus(400);
            $this->response = $this->response->withType('application/json');
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'ID do usuário não informado'
            ]));
            return $this->response;
        }
        
        try {
            $user = $this->Users->get($userId);
            
            // Remove dados sensíveis
            unset($user->senha_hash);
            unset($user->token);
            unset($user->token_expires);
            
            $this->response = $this->response->withType('application/json');
            $this->response->getBody()->write(json_encode([
                'success' => true,
                'data' => $user
            ]));
            return $this->response;
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->response = $this->response->withStatus(404);
            $this->response = $this->response->withType('application/json');
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Usuário não encontrado'
            ]));
            return $this->response;
        }
    }

    // ============================================
    // API: ATUALIZAR PERFIL
    // ============================================
    
    public function edit($id = null)
    {
        $this->request->allowMethod(['put', 'patch']);
        
        $userId = $id ?? $this->Auth->user('id');
        $currentUserId = $this->Auth->user('id');
        
        // Verifica permissão
        if ($userId != $currentUserId) {
            $this->response = $this->response->withStatus(403);
            $this->response = $this->response->withType('application/json');
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Você não tem permissão para editar este usuário'
            ]));
            return $this->response;
        }
        
        try {
            $user = $this->Users->get($userId);
            $data = $this->request->getData();
            
            // Se a senha estiver vazia, remove para não atualizar
            if (empty($data['senha'])) {
                unset($data['senha']);
            } else {
                $data['senha_hash'] = (new \Authentication\PasswordHasher\DefaultPasswordHasher())->hash($data['senha']);
                unset($data['senha']);
            }
            
            $user = $this->Users->patchEntity($user, $data);
            
            if ($this->Users->save($user)) {
                unset($user->senha_hash);
                
                $this->response = $this->response->withType('application/json');
                $this->response->getBody()->write(json_encode([
                    'success' => true,
                    'data' => $user,
                    'message' => 'Perfil atualizado com sucesso'
                ]));
                return $this->response;
            } else {
                $this->response = $this->response->withStatus(422);
                $this->response = $this->response->withType('application/json');
                $this->response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Erro ao atualizar perfil',
                    'errors' => $user->getErrors()
                ]));
                return $this->response;
            }
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->response = $this->response->withStatus(404);
            $this->response = $this->response->withType('application/json');
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Usuário não encontrado'
            ]));
            return $this->response;
        }
    }

    // ============================================
    // API: DELETAR USUÁRIO
    // ============================================
    
    public function delete($id = null)
    {
        $this->request->allowMethod(['delete']);
        
        $userId = $id ?? $this->Auth->user('id');
        $currentUserId = $this->Auth->user('id');
        
        if (!$userId) {
            $this->response = $this->response->withStatus(400);
            $this->response = $this->response->withType('application/json');
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'ID do usuário não especificado'
            ]));
            return $this->response;
        }
        
        // Impede que o usuário exclua a si mesmo
        if ($userId == $currentUserId) {
            $this->response = $this->response->withStatus(403);
            $this->response = $this->response->withType('application/json');
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Você não pode excluir sua própria conta'
            ]));
            return $this->response;
        }
        
        try {
            $user = $this->Users->get($userId);
            
            if ($this->Users->delete($user)) {
                $this->response = $this->response->withType('application/json');
                $this->response->getBody()->write(json_encode([
                    'success' => true,
                    'message' => 'Usuário excluído com sucesso'
                ]));
                return $this->response;
            } else {
                $this->response = $this->response->withStatus(500);
                $this->response = $this->response->withType('application/json');
                $this->response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Erro ao excluir usuário'
                ]));
                return $this->response;
            }
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->response = $this->response->withStatus(404);
            $this->response = $this->response->withType('application/json');
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Usuário não encontrado'
            ]));
            return $this->response;
        }
    }

    // ============================================
    // API: ESQUECI MINHA SENHA
    // ============================================
    
    public function forgotPassword()
    {
        $this->request->allowMethod(['post']);
        
        $email = $this->request->getData('email');
        
        if (empty($email)) {
            $this->response = $this->response->withStatus(400);
            $this->response = $this->response->withType('application/json');
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'E-mail é obrigatório'
            ]));
            return $this->response;
        }
        
        $user = $this->Users->find()->where(['email' => $email])->first();
        
        if ($user) {
            $token = Security::hash(Security::randomBytes(32));
            $user->token = $token;
            $user->token_expires = new \DateTime('+1 hour');
            
            if ($this->Users->save($user)) {
                $resetLink = Router::url(['controller' => 'Users', 'action' => 'resetPassword', $token], true);
                
                $this->response = $this->response->withType('application/json');
                $this->response->getBody()->write(json_encode([
                    'success' => true,
                    'message' => 'Link de recuperação enviado para o e-mail',
                    'reset_link' => $resetLink
                ]));
                return $this->response;
            }
        }
        
        // Por segurança, sempre retorna sucesso mesmo se o e-mail não existir
        $this->response = $this->response->withType('application/json');
        $this->response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Se o e-mail estiver cadastrado, você receberá um link de recuperação'
        ]));
        return $this->response;
    }

    // ============================================
    // API: REDEFINIR SENHA
    // ============================================
    
    public function resetPassword($token = null)
    {
        if (!$token) {
            $this->response = $this->response->withStatus(400);
            $this->response = $this->response->withType('application/json');
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Token de redefinição de senha inválido'
            ]));
            return $this->response;
        }
        
        if ($this->request->is(['post', 'put'])) {
            $user = $this->Users->find()->where([
                'token' => $token,
                'token_expires >' => new \DateTime(),
            ])->first();
            
            if (!$user) {
                $this->response = $this->response->withStatus(400);
                $this->response = $this->response->withType('application/json');
                $this->response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Token expirado ou inválido'
                ]));
                return $this->response;
            }
            
            $data = $this->request->getData();
            
            if (empty($data['senha'])) {
                $this->response = $this->response->withStatus(400);
                $this->response = $this->response->withType('application/json');
                $this->response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Nova senha é obrigatória'
                ]));
                return $this->response;
            }
            
            $user->senha_hash = (new \Authentication\PasswordHasher\DefaultPasswordHasher())->hash($data['senha']);
            $user->token = null;
            $user->token_expires = null;
            
            if ($this->Users->save($user)) {
                $this->response = $this->response->withType('application/json');
                $this->response->getBody()->write(json_encode([
                    'success' => true,
                    'message' => 'Senha redefinida com sucesso'
                ]));
                return $this->response;
            } else {
                $this->response = $this->response->withStatus(500);
                $this->response = $this->response->withType('application/json');
                $this->response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Erro ao redefinir senha'
                ]));
                return $this->response;
            }
        } else {
            // GET - valida o token
            $user = $this->Users->find()->where([
                'token' => $token,
                'token_expires >' => new \DateTime(),
            ])->first();
            
            if ($user) {
                $this->response = $this->response->withType('application/json');
                $this->response->getBody()->write(json_encode([
                    'success' => true,
                    'message' => 'Token válido',
                    'email' => $user->email
                ]));
                return $this->response;
            } else {
                $this->response = $this->response->withStatus(400);
                $this->response = $this->response->withType('application/json');
                $this->response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Token inválido ou expirado'
                ]));
                return $this->response;
            }
        }
    }
}