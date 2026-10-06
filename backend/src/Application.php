<?php

declare(strict_types=1);

namespace App;

use App\Exceptions\SentryExceptionRenderer;
use App\Middleware\AdminMiddleware;
use App\Middleware\CorsMiddleware;
use App\Middleware\JwtAuthMiddleware;
use App\Middleware\RateLimitMiddleware;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\BaseApplication;
use Cake\Http\Middleware\BodyParserMiddleware;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;
use Cake\Routing\RouteBuilder;

class Application extends BaseApplication
{
    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        // CORS primeiro, para que respostas de erro também levem os headers
        $middlewareQueue->add(new CorsMiddleware());

        // Error Handler logo após o CORS, cobrindo todo o resto da fila
        $middlewareQueue->add(new ErrorHandlerMiddleware([
            'exceptionRenderer' => SentryExceptionRenderer::class,
        ]));

        $middlewareQueue->add(new BodyParserMiddleware());

        // Rate Limit: 300 req/min por IP no geral e 10 req/min por IP nas rotas de autenticação
        $middlewareQueue->add(new RateLimitMiddleware(300, 10, 60));

        // JWT (define user_id/role) e, em seguida, a restrição do escopo /admin
        $middlewareQueue->add(new JwtAuthMiddleware());
        $middlewareQueue->add(new AdminMiddleware());

        $middlewareQueue->add(new RoutingMiddleware($this));

        $middlewareQueue->add(new AssetMiddleware());

        return $middlewareQueue;
    }

    public function routes(RouteBuilder $routes): void
    {
        require CONFIG . 'routes.php';
    }
}
