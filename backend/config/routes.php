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
$routes->connect('/users/{id}', ['controller' => 'Users', 'action' => 'view'])
    ->setPatterns(['id' => '\d+'])
    ->setPass(['id']);

$routes->connect('/users/{id}', ['controller' => 'Users', 'action' => 'update'])
    ->setPatterns(['id' => '\d+'])
    ->setMethods(['PUT'])
    ->setPass(['id']);

$routes->connect('/users/{id}', ['controller' => 'Users', 'action' => 'delete'])
    ->setPatterns(['id' => '\d+'])
    ->setMethods(['DELETE'])
    ->setPass(['id']);

// Fallback
$routes->connect('/*', ['controller' => 'Users', 'action' => 'notFound']);
$routes->fallbacks();