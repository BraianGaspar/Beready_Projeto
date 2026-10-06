<?php

declare(strict_types=1);

namespace App\Middleware;

use Cake\Http\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Resposta de erro no mesmo formato dos controllers: {success:false, message}.
 */
trait JsonErrorResponseTrait
{
    private function jsonErrorResponse(int $status, string $message, array $extra = []): ResponseInterface
    {
        return (new Response())
            ->withStatus($status)
            ->withType('application/json')
            ->withStringBody(json_encode(['success' => false, 'message' => $message] + $extra));
    }
}
