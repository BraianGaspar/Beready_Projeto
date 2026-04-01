<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class FlashcardTagsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        
        $this->setTable('flashcard_tags'); // ✅ CORRIGIDO
        $this->setPrimaryKey('id');
        
        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'criado_em' => 'new'
                ]
            ]
        ]);
        
        // Associação com Flashcards
        $this->belongsTo('Flashcards', [
            'foreignKey' => 'flashcard_id',
            'joinType' => 'INNER',
        ]);
        
        // Associação com Tags
        $this->belongsTo('Tags', [
            'foreignKey' => 'tag_id',
            'joinType' => 'INNER',
        ]);
    }
    
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('flashcard_id')
            ->requirePresence('flashcard_id', 'create')
            ->notEmptyString('flashcard_id');
        
        $validator
            ->integer('tag_id')
            ->requirePresence('tag_id', 'create')
            ->notEmptyString('tag_id');
        
        return $validator;
    }
}