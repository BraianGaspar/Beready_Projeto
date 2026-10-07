<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * @property int $id
 * @property int $quiz_id
 * @property string $tipo
 * @property string $enunciado
 * @property string|null $resposta_esperada
 * @property string|null $explicacao
 * @property int $ordem
 * @property \Cake\I18n\DateTime|null $criado_em
 * @property \Cake\I18n\DateTime|null $atualizado_em
 * @property \App\Model\Entity\QuizAlternativa[] $quiz_alternativas
 */
class QuizQuestao extends Entity
{
    /**
     * quiz_id fica fora: é definido pelo serviço a partir da URL, nunca pelo corpo.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'tipo' => true,
        'enunciado' => true,
        'resposta_esperada' => true,
        'explicacao' => true,
        'ordem' => true,
    ];
}
