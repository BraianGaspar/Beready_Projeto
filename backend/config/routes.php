<?php
declare(strict_types=1);

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

/** @var \Cake\Routing\RouteBuilder $routes */
$routes->setRouteClass(DashedRoute::class);

// Health
$routes->connect('/health', ['controller' => 'Users', 'action' => 'health']);

// Auth
$routes->connect('/auth/register', ['controller' => 'Users', 'action' => 'register']);
$routes->connect('/auth/login', ['controller' => 'Users', 'action' => 'login']);
$routes->connect('/auth/logout', ['controller' => 'Users', 'action' => 'logout']);
$routes->connect('/auth/forgot-password', ['controller' => 'Users', 'action' => 'forgotPassword']);
$routes->connect('/auth/reset-password/*', ['controller' => 'Users', 'action' => 'resetPassword']);

// Users
$routes->connect('/users/test', ['controller' => 'Users', 'action' => 'test']);
$routes->connect('/users/register', ['controller' => 'Users', 'action' => 'register']);
$routes->connect('/users/login', ['controller' => 'Users', 'action' => 'login']);

// Rotas parametrizadas usando passparams
$routes->connect('/users/view/{id}', ['controller' => 'Users', 'action' => 'view'])
    ->setPatterns(['id' => '\d+'])
    ->setMethods(['GET'])
    ->setPass(['id']);

$routes->connect('/users/update/{id}', ['controller' => 'Users', 'action' => 'update'])
    ->setPatterns(['id' => '\d+'])
    ->setMethods(['PUT', 'POST'])
    ->setPass(['id']);

$routes->connect('/users/delete/{id}', ['controller' => 'Users', 'action' => 'delete'])
    ->setPatterns(['id' => '\d+'])
    ->setMethods(['DELETE'])
    ->setPass(['id']);

// Rotas para Quizes
$routes->connect('/quizes', ['controller' => 'Quizes', 'action' => 'index'])->setMethods(['GET']);
$routes->connect('/quizes', ['controller' => 'Quizes', 'action' => 'add'])->setMethods(['POST']);
$routes->connect('/quizes/user/{usuarioId}', ['controller' => 'Quizes', 'action' => 'userQuizes'])
    ->setPatterns(['usuarioId' => '\d+'])
    ->setMethods(['GET'])
    ->setPass(['usuarioId']);
$routes->connect('/quizes/view/{id}', ['controller' => 'Quizes', 'action' => 'view'])
    ->setPatterns(['id' => '\d+'])
    ->setMethods(['GET'])
    ->setPass(['id']);
$routes->connect('/quizes/edit/{id}', ['controller' => 'Quizes', 'action' => 'edit'])
    ->setPatterns(['id' => '\d+'])
    ->setMethods(['PUT'])
    ->setPass(['id']);
$routes->connect('/quizes/delete/{id}', ['controller' => 'Quizes', 'action' => 'delete'])
    ->setPatterns(['id' => '\d+'])
    ->setMethods(['DELETE'])
    ->setPass(['id']);

// Rotas para Flashcards
$routes->connect('/flashcards', ['controller' => 'Flashcards', 'action' => 'index'])->setMethods(['GET']);
$routes->connect('/flashcards', ['controller' => 'Flashcards', 'action' => 'add'])->setMethods(['POST']);
$routes->connect('/flashcards/view/{id}', ['controller' => 'Flashcards', 'action' => 'view'])
    ->setPatterns(['id' => '\d+'])
    ->setMethods(['GET'])
    ->setPass(['id']);
$routes->connect('/flashcards/edit/{id}', ['controller' => 'Flashcards', 'action' => 'edit'])
    ->setPatterns(['id' => '\d+'])
    ->setMethods(['PUT'])
    ->setPass(['id']);
$routes->connect('/flashcards/delete/{id}', ['controller' => 'Flashcards', 'action' => 'delete'])
    ->setPatterns(['id' => '\d+'])
    ->setMethods(['DELETE'])
    ->setPass(['id']);

// Fallback
$routes->connect('/*', ['controller' => 'Users', 'action' => 'notFound']);
$routes->fallbacks();