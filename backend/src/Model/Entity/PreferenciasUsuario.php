<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PreferenciasUsuario Entity
 *
 * @property int $id
 * @property int $usuario_id
 * @property string|null $tema
 * @property bool|null $modo_daltonico
 * @property bool|null $notificacoes_ativas
 * @property bool|null $som_ativo
 * @property bool|null $traducao_automatica
 * @property string|null $preferencia_dificuldade
 * @property int|null $meta_diaria_minutos
 * @property \Cake\I18n\DateTime|null $criado_em
 * @property \Cake\I18n\DateTime|null $atualizado_em
 *
 * @property \App\Model\Entity\Usuario $usuario
 */
class PreferenciasUsuario extends Entity
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
        'tema' => true,
        'modo_daltonico' => true,
        'notificacoes_ativas' => true,
        'som_ativo' => true,
        'traducao_automatica' => true,
        'preferencia_dificuldade' => true,
        'meta_diaria_minutos' => true,
        'criado_em' => true,
        'atualizado_em' => true,
        'usuario' => true,
    ];
}
