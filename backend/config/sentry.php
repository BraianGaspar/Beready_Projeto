<?php

use function Cake\Core\env;

$dsn = env('SENTRY_DSN');

if (!empty($dsn)) {
    \Sentry\init([
        'dsn' => $dsn,
        'environment' => env('APP_ENV'),
        'traces_sample_rate' => 1.0,
        'send_default_pii' => false,
        'release' => '1.0.0',
        // Usa o repositório de certificados do sistema (TLS continua verificado)
        'http_ssl_native_ca' => true,
    ]);
}
