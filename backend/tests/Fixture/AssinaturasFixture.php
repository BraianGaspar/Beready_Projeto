<?php

declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * Sem registros fixos: a fixture só garante que a tabela `assinaturas` é limpa antes de cada teste.
 * Os testes criam os próprios dados (ver ApiTestCase).
 */
class AssinaturasFixture extends TestFixture
{
    public string $table = 'assinaturas';
}
