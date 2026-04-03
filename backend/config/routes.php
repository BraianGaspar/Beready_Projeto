<?php
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

return function (RouteBuilder $routes): void {
    $routes->setRouteClass(DashedRoute::class);

    $routes->scope('/', function (RouteBuilder $builder): void {
        $builder->setExtensions(['json']);
        
        // Rota de teste
        $builder->connect('/ping', ['controller' => 'Ping', 'action' => 'index']);
        $builder->connect('/health', ['controller' => 'Users', 'action' => 'test']);
        
        $builder->fallbacks();
    });
};