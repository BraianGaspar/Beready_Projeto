<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;

class UsersController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
    }

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

    public function register()
    {
        $this->response = $this->response->withType('application/json');
        
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (!$data) {
            $data = $this->request->getData();
        }
        
        if (empty($data['nome']) || empty($data['email']) || empty($data['senha'])) {
            $this->response = $this->response->withStatus(400);
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Nome, e-mail e senha são obrigatórios'
            ]));
            return $this->response;
        }
        
        $existingUser = $this->Users->find()->where(['email' => $data['email']])->first();
        if ($existingUser) {
            $this->response = $this->response->withStatus(409);
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Este e-mail já está cadastrado'
            ]));
            return $this->response;
        }
        
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
            unset($user->senha_hash);
            $this->response->getBody()->write(json_encode([
                'success' => true,
                'data' => $user,
                'message' => 'Usuário cadastrado com sucesso!'
            ]));
            return $this->response;
        } else {
            $this->response = $this->response->withStatus(422);
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Erro ao cadastrar usuário',
                'errors' => $user->getErrors()
            ]));
            return $this->response;
        }
    }

    public function login()
    {
        $this->response = $this->response->withType('application/json');
        
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

        $user = $this->Users->find()
            ->where(['email' => $data['email']])
            ->first();
        
        $passwordHasher = new \Authentication\PasswordHasher\DefaultPasswordHasher();
        
        if ($user && $passwordHasher->check($data['password'], $user->senha_hash)) {
            $user->ultimo_login = new \DateTime();
            $this->Users->save($user);
            
            unset($user->senha_hash);
            unset($user->token);
            unset($user->token_expires);
            
            $this->response->getBody()->write(json_encode([
                'success' => true,
                'data' => $user,
                'message' => 'Login realizado com sucesso'
            ]));
            return $this->response;
        } else {
            $this->response = $this->response->withStatus(401);
            $this->response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'E-mail ou senha inválidos'
            ]));
            return $this->response;
        }
    }
}