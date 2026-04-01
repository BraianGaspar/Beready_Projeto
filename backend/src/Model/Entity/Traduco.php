<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Traduco Entity
 *
 * @property int $id
 * @property int $prompt_id
 * @property string|null $texto_traduzido
 * @property string|null $idioma_destino
 * @property string|null $pontuacao_confianca
 * @property string|null $servico_traducao
 * @property array|null $traducoes_alternativas
 * @property \Cake\I18n\DateTime|null $criado_em
 *
 * @property \App\Model\Entity\Prompt $prompt
 */
class Traduco extends Entity
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
        'texto_traduzido' => true,
        'idioma_destino' => true,
        'pontuacao_confianca' => true,
        'servico_traducao' => true,
        'traducoes_alternativas' => true,
        'criado_em' => true,
        'prompt' => true,
    ];
}
