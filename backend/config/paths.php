<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         3.0.0
 * @license       MIT License (https://opensource.org/licenses/mit-license.php)
 */

/*
 * Use the DS to separate the directories in other defines
 */
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

/*
 * The full path to the directory which holds "src", WITHOUT a trailing DS.
 * AGORA: ROOT aponta para a pasta raiz do projeto (fora de backend)
 */
define('ROOT', dirname(__DIR__, 2));

/*
 * The actual directory name for the application directory. Normally
 * named 'src'.
 * AGORA: APP_DIR precisa incluir o caminho backend/
 */
define('APP_DIR', 'backend' . DS . 'src');

/*
 * Path to the application's directory.
 */
define('APP', ROOT . DS . APP_DIR . DS);

/*
 * Path to the config directory.
 * AGORA: config está dentro de backend/
 */
define('CONFIG', ROOT . DS . 'backend' . DS . 'config' . DS);

/*
 * File path to the webroot directory.
 * AGORA: webroot está dentro de backend/
 */
define('WWW_ROOT', ROOT . DS . 'backend' . DS . 'webroot' . DS);

/*
 * Path to the tests directory.
 */
define('TESTS', ROOT . DS . 'backend' . DS . 'tests' . DS);

/*
 * Path to the temporary files directory.
 */
define('TMP', ROOT . DS . 'backend' . DS . 'tmp' . DS);

/*
 * Path to the logs directory.
 */
define('LOGS', ROOT . DS . 'backend' . DS . 'logs' . DS);

/*
 * Path to the cache files directory. It can be shared between hosts in a multi-server setup.
 */
define('CACHE', TMP . 'cache' . DS);

/*
 * Path to the resources directory.
 */
define('RESOURCES', ROOT . DS . 'backend' . DS . 'resources' . DS);

/*
 * The absolute path to the "cake" directory, WITHOUT a trailing DS.
 *
 * CakePHP should always be installed with composer, so look there.
 */
define('CAKE_CORE_INCLUDE_PATH', ROOT . DS . 'backend' . DS . 'vendor' . DS . 'cakephp' . DS . 'cakephp');

/*
 * Path to the cake directory.
 */
define('CORE_PATH', CAKE_CORE_INCLUDE_PATH . DS);
define('CAKE', CORE_PATH . 'src' . DS);