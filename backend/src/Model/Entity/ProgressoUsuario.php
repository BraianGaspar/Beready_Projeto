<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ProgressoUsuario Entity
 *
 * @property int $id
 * @property int $usuario_id
 * @property int|null $vocabulario_aprendido
 * @property int|null $flashcards_concluidos
 * @property int|null $quizes_concluidos
 * @property int|null $tempo_total_estudo
 * @property int|null $sequencia_atual
 * @property int|null $maior_sequencia
 * @property \Cake\I18n\DateTime|null $ultima_atividade
 * @property array|null $progresso_nivel
 * @property \Cake\I18n\DateTime|null $atualizado_em
 *
 * @property \App\Model\Entity\Usuario $usuario
 */
class ProgressoUsuario extends Entity
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
        'vocabulario_aprendido' => true,
        'flashcards_concluidos' => true,
        'quizes_concluidos' => true,
        'tempo_total_estudo' => true,
        'sequencia_atual' => true,
        'maior_sequencia' => true,
        'ultima_atividade' => true,
        'progresso_nivel' => true,
        'atualizado_em' => true,
        'usuario' => true,
    ];
}
