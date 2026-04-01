<?php
declare(strict_types=1);

namespace App;

use Cake\Http\BaseApplication;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\Middleware\RoutingMiddleware;

class Application extends BaseApplication
{
    public function bootstrap(): void
    {
        parent::bootstrap();
    }

    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        return $middlewareQueue->add(new RoutingMiddleware($this));
    }

    /**
     
MÉTODO CUSTOM CONNECT - SUBSTITUI Router::connect()*/
  public static function customConnect($url, $params = []){// Simulação manual do roteamento
      if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] === $url) {$_GET['controller'] = $params['controller'] ?? 'Pages';$_GET['action'] = $params['action'] ?? 'index';

            if (isset($params['pass'])) {
                $_GET['pass'] = (array)$params['pass'];
            }
        }

        return true;
    }
}