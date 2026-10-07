<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * @property int $id
 * @property int $questao_id
 * @property string $texto
 * @property bool $correta
 * @property int $ordem
 */
class QuizAlternativa extends Entity
{
    /**
     * questao_id fica fora: é definido pelo serviço, nunca pelo corpo.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'texto' => true,
        'correta' => true,
        'ordem' => true,
    ];
}
