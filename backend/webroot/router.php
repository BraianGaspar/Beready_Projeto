<?php

// 🔥 CORS (ESSENCIAL)
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Pega o caminho da URL
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Arquivos estáticos - serve direto
if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico)$/', $path)) {
    return false;
}

// ============================================
// API ROUTES
// ============================================

// Health check
if ($path === '/health') {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'API funcionando!',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit();
}

// Registro de usuário
if ($path === '/auth/register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);

    echo json_encode([
        'success' => true,
        'message' => 'Registro simulado',
        'data' => $data
    ]);
    exit();
}

// Login de usuário
if ($path === '/auth/login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);

    echo json_encode([
        'success' => true,
        'message' => 'Login simulado',
        'user' => [
            'id' => 1,
            'nome' => $data['email'] ?? 'Usuário'
        ]
    ]);
    exit();
}

// 🔥 LOGOUT - ADICIONADO
if ($path === '/auth/logout' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Logout realizado com sucesso'
    ]);
    exit();
}

if (file_exists(__DIR__ . $path) && !is_dir(__DIR__ . $path)) {
    return false;
}

// Fallback para o CakePHP (se existir)
if (file_exists(__DIR__ . '/index_cake.php')) {
    require __DIR__ . '/index_cake.php';
} else {
    // Se não tiver o index_cake.php, retorna 404
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Rota não encontrada'
    ]);
}