<?php

namespace App\Middleware;

use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;

class PermissionMiddleware
{
    public function __invoke(ServerRequest $request, Response $response, $next)
    {
        $required = $request->getParam('permission');
        
        if (!$required) {
            return $next($request, $response);
        }

        // Buscar usuário da sessão ou token
        $userId = $request->getAttribute('userId');
        
        if (!$userId) {
            return $response->withStatus(401)
                ->withJson(['error' => 'Usuário não autenticado']);
        }

        // Verificar permissão
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        
        if ($usersTable->hasPermission($userId, $required)) {
            return $next($request, $response);
        }

        return $response->withStatus(403)
            ->withJson(['error' => 'Permissão negada: ' . $required]);
    }
}