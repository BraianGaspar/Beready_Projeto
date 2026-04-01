<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Flashcard Entity
 *
 * @property int $id
 * @property int $user_id
 * @property string $question
 * @property string $answer
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Tag[] $tags
 */
class Flashcard extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'user_id' => true,
        'question' => true,
        'answer' => true,
        'created' => true,
        'modified' => true,
        'user' => true,
        'tags' => true,
    ];
}