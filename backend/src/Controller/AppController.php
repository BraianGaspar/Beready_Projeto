<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\EventInterface;

class AppController extends Controller
{
    public function initialize(): void
    {
        parent::initialize();

        // REMOVA o RequestHandler - vamos usar um método mais simples
        
        $this->loadComponent('Flash');
        
        // Configuração do Auth
        $this->loadComponent('Auth', [
            'authenticate' => [
                'Form' => [
                    'fields' => [
                        'username' => 'email',
                        'password' => 'password'
                    ],
                    'userModel' => 'Users'
                ]
            ],
            'loginAction' => [
                'controller' => 'Users',
                'action' => 'login'
            ],
            'loginRedirect' => [
                'controller' => 'Users',
                'action' => 'index'
            ],
            'logoutRedirect' => [
                'controller' => 'Users',
                'action' => 'login'
            ],
            'authError' => 'Você não está autorizado a acessar essa página.',
            'storage' => 'Session'
        ]);
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        
        // Permite acesso público a essas ações
        $this->Auth->allow([
            'login',
            'register',
            'test',
            'forgotPassword',
            'resetPassword'
        ]);
    }
}