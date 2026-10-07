<?php

declare(strict_types=1);

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

/** @var \Cake\Routing\RouteBuilder $routes */
$routes->setRouteClass(DashedRoute::class);

/**
 * HEALTHCHECK
 */
$routes->connect('/', [
    'controller' => 'Users',
    'action' => 'health'
])->setMethods(['GET']);

// ADMIN ROUTES
$routes->connect('/admin/users', [
    'controller' => 'Admin',
    'action' => 'users'
])->setMethods(['GET']);

$routes->connect('/admin/users/role', [
    'controller' => 'Admin',
    'action' => 'updateRole'
])->setMethods(['POST']);

$routes->connect('/admin/stats', [
    'controller' => 'Admin',
    'action' => 'stats'
])->setMethods(['GET']);


/**
 * ADMIN - ROLES
*/
$routes->connect('/admin/roles', [
    'controller' => 'AdminRoles',
    'action' => 'index'
])->setMethods(['GET']);

$routes->connect('/admin/roles', [
    'controller' => 'AdminRoles',
    'action' => 'add'
])->setMethods(['POST']);

$routes->connect('/admin/roles/{id}', [
    'controller' => 'AdminRoles',
    'action' => 'edit'
])->setPatterns(['id' => '\d+'])
->setPass(['id'])
->setMethods(['PUT']);

$routes->connect('/admin/roles/{id}', [
    'controller' => 'AdminRoles',
    'action' => 'delete'
])->setPatterns(['id' => '\d+'])
->setPass(['id'])
->setMethods(['DELETE']);

/**
 * ROTAS: ADMIN - PERMISSÕES
 */
$routes->connect('/admin/permissions', [
    'controller' => 'AdminPermissions',
    'action' => 'index'
])->setMethods(['GET']);

/**
 * ROTAS: ADMIN - PLANOS
 */
$routes->connect('/admin/planos', [
    'controller' => 'AdminPlanos',
    'action' => 'index'
])->setMethods(['GET']);

$routes->connect('/admin/planos', [
    'controller' => 'AdminPlanos',
    'action' => 'add'
])->setMethods(['POST']);

$routes->connect('/admin/planos/edit/{id}', [
    'controller' => 'AdminPlanos',
    'action' => 'edit'
])->setPatterns(['id' => '\d+'])
->setPass(['id'])
->setMethods(['PUT', 'POST']);

$routes->connect('/admin/planos/delete/{id}', [
    'controller' => 'AdminPlanos',
    'action' => 'delete'
])->setPatterns(['id' => '\d+'])
->setPass(['id'])
->setMethods(['DELETE', 'POST']);

/**
 * ROTA: PLANOS PÚBLICOS
 */
$routes->connect('/planos', [
    'controller' => 'Planos',
    'action' => 'index'
])->setMethods(['GET']);

/**
 * ROTA: USUÁRIO - PERMISSÕES
 */
$routes->connect('/user/permissions', [
    'controller' => 'Users',
    'action' => 'permissions'
])->setMethods(['GET']);

/**
 * ROTA: USUÁRIO - ASSINATURA
 */
$routes->connect('/user/assinatura', [
    'controller' => 'Assinaturas',
    'action' => 'current'
])->setMethods(['GET']);

/**
 * ROTA: PLANOS - ASSINAR
 */
$routes->connect('/planos/{id}/assinar', [
    'controller' => 'Planos',
    'action' => 'assinar'
])->setPatterns(['id' => '\d+'])
->setPass(['id'])
->setMethods(['POST']);

/**
 * ROTA: PLANOS - CANCELAR
 */
$routes->connect('/planos/cancelar', [
    'controller' => 'Planos',
    'action' => 'cancelar'
])->setMethods(['POST']);

/**
 * ROTA: PLANOS - PORTAL DO CLIENTE (Stripe Billing Portal)
 */
$routes->connect('/planos/portal', [
    'controller' => 'Planos',
    'action' => 'portal'
])->setMethods(['POST']);

/**
 * STRIPE ROUTES
 */
$routes->connect('/payments/webhook', [
    'controller' => 'Payments',
    'action' => 'webhook'
])->setMethods(['POST']);

/**
 * AUTH ROUTES
 */
$routes->connect('/auth/register', [
    'controller' => 'Users',
    'action' => 'register'
])->setMethods(['POST']);

$routes->connect('/auth/login', [
    'controller' => 'Users',
    'action' => 'login'
])->setMethods(['POST']);

/**
 * SOCIAL AUTH
 * login/{provider} redireciona ao provedor; o callback gera um código de uso único
 * que o frontend troca por tokens em /auth/social/exchange.
 */
$routes->connect('/auth/login/{provider}', [
    'controller' => 'SocialAuth',
    'action' => 'login'
])->setPatterns([
    'provider' => 'google|facebook|linkedin'
])->setPass(['provider'])
->setMethods(['GET']);

$routes->connect('/social-auth/callback/{provider}', [
    'controller' => 'SocialAuth',
    'action' => 'callback'
])->setPatterns([
    'provider' => 'google|facebook|linkedin'
])->setPass(['provider'])
->setMethods(['GET']);

$routes->connect('/auth/social/exchange', [
    'controller' => 'Users',
    'action' => 'socialExchange'
])->setMethods(['POST']);

$routes->connect('/auth/logout', [
    'controller' => 'Users',
    'action' => 'logout'
])->setMethods(['POST']);

$routes->connect('/auth/forgot-password', [
    'controller' => 'Users',
    'action' => 'forgotPassword'
])->setMethods(['POST']);

$routes->connect('/auth/reset-password/{token}', [
    'controller' => 'Users',
    'action' => 'resetPassword'
])->setPatterns([
    'token' => '[a-f0-9]{64}'
])->setPass(['token'])
->setMethods(['POST']);

$routes->connect('/auth/refresh', [
    'controller' => 'Users',
    'action' => 'refresh'
])->setMethods(['POST']);

// Nova rota para obter usuário do token
$routes->connect('/users/me', [
    'controller' => 'Users',
    'action' => 'me'
])->setMethods(['GET']);

/**
 * UPLOAD
 */
$routes->connect('/upload/profile-photo', [
    'controller' => 'Upload',
    'action' => 'profilePhoto'
])->setMethods(['POST']);

/**
 * USERS (UUID FIRST + ID FALLBACK)
 */
$routes->connect('/users/{uuid}', [
    'controller' => 'Users',
    'action' => 'viewByUuid'
])->setPatterns([
    'uuid' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}'
])->setMethods(['GET']);

// fallback ID

$routes->connect('/users/view/{id}', [
    'controller' => 'Users',
    'action' => 'view'
])->setPatterns(['id' => '\d+'])->setMethods(['GET']);

$routes->connect('/users/{id}', [
    'controller' => 'Users',
    'action' => 'view'
])->setPatterns(['id' => '\d+'])->setMethods(['GET']);

$routes->connect('/users/update/{id}', [
    'controller' => 'Users',
    'action' => 'update'
])->setPatterns(['id' => '\d+'])->setMethods(['PUT', 'POST']);

$routes->connect('/users/delete/{id}', [
    'controller' => 'Users',
    'action' => 'delete'
])->setPatterns(['id' => '\d+'])->setMethods(['DELETE']);

/**
 * FLASHCARDS (REST CLEAN) - COMPLETO
 */
$routes->connect('/flashcards', [
    'controller' => 'Flashcards',
    'action' => 'index'
])->setMethods(['GET']);

$routes->connect('/flashcards', [
    'controller' => 'Flashcards',
    'action' => 'add'
])->setMethods(['POST']);

// UUID view
$routes->connect('/flashcards/{uuid}', [
    'controller' => 'Flashcards',
    'action' => 'viewByUuid'
])->setPatterns([
    'uuid' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}'
])->setMethods(['GET']);

// fallback ID view
$routes->connect('/flashcards/view/{id}', [
    'controller' => 'Flashcards',
    'action' => 'view'
])->setPatterns(['id' => '\d+'])->setMethods(['GET']);

$routes->connect('/flashcards/{id}', [
    'controller' => 'Flashcards',
    'action' => 'view'
])->setPatterns(['id' => '\d+'])->setMethods(['GET']);

// PUT edit - suporta /flashcards/{id} e /flashcards/edit/{id}
$routes->connect('/flashcards/{id}', [
    'controller' => 'Flashcards',
    'action' => 'edit'
])->setPatterns(['id' => '\d+'])->setMethods(['PUT']);

$routes->connect('/flashcards/edit/{id}', [
    'controller' => 'Flashcards',
    'action' => 'edit'
])->setPatterns(['id' => '\d+'])->setMethods(['PUT']);

// DELETE - suporta /flashcards/{id} e /flashcards/delete/{id}
$routes->connect('/flashcards/{id}', [
    'controller' => 'Flashcards',
    'action' => 'delete'
])->setPatterns(['id' => '\d+'])->setMethods(['DELETE']);

$routes->connect('/flashcards/delete/{id}', [
    'controller' => 'Flashcards',
    'action' => 'delete'
])->setPatterns(['id' => '\d+'])->setMethods(['DELETE']);

// REPETIÇÃO ESPAÇADA: devidos para revisar e registro da avaliação
$routes->connect('/flashcards/revisao', [
    'controller' => 'Flashcards',
    'action' => 'devidos'
])->setMethods(['GET']);

$routes->connect('/flashcards/{id}/revisao', [
    'controller' => 'Flashcards',
    'action' => 'revisao'
])->setPatterns(['id' => '\d+'])->setPass(['id'])->setMethods(['POST']);

/**
 * QUIZES (REST) - COMPLETO
 */
$routes->connect('/quizes', [
    'controller' => 'Quizes',
    'action' => 'index'
])->setMethods(['GET']);

$routes->connect('/quizes', [
    'controller' => 'Quizes',
    'action' => 'add'
])->setMethods(['POST']);

$routes->connect('/quizes/view/{id}', [
    'controller' => 'Quizes',
    'action' => 'view'
])->setPatterns(['id' => '\d+'])->setMethods(['GET']);

$routes->connect('/quizes/{id}', [
    'controller' => 'Quizes',
    'action' => 'view'
])->setPatterns(['id' => '\d+'])->setMethods(['GET']);

// PUT edit
$routes->connect('/quizes/{id}', [
    'controller' => 'Quizes',
    'action' => 'edit'
])->setPatterns(['id' => '\d+'])->setMethods(['PUT']);

$routes->connect('/quizes/edit/{id}', [
    'controller' => 'Quizes',
    'action' => 'edit'
])->setPatterns(['id' => '\d+'])->setMethods(['PUT']);

// DELETE
$routes->connect('/quizes/{id}', [
    'controller' => 'Quizes',
    'action' => 'delete'
])->setPatterns(['id' => '\d+'])->setMethods(['DELETE']);

$routes->connect('/quizes/delete/{id}', [
    'controller' => 'Quizes',
    'action' => 'delete'
])->setPatterns(['id' => '\d+'])->setMethods(['DELETE']);

// Gerar quiz a partir dos flashcards do usuário (cria um quiz: sujeito ao limite do plano)
$routes->connect('/quizes/gerar', [
    'controller' => 'Quizes',
    'action' => 'gerar'
])->setMethods(['POST']);

// QUESTÕES DO QUIZ (dono)
$routes->connect('/quizes/{id}/questoes', [
    'controller' => 'Quizes',
    'action' => 'questoes'
])->setPatterns(['id' => '\d+'])->setPass(['id'])->setMethods(['GET']);

$routes->connect('/quizes/{id}/questoes', [
    'controller' => 'Quizes',
    'action' => 'addQuestao'
])->setPatterns(['id' => '\d+'])->setPass(['id'])->setMethods(['POST']);

$routes->connect('/quizes/{id}/questoes', [
    'controller' => 'Quizes',
    'action' => 'substituirQuestoes'
])->setPatterns(['id' => '\d+'])->setPass(['id'])->setMethods(['PUT']);

$routes->connect('/quizes/{id}/questoes/{questaoId}', [
    'controller' => 'Quizes',
    'action' => 'editQuestao'
])->setPatterns(['id' => '\d+', 'questaoId' => '\d+'])->setPass(['id', 'questaoId'])->setMethods(['PUT']);

$routes->connect('/quizes/{id}/questoes/{questaoId}', [
    'controller' => 'Quizes',
    'action' => 'deleteQuestao'
])->setPatterns(['id' => '\d+', 'questaoId' => '\d+'])->setPass(['id', 'questaoId'])->setMethods(['DELETE']);

// JOGAR (dono ou quiz público): correção sempre no servidor
$routes->connect('/quizes/{id}/questoes/{questaoId}/verificar', [
    'controller' => 'Quizes',
    'action' => 'verificar'
])->setPatterns(['id' => '\d+', 'questaoId' => '\d+'])->setPass(['id', 'questaoId'])->setMethods(['POST']);

$routes->connect('/quizes/{id}/finalizar', [
    'controller' => 'Quizes',
    'action' => 'finalizar'
])->setPatterns(['id' => '\d+'])->setPass(['id'])->setMethods(['POST']);

/**
 * TAGS (REST) - COMPLETO
 */
$routes->connect('/tags', [
    'controller' => 'Tags',
    'action' => 'index'
])->setMethods(['GET']);

$routes->connect('/tags', [
    'controller' => 'Tags',
    'action' => 'add'
])->setMethods(['POST']);

$routes->connect('/tags/view/{id}', [
    'controller' => 'Tags',
    'action' => 'view'
])->setPatterns(['id' => '\d+'])->setMethods(['GET']);

$routes->connect('/tags/{id}', [
    'controller' => 'Tags',
    'action' => 'view'
])->setPatterns(['id' => '\d+'])->setMethods(['GET']);

// PUT edit
$routes->connect('/tags/{id}', [
    'controller' => 'Tags',
    'action' => 'edit'
])->setPatterns(['id' => '\d+'])->setMethods(['PUT']);

$routes->connect('/tags/edit/{id}', [
    'controller' => 'Tags',
    'action' => 'edit'
])->setPatterns(['id' => '\d+'])->setMethods(['PUT']);

// DELETE
$routes->connect('/tags/{id}', [
    'controller' => 'Tags',
    'action' => 'delete'
])->setPatterns(['id' => '\d+'])->setMethods(['DELETE']);

$routes->connect('/tags/delete/{id}', [
    'controller' => 'Tags',
    'action' => 'delete'
])->setPatterns(['id' => '\d+'])->setMethods(['DELETE']);

$routes->connect('/tags/usuario/{usuarioId}', [
    'controller' => 'Tags',
    'action' => 'getByUsuario'
])->setPatterns(['usuarioId' => '\d+'])->setMethods(['GET']);

/**
 * PREFERÊNCIAS
 */
$routes->connect('/preferencias/usuario/{usuarioId}', [
    'controller' => 'Preferencias',
    'action' => 'getByUsuario'
])->setPatterns(['usuarioId' => '\d+'])->setMethods(['GET']);

$routes->connect('/preferencias', [
    'controller' => 'Preferencias',
    'action' => 'save'
])->setMethods(['POST']);

/**
 * PROGRESSO
 */
$routes->connect('/progresso/usuario/{usuarioId}', [
    'controller' => 'Progresso',
    'action' => 'getByUsuario'
])->setPatterns(['usuarioId' => '\d+'])->setMethods(['GET']);

$routes->connect('/progresso', [
    'controller' => 'Progresso',
    'action' => 'save'
])->setMethods(['POST']);


/**
 * RESPOSTAS
 */
$routes->connect('/respostas', [
    'controller' => 'Respostas',
    'action' => 'save'
])->setMethods(['POST']);

/**
 * PROMPTS - COMPLETO
 */
$routes->connect('/prompts', [
    'controller' => 'Prompts',
    'action' => 'index'
])->setMethods(['GET']);

$routes->connect('/prompts', [
    'controller' => 'Prompts',
    'action' => 'add'
])->setMethods(['POST']);

$routes->connect('/prompts/view/{id}', [
    'controller' => 'Prompts',
    'action' => 'view'
])->setPatterns(['id' => '\d+'])->setMethods(['GET']);

$routes->connect('/prompts/{id}', [
    'controller' => 'Prompts',
    'action' => 'view'
])->setPatterns(['id' => '\d+'])->setMethods(['GET']);

// PUT edit
$routes->connect('/prompts/{id}', [
    'controller' => 'Prompts',
    'action' => 'edit'
])->setPatterns(['id' => '\d+'])->setMethods(['PUT']);

$routes->connect('/prompts/edit/{id}', [
    'controller' => 'Prompts',
    'action' => 'edit'
])->setPatterns(['id' => '\d+'])->setMethods(['PUT']);

// DELETE
$routes->connect('/prompts/{id}', [
    'controller' => 'Prompts',
    'action' => 'delete'
])->setPatterns(['id' => '\d+'])->setMethods(['DELETE']);

$routes->connect('/prompts/delete/{id}', [
    'controller' => 'Prompts',
    'action' => 'delete'
])->setPatterns(['id' => '\d+'])->setMethods(['DELETE']);

$routes->connect('/prompts/usuario/{usuarioId}', [
    'controller' => 'Prompts',
    'action' => 'getByUsuario'
])->setPatterns(['usuarioId' => '\d+'])->setMethods(['GET']);

/**
 * TRADUÇÕES
 */
$routes->connect('/traducoes/view/{id}', [
    'controller' => 'Traducoes',
    'action' => 'view'
])->setPatterns(['id' => '\d+'])->setMethods(['GET']);

$routes->connect('/traducoes/{id}', [
    'controller' => 'Traducoes',
    'action' => 'view'
])->setPatterns(['id' => '\d+'])->setMethods(['GET']);

$routes->connect('/traducoes', [
    'controller' => 'Traducoes',
    'action' => 'add'
])->setMethods(['POST']);

$routes->connect('/traducoes/edit/{id}', [
    'controller' => 'Traducoes',
    'action' => 'edit'
])->setPatterns(['id' => '\d+'])->setMethods(['PUT']);

$routes->connect('/traducoes/{id}', [
    'controller' => 'Traducoes',
    'action' => 'edit'
])->setPatterns(['id' => '\d+'])->setMethods(['PUT']);

$routes->connect('/traducoes/delete/{id}', [
    'controller' => 'Traducoes',
    'action' => 'delete'
])->setPatterns(['id' => '\d+'])->setMethods(['DELETE']);

$routes->connect('/traducoes/{id}', [
    'controller' => 'Traducoes',
    'action' => 'delete'
])->setPatterns(['id' => '\d+'])->setMethods(['DELETE']);

$routes->connect('/traducoes/prompt/{promptId}', [
    'controller' => 'Traducoes',
    'action' => 'getByPrompt'
])->setPatterns(['promptId' => '\d+'])->setMethods(['GET']);

/**
 * IMAGENS GERADAS
 */
$routes->connect('/imagens/view/{id}', [
    'controller' => 'ImagensGeradas',
    'action' => 'view'
])->setPatterns(['id' => '\d+'])->setMethods(['GET']);

$routes->connect('/imagens/{id}', [
    'controller' => 'ImagensGeradas',
    'action' => 'view'
])->setPatterns(['id' => '\d+'])->setMethods(['GET']);

$routes->connect('/imagens', [
    'controller' => 'ImagensGeradas',
    'action' => 'add'
])->setMethods(['POST']);

$routes->connect('/imagens/edit/{id}', [
    'controller' => 'ImagensGeradas',
    'action' => 'edit'
])->setPatterns(['id' => '\d+'])->setMethods(['PUT']);

$routes->connect('/imagens/{id}', [
    'controller' => 'ImagensGeradas',
    'action' => 'edit'
])->setPatterns(['id' => '\d+'])->setMethods(['PUT']);

$routes->connect('/imagens/delete/{id}', [
    'controller' => 'ImagensGeradas',
    'action' => 'delete'
])->setPatterns(['id' => '\d+'])->setMethods(['DELETE']);

$routes->connect('/imagens/{id}', [
    'controller' => 'ImagensGeradas',
    'action' => 'delete'
])->setPatterns(['id' => '\d+'])->setMethods(['DELETE']);

$routes->connect('/imagens/prompt/{promptId}', [
    'controller' => 'ImagensGeradas',
    'action' => 'getByPrompt'
])->setPatterns(['promptId' => '\d+'])->setMethods(['GET']);

/**
 * FRASES SEMELHANTES
 */
$routes->connect('/frases/view/{id}', [
    'controller' => 'FrasesSemelhantes',
    'action' => 'view'
])->setPatterns(['id' => '\d+'])->setMethods(['GET']);

$routes->connect('/frases/{id}', [
    'controller' => 'FrasesSemelhantes',
    'action' => 'view'
])->setPatterns(['id' => '\d+'])->setMethods(['GET']);

$routes->connect('/frases', [
    'controller' => 'FrasesSemelhantes',
    'action' => 'add'
])->setMethods(['POST']);

$routes->connect('/frases/edit/{id}', [
    'controller' => 'FrasesSemelhantes',
    'action' => 'edit'
])->setPatterns(['id' => '\d+'])->setMethods(['PUT']);

$routes->connect('/frases/{id}', [
    'controller' => 'FrasesSemelhantes',
    'action' => 'edit'
])->setPatterns(['id' => '\d+'])->setMethods(['PUT']);

$routes->connect('/frases/delete/{id}', [
    'controller' => 'FrasesSemelhantes',
    'action' => 'delete'
])->setPatterns(['id' => '\d+'])->setMethods(['DELETE']);

$routes->connect('/frases/{id}', [
    'controller' => 'FrasesSemelhantes',
    'action' => 'delete'
])->setPatterns(['id' => '\d+'])->setMethods(['DELETE']);

$routes->connect('/frases/prompt/{promptId}', [
    'controller' => 'FrasesSemelhantes',
    'action' => 'getByPrompt'
])->setPatterns(['promptId' => '\d+'])->setMethods(['GET']);

/**
 * FLASHCARD TAGS
 */
$routes->connect('/flashcard-tags/flashcard/{flashcardId}', [
    'controller' => 'FlashcardTags',
    'action' => 'getByFlashcard'
])->setPatterns(['flashcardId' => '\d+'])->setMethods(['GET']);

$routes->connect('/flashcard-tags', [
    'controller' => 'FlashcardTags',
    'action' => 'add'
])->setMethods(['POST']);

$routes->connect('/flashcard-tags', [
    'controller' => 'FlashcardTags',
    'action' => 'remove'
])->setMethods(['DELETE']);

$routes->connect('/progresso/incrementar-flashcards', [
    'controller' => 'Progresso',
    'action' => 'incrementarFlashcards'
])->setMethods(['POST']);

$routes->connect('/progresso/incrementar-tempo', [
    'controller' => 'Progresso',
    'action' => 'incrementarTempo'
])->setMethods(['POST']);

/**
 * FALLBACK (deve ser sempre a última rota)
 */
$routes->connect('/*', [
    'controller' => 'Users',
    'action' => 'notFound'
]);