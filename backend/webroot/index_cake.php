<?php
// ============================================
// CORS CONFIGURATION - MUST BE FIRST
// ============================================
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-CSRF-Token, X-Requested-With');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ============================================
// CakePHP Bootstrap - CAMINHOS CORRIGIDOS
// ============================================
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

// 🔥 CORRIGIDO: ROOT aponta para a pasta backend
define('ROOT', dirname(__DIR__));

define('APP_DIR', 'src');
define('APP', ROOT . DS . APP_DIR . DS);
define('CONFIG', ROOT . DS . 'config' . DS);
define('WWW_ROOT', ROOT . DS . 'webroot' . DS);
define('LOGS', ROOT . DS . 'logs' . DS);
define('TMP', ROOT . DS . 'tmp' . DS);
define('CACHE', TMP . 'cache' . DS);
define('CAKE_CORE_INCLUDE_PATH', ROOT . DS . 'vendor' . DS . 'cakephp' . DS . 'cakephp');
define('CORE_PATH', CAKE_CORE_INCLUDE_PATH . DS);
define('CAKE', CORE_PATH . 'src' . DS);

// 🔥 CORRIGIDO: Caminho correto para o autoload
require ROOT . DS . 'vendor' . DS . 'autoload.php';

use App\Application;
use Cake\Http\Server;

$app = new Application(CONFIG);
$server = new Server($app);
$server->emit($server->run());