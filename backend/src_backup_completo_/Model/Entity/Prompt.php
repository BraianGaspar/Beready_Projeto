<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Prompt Entity
 *
 * @property int $id
 * @property int $usuario_id
 * @property string $texto_original
 * @property string|null $idioma_original
 * @property string|null $contexto
 * @property int|null $midia_origem_id
 * @property string|null $sessao_id
 * @property \Cake\I18n\DateTime|null $criado_em
 *
 * @property \App\Model\Entity\Usuario $usuario
 * @property \App\Model\Entity\Flashcard[] $flashcards
 * @property \App\Model\Entity\FrasesSemelhante[] $frases_semelhantes
 * @property \App\Model\Entity\ImagensGerada[] $imagens_geradas
 * @property \App\Model\Entity\Traduco[] $traducoes
 */
class Prompt extends Entity
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
        'usuario_id' => true,
        'texto_original' => true,
        'idioma_original' => true,
        'contexto' => true,
        'midia_origem_id' => true,
        'sessao_id' => true,
        'criado_em' => true,
        'usuario' => true,
        'flashcards' => true,
        'frases_semelhantes' => true,
        'imagens_geradas' => true,
        'traducoes' => true,
    ];
}
