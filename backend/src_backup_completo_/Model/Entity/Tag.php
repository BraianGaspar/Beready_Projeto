<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Tag Entity
 *
 * @property int $id
 * @property string|null $nome
 * @property string|null $cor
 * @property string|null $descricao
 * @property int|null $criado_por
 * @property bool|null $tag_sistema
 * @property \Cake\I18n\DateTime|null $criado_em
 *
 * @property \App\Model\Entity\FlashcardTag[] $flashcard_tags // 🔥 CORRETO
 */
class Tag extends Entity
{
    protected array $_accessible = [
        'nome' => true,
        'cor' => true,
        'descricao' => true,
        'criado_por' => true,
        'tag_sistema' => true,
        'criado_em' => true,
        'flashcard_tags' => true, // 🔥 CORRETO
    ];
}