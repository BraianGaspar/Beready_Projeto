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
            $response = (new Response())
                ->withStatus(200)
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
                ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-CSRF-Token, X-Requested-With, Accept, Cache-Control')
                ->withHeader('Access-Control-Max-Age', '86400');

            return $this->withCorsHeaders($request, $response);
        }

        return $this->withCorsHeaders($request, $handler->handle($request))
            ->withHeader('Access-Control-Expose-Headers', 'Authorization, X-CSRF-Token');
    }

    /**
     * Só devolve Allow-Origin quando a origem está na lista; origem desconhecida fica sem o header.
     */
    private function withCorsHeaders(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $response = $response->withAddedHeader('Vary', 'Origin');

        $origin = $request->getHeaderLine('Origin');
        if ($origin === '' || !in_array($origin, $this->allowedOrigins(), true)) {
            return $response;
        }

        return $response
            ->withHeader('Access-Control-Allow-Origin', $origin)
            ->withHeader('Access-Control-Allow-Credentials', 'true');
    }

    private function allowedOrigins(): array
    {
        $origins = (string)env('CORS_ALLOWED_ORIGINS');

        return array_values(array_filter(array_map('trim', explode(',', $origins))));
    }
}
