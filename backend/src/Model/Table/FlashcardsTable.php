<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Flashcards Model 
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\TagsTable&\Cake\ORM\Association\BelongsToMany $Tags
 */
class FlashcardsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('flashcards');
        $this->setDisplayField('question');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        // Relacionamento com Users
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);

        // Relacionamento Many-to-Many com Tags através de FlashcardTags
        $this->belongsToMany('Tags', [
            'foreignKey' => 'flashcard_id',
            'targetForeignKey' => 'tag_id',
            'joinTable' => 'flashcard_tags',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('user_id')
            ->requirePresence('user_id', 'create')
            ->notEmptyString('user_id');

        $validator
            ->scalar('question')
            ->maxLength('question', 1000)
            ->requirePresence('question', 'create')
            ->notEmptyString('question', 'A pergunta é obrigatória');

        $validator
            ->scalar('answer')
            ->maxLength('answer', 2000)
            ->requirePresence('answer', 'create')
            ->notEmptyString('answer', 'A resposta é obrigatória');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(\Cake\ORM\RulesChecker $rules): \Cake\ORM\RulesChecker
    {
        $rules->add($rules->existsIn('user_id', 'Users'), [
            'errorField' => 'user_id',
            'message' => 'Usuário não encontrado'
        ]);

        return $rules;
    }
}