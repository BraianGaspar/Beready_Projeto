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
        $middlewareQueue
            ->add(new CorsMiddleware())
            ->add(new \Cake\Http\Middleware\BodyParserMiddleware())
            ->add(new \Cake\Routing\Middleware\RoutingMiddleware($this))
            ->add(new \Cake\Routing\Middleware\AssetMiddleware());

        return $middlewareQueue;
    }

    public function routes(RouteBuilder $routes): void
    {
        require CONFIG . 'routes.php';
    }
}