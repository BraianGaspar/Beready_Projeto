<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Ignorar o nome do arquivo se for chamado diretamente
if ($path === '/api_simples.php') {
    echo json_encode(['success' => true, 'message' => 'API Simples funcionando!']);
    exit();
}

// Rota /health
if ($path === '/health') {
    echo json_encode(['success' => true, 'message' => 'API funcionando!', 'timestamp' => date('Y-m-d H:i:s')]);
    exit();
}

// Rota /auth/register
if ($path === '/auth/register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    echo json_encode(['success' => true, 'message' => 'Registro simulado', 'data' => $data]);
    exit();
}

// Rota /auth/login
if ($path === '/auth/login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    echo json_encode(['success' => true, 'message' => 'Login simulado', 'user' => ['id' => 1, 'nome' => 'Teste']]);
    exit();
}

echo json_encode(['success' => false, 'message' => "Rota $path não encontrada"]);