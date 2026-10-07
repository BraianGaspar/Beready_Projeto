<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\QuizAlternativa;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * @property \App\Model\Table\QuizQuestoesTable&\Cake\ORM\Association\BelongsTo $QuizQuestoes
 */
class QuizAlternativasTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('quiz_alternativas');
        $this->setDisplayField('texto');
        $this->setPrimaryKey('id');
        $this->setEntityClass(QuizAlternativa::class);

        $this->belongsTo('QuizQuestoes', [
            'foreignKey' => 'questao_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('questao_id')
            ->requirePresence('questao_id', 'create')
            ->notEmptyString('questao_id');

        $validator
            ->scalar('texto')
            ->maxLength('texto', 2000)
            ->requirePresence('texto', 'create')
            ->notEmptyString('texto');

        $validator
            ->boolean('correta')
            ->requirePresence('correta', 'create');

        $validator
            ->integer('ordem')
            ->allowEmptyString('ordem');

        return $validator;
    }
}
