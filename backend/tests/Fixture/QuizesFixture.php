<?php

declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * Sem registros fixos: a fixture só garante que a tabela `quizes` é limpa antes de cada teste.
 * Os testes criam os próprios dados (ver ApiTestCase).
 */
class QuizesFixture extends TestFixture
{
    public string $table = 'quizes';
}
