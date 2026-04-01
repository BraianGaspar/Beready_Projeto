<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * FrasesSemelhante Entity
 *
 * @property int $id
 * @property int $prompt_id
 * @property string|null $frase_semelhante
 * @property string|null $pontuacao_semelhante
 * @property string|null $tipo_frase
 * @property string|null $nivel_dificuldade
 * @property \Cake\I18n\DateTime|null $criado_em
 *
 * @property \App\Model\Entity\Prompt $prompt
 */
class FrasesSemelhante extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'prompt_id' => true,
        'frase_semelhante' => true,
        'pontuacao_semelhante' => true,
        'tipo_frase' => true,
        'nivel_dificuldade' => true,
        'criado_em' => true,
        'prompt' => true,
    ];
}
