<?php
declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Cake\Http\Response;

class CorsMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getMethod() === 'OPTIONS') {
            return new Response([
                'status' => 200,
                'headers' => [
                    'Access-Control-Allow-Origin' => 'http://localhost:5173',
                    'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
                    'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-CSRF-Token, X-Requested-With',
                    'Access-Control-Allow-Credentials' => 'true',
                    'Access-Control-Max-Age' => '86400'
                ]
            ]);
        }
        
        return $handler->handle($request);
    }
}