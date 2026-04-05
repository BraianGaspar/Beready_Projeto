<?php

use Cake\Cache\Engine\FileEngine;
use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Cake\Log\Engine\FileLog;
use Cake\Mailer\Transport\MailTransport;
use Cake\Database\Driver\Postgres;
use function Cake\Core\env;

return [
    /*
     * Debug Level:
     * Production Mode: false
     * Development Mode: true
     */
    // Mude para false em produção ou para suprimir warnings
    'debug' => filter_var(env('DEBUG', false), FILTER_VALIDATE_BOOLEAN),

    /*
     * Configure basic information about the application.
     */
    'App' => [
        'namespace' => 'App',
        'encoding' => env('APP_ENCODING', 'UTF-8'),
        'defaultLocale' => env('APP_DEFAULT_LOCALE', 'en_US'),
        'defaultTimezone' => env('APP_DEFAULT_TIMEZONE', 'UTC'),
        'base' => false,
        'dir' => 'src',
        'webroot' => 'webroot',
        'wwwRoot' => WWW_ROOT,
        'fullBaseUrl' => false,
        'imageBaseUrl' => 'img/',
        'cssBaseUrl' => 'css/',
        'jsBaseUrl' => 'js/',
        'paths' => [
            'plugins' => [ROOT . DS . 'plugins' . DS],
            'templates' => [dirname(__DIR__) . DS . 'templates' . DS],
            'locales' => [RESOURCES . 'locales' . DS],
        ],
    ],

    /*
     * Security and encryption configuration
     */
    'Security' => [
        'salt' => env('SECURITY_SALT', 'd7a8fbb307d7809469ca9abcb0082e4f8d5651e46d3cdb762d02d0bf37c9e592'),
    ],

    /*
     * Asset timestamps
     */
    'Asset' => [
        //'timestamp' => true,
    ],

    /*
     * Cache adapters - MODIFICADO para usar Array em desenvolvimento
     */
    'Cache' => [
        'default' => [
            'className' => FileEngine::class,
            'path' => CACHE,
            'url' => env('CACHE_DEFAULT_URL', null),
        ],
        // Usa cache em memória para evitar problemas de permissão
        '_cake_core_' => [
            'className' => 'Array',  // Mudado de FileEngine para Array
            'prefix' => 'myapp_cake_core_',
            'serialize' => true,
            'duration' => '+1 years',
        ],
        '_cake_model_' => [
            'className' => 'Array',  // Mudado de FileEngine para Array
            'prefix' => 'myapp_cake_model_',
            'serialize' => true,
            'duration' => '+1 years',
        ],
        '_cake_translations_' => [
            'className' => 'Array',  // Mudado de FileEngine para Array
            'prefix' => 'myapp_cake_translations_',
            'serialize' => true,
            'duration' => '+1 years',
        ],
    ],

    /*
     * Error and Exception handlers
     */
    'Error' => [
        'errorLevel' => E_ALL & ~E_WARNING & ~E_USER_WARNING & ~E_NOTICE & ~E_DEPRECATED,
        'skipLog' => [],
        'log' => false,  // Desabilita log de erros para desenvolvimento
        'trace' => false,  // Desabilita trace para não poluir a saída
        'ignoredDeprecationPaths' => [],
    ],

    /*
     * Debugger configuration
     */
    'Debugger' => [
        'editor' => 'phpstorm',
    ],

    /*
     * Email configuration
     */
    'EmailTransport' => [
        'default' => [
            'className' => MailTransport::class,
            'host' => 'localhost',
            'port' => 25,
            'timeout' => 30,
            'client' => null,
            'tls' => false,
            'url' => env('EMAIL_TRANSPORT_DEFAULT_URL', null),
        ],
    ],

    'Email' => [
        'default' => [
            'transport' => 'default',
            'from' => 'you@localhost',
        ],
    ],

    /*
     * Database connections
     */
    'Datasources' => [
        'default' => [
            'className' => Connection::class,
            'driver' => Postgres::class,
            'host' => 'aws-1-sa-east-1.pooler.supabase.com',
            'port' => 6543,
            'username' => 'postgres.bwppzgxlcqxfuzdehgyb',
            'password' => 'B3r3@dy#Sup4_9Xq!2026',
            'database' => 'postgres',
            'encoding' => 'utf8',
            'timezone' => 'UTC',
            'cacheMetadata' => true,
            'quoteIdentifiers' => false,
        ],
        'test' => [
            'className' => Connection::class,
            'driver' => Mysql::class,
            'persistent' => false,
            'timezone' => 'UTC',
            'encoding' => 'utf8mb4',
            'flags' => [],
            'cacheMetadata' => true,
            'quoteIdentifiers' => false,
            'log' => false,
        ],
    ],

    /*
     * Logging configuration
     */
    'Log' => [
        'debug' => [
            'className' => FileLog::class,
            'path' => LOGS,
            'file' => 'debug',
            'url' => env('LOG_DEBUG_URL', null),
            'scopes' => null,
            'levels' => ['notice', 'info', 'debug'],
        ],
        'error' => [
            'className' => FileLog::class,
            'path' => LOGS,
            'file' => 'error',
            'url' => env('LOG_ERROR_URL', null),
            'scopes' => null,
            'levels' => ['warning', 'error', 'critical', 'alert', 'emergency'],
        ],
        'queries' => [
            'className' => FileLog::class,
            'path' => LOGS,
            'file' => 'queries',
            'url' => env('LOG_QUERIES_URL', null),
            'scopes' => ['cake.database.queries'],
        ],
    ],

    /*
     * Session configuration
     */
    'Session' => [
        'defaults' => 'php',
    ],

    /*
     * DebugKit configuration
     */
    'DebugKit' => [
        'forceEnable' => false,
        'safeTld' => env('DEBUG_KIT_SAFE_TLD', null),
        'ignoreAuthorization' => env('DEBUG_KIT_IGNORE_AUTHORIZATION', false),
    ],

    'TestSuite' => [
        'errorLevel' => null,
        'fixtureStrategy' => null,
    ],
];