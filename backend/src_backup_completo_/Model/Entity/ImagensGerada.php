<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ImagensGerada Entity
 *
 * @property int $id
 * @property int|null $prompt_id
 * @property int|null $traducao_id
 * @property string|null $url_imagem
 * @property string|null $prompt_imagem
 * @property string|null $servico_geracao
 * @property string|null $qualidade_imagem
 * @property int|null $tamanho_arquivo
 * @property string|null $dimensoes
 * @property \Cake\I18n\DateTime|null $criado_em
 *
 * @property \App\Model\Entity\Prompt $prompt
 */
class ImagensGerada extends Entity
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
        'traducao_id' => true,
        'url_imagem' => true,
        'prompt_imagem' => true,
        'servico_geracao' => true,
        'qualidade_imagem' => true,
        'tamanho_arquivo' => true,
        'dimensoes' => true,
        'criado_em' => true,
        'prompt' => true,
    ];
}
