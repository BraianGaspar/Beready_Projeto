<?php
declare(strict_types=1);

namespace App;

use Cake\Http\BaseApplication;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\RouteBuilder;
use App\Middleware\CorsMiddleware;

class Application extends BaseApplication
{
    public function bootstrap(): void
    {
        parent::bootstrap();
    }

    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        // Adicionar CORS primeiro
        $middlewareQueue->add(new CorsMiddleware());

        return $middlewareQueue;
    }

    public function routes(RouteBuilder $routes): void
    {
        parent::routes($routes);
        error_log('Application::routes() - Carregando rotas de: ' . CONFIG . 'routes.php');

        // Carrega o arquivo de rotas
        require CONFIG . 'routes.php';
    }
}