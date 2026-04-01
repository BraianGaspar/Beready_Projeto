<?php
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

return function (RouteBuilder $routes): void {
    $routes->setRouteClass(DashedRoute::class);

    $routes->scope('/', function (RouteBuilder $builder): void {
        $builder->connect('/', ['controller' => 'Users', 'action' => 'login', 'index']);
        $builder->connect('/pages/*', 'Pages::display');

        // Rotas de Users
        $builder->connect('/login', ['controller' => 'Users', 'action' => 'login']);
        $builder->connect('/    ', ['controller' => 'Users', 'action' => 'logout']);
        $builder->connect('/register', ['controller' => 'Users', 'action' => 'add']);
        $builder->connect('/forgot-password', ['controller' => 'Users', 'action' => 'forgotPassword']);
        $builder->connect('/reset-password/*', ['controller' => 'Users', 'action' => 'resetPassword']);
        $builder->connect('/profile', ['controller' => 'Users', 'action' => 'edit']);
        $builder->connect('/users', ['controller' => 'Users', 'action' => 'index']);
        $builder->connect('/users/view/*', ['controller' => 'Users', 'action' => 'view']);
        $builder->connect('/users/edit/*', ['controller' => 'Users', 'action' => 'edit']);
        $builder->connect('/users/delete/*', ['controller' => 'Users', 'action' => 'delete']);

        // Rotas de Tags
        $builder->connect('/tags', ['controller' => 'Tags', 'action' => 'index']);
        $builder->connect('/tags/add', ['controller' => 'Tags', 'action' => 'add']);
        $builder->connect('/tags/view/*', ['controller' => 'Tags', 'action' => 'view']);
        $builder->connect('/tags/edit/*', ['controller' => 'Tags', 'action' => 'edit']);
        $builder->connect('/tags/delete/*', ['controller' => 'Tags', 'action' => 'delete']);

        // Rotas de Flashcards
        $builder->connect('/flashcards', ['controller' => 'Flashcards', 'action' => 'listar']);
        $builder->connect('/flashcards/criar', ['controller' => 'Flashcards', 'action' => 'criar']);
        $builder->connect('/flashcards/ver/*', ['controller' => 'Flashcards', 'action' => 'ver']);
        $builder->connect('/flashcards/editar/*', ['controller' => 'Flashcards', 'action' => 'editar']);
        $builder->connect('/flashcards/excluir/*', ['controller' => 'Flashcards', 'action' => 'excluir']);

        $builder->fallbacks();
    });

    // Rotas da API (se necessário no futuro)
    /*
    $routes->prefix('Api', function (RouteBuilder $builder) {
        $builder->setExtensions(['json']);
        $builder->connect('/users', ['controller' => 'Users', 'action' => 'index']);
        $builder->connect('/tags', ['controller' => 'Tags', 'action' => 'index']);
        $builder->connect('/flashcards', ['controller' => 'Flashcards', 'action' => 'index']);
        $builder->fallbacks(DashedRoute::class);
    });
    */

    // Rotas de Admin (se necessário no futuro)
    /*
    $routes->prefix('Admin', function (RouteBuilder $builder) {
        $builder->connect('/', ['controller' => 'Users', 'action' => 'index']);
        $builder->fallbacks(DashedRoute::class);
    });
    */
};