<?php

// ============================================
// CORS CONFIGURATION
// ============================================
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-CSRF-Token, X-Requested-With');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Pega o caminho da URL
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Arquivos estáticos - serve direto
if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf)$/', $path)) {
    return false;
}

// ============================================
// FUNÇÃO PARA CONEXÃO COM O BANCO
// ============================================
function getDBConnection() {
    $database_url = getenv('DATABASE_URL');
    
    if (!$database_url) {
        $envFile = __DIR__ . '/../config/.env';
        if (file_exists($envFile)) {
            $lines = file($envFile);
            foreach ($lines as $line) {
                if (strpos($line, 'export DATABASE_URL=') === 0) {
                    $database_url = trim(str_replace('export DATABASE_URL=', '', $line));
                    $database_url = trim($database_url, '"');
                    break;
                }
            }
        }
    }
    
    if (!$database_url) {
        throw new Exception('DATABASE_URL não configurada');
    }
    
    $remaining = substr($database_url, strlen('postgres://'));
    $lastAtPos = strrpos($remaining, '@');
    if ($lastAtPos === false) {
        throw new Exception('Formato de DATABASE_URL inválido');
    }
    
    $userPass = substr($remaining, 0, $lastAtPos);
    $hostDb = substr($remaining, $lastAtPos + 1);
    
    $firstColonPos = strpos($userPass, ':');
    if ($firstColonPos === false) {
        $user = $userPass;
        $password = '';
    } else {
        $user = substr($userPass, 0, $firstColonPos);
        $password = substr($userPass, $firstColonPos + 1);
    }
    
    $slashPos = strpos($hostDb, '/');
    if ($slashPos === false) {
        $hostPort = $hostDb;
        $dbname = '';
    } else {
        $hostPort = substr($hostDb, 0, $slashPos);
        $dbname = substr($hostDb, $slashPos + 1);
        $questionPos = strpos($dbname, '?');
        if ($questionPos !== false) {
            $dbname = substr($dbname, 0, $questionPos);
        }
    }
    
    $host = $hostPort;
    $port = 5432;
    if (strpos($hostPort, ':') !== false) {
        $parts = explode(':', $hostPort);
        $host = $parts[0];
        $port = (int)$parts[1];
    }
    
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    
    try {
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        return $pdo;
    } catch (PDOException $e) {
        $encodedPassword = urlencode($password);
        $pdo = new PDO($dsn, $user, $encodedPassword, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        return $pdo;
    }
}

// ============================================
// HEALTH CHECK
// ============================================
if ($path === '/health') {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'API funcionando!',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit();
}

// ============================================
// AUTH ROUTES
// ============================================

// Registro
if ($path === '/auth/register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['nome']) || empty($data['email']) || empty($data['senha'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Nome, e-mail e senha são obrigatórios'
        ]);
        exit();
    }

    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) {
            http_response_code(409);
            echo json_encode([
                'success' => false,
                'message' => 'Este e-mail já está cadastrado'
            ]);
            exit();
        }
        
        $hashedPassword = password_hash($data['senha'], PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("
            INSERT INTO users (nome, email, senha_hash, telefone, nivel_ingles, idioma_preferido, status, objetivos_aprendizado, criado_em, atualizado_em) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            RETURNING id, nome, email, telefone, nivel_ingles, idioma_preferido, status, objetivos_aprendizado, criado_em
        ");
        
        $stmt->execute([
            $data['nome'],
            $data['email'],
            $hashedPassword,
            $data['telefone'] ?? null,
            $data['nivel_ingles'] ?? 'iniciante',
            $data['idioma_preferido'] ?? 'pt-BR',
            'ativo',
            $data['objetivos_aprendizado'] ?? null
        ]);
        
        $user = $stmt->fetch();
        
        echo json_encode([
            'success' => true,
            'message' => 'Registro realizado com sucesso',
            'user' => $user
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao registrar usuário: ' . $e->getMessage()
        ]);
    }
    exit();
}

// Login
if ($path === '/auth/login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['email']) || empty($data['password'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'E-mail e senha são obrigatórios'
        ]);
        exit();
    }

    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("SELECT id, nome, email, senha_hash, telefone, nivel_ingles, idioma_preferido, status, objetivos_aprendizado FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        $user = $stmt->fetch();
        
        if (!$user) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'E-mail ou senha inválidos'
            ]);
            exit();
        }
        
        if (password_verify($data['password'], $user['senha_hash'])) {
            $updateStmt = $pdo->prepare("UPDATE users SET ultimo_login = NOW() WHERE id = ?");
            $updateStmt->execute([$user['id']]);
            
            unset($user['senha_hash']);
            
            echo json_encode([
                'success' => true,
                'message' => 'Login realizado com sucesso',
                'user' => $user
            ]);
        } else {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'E-mail ou senha inválidos'
            ]);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao processar login'
        ]);
    }
    exit();
}

// Logout
if ($path === '/auth/logout' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Logout realizado com sucesso'
    ]);
    exit();
}

// Forgot Password
if ($path === '/auth/forgot-password' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    
    echo json_encode([
        'success' => true,
        'message' => 'Link de recuperação enviado para seu e-mail!',
        'reset_link' => 'http://localhost:5173/reset-password/token_simulado_' . md5(uniqid())
    ]);
    exit();
}

// Reset Password
if (preg_match('/^\/auth\/reset-password\/(.+)$/', $path, $matches) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $token = $matches[1];
    $data = json_decode(file_get_contents('php://input'), true);
    
    echo json_encode([
        'success' => true,
        'message' => 'Senha redefinida com sucesso!'
    ]);
    exit();
}

// ============================================
// USERS ROUTES
// ============================================

// Buscar usuário
if (preg_match('/^\/users\/(\d+)$/', $path, $matches) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json');
    $userId = (int)$matches[1];
    
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT id, nome, email, telefone, nivel_ingles, idioma_preferido, status, objetivos_aprendizado FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if (!$user) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Usuário não encontrado'
            ]);
            exit();
        }
        
        echo json_encode([
            'success' => true,
            'user' => $user
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao buscar usuário'
        ]);
    }
    exit();
}

// Atualizar usuário
if (preg_match('/^\/users\/(\d+)$/', $path, $matches) && $_SERVER['REQUEST_METHOD'] === 'PUT') {
    header('Content-Type: application/json');
    $userId = (int)$matches[1];
    $data = json_decode(file_get_contents('php://input'), true);
    
    try {
        $pdo = getDBConnection();
        
        $updates = [];
        $params = [];
        
        if (isset($data['nome'])) {
            $updates[] = "nome = ?";
            $params[] = $data['nome'];
        }
        if (isset($data['email'])) {
            $updates[] = "email = ?";
            $params[] = $data['email'];
        }
        if (isset($data['telefone'])) {
            $updates[] = "telefone = ?";
            $params[] = $data['telefone'];
        }
        if (isset($data['nivel_ingles'])) {
            $updates[] = "nivel_ingles = ?";
            $params[] = $data['nivel_ingles'];
        }
        if (isset($data['idioma_preferido'])) {
            $updates[] = "idioma_preferido = ?";
            $params[] = $data['idioma_preferido'];
        }
        if (isset($data['status'])) {
            $updates[] = "status = ?";
            $params[] = $data['status'];
        }
        if (isset($data['objetivos_aprendizado'])) {
            $updates[] = "objetivos_aprendizado = ?";
            $params[] = $data['objetivos_aprendizado'];
        }
        if (isset($data['senha'])) {
            $updates[] = "senha_hash = ?";
            $params[] = password_hash($data['senha'], PASSWORD_DEFAULT);
        }
        
        $updates[] = "atualizado_em = NOW()";
        
        if (empty($updates)) {
            echo json_encode([
                'success' => true,
                'message' => 'Nenhuma alteração realizada'
            ]);
            exit();
        }
        
        $params[] = $userId;
        $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        $stmt = $pdo->prepare("SELECT id, nome, email, telefone, nivel_ingles, idioma_preferido, status, objetivos_aprendizado FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        echo json_encode([
            'success' => true,
            'message' => 'Perfil atualizado com sucesso',
            'user' => $user
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao atualizar usuário'
        ]);
    }
    exit();
}

// Deletar usuário
if (preg_match('/^\/users\/(\d+)$/', $path, $matches) && $_SERVER['REQUEST_METHOD'] === 'DELETE') {
    header('Content-Type: application/json');
    $userId = (int)$matches[1];
    
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if (!$user) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Usuário não encontrado'
            ]);
            exit();
        }
        
        if ($userId === 1) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Não é possível excluir o usuário administrador'
            ]);
            exit();
        }
        
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Conta excluída com sucesso'
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao excluir conta'
        ]);
    }
    exit();
}

// ============================================
// FALLBACK
// ============================================

if (file_exists(__DIR__ . $path) && !is_dir(__DIR__ . $path)) {
    return false;
}

http_response_code(404);
header('Content-Type: application/json');
echo json_encode([
    'success' => false,
    'message' => 'Rota não encontrada'
]);