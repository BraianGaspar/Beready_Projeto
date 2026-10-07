<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\QuizQuestao;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Questões de um quiz. Regras que dependem do tipo (alternativas, resposta esperada)
 * ficam no QuizQuestaoService.
 *
 * @property \App\Model\Table\QuizesTable&\Cake\ORM\Association\BelongsTo $Quizes
 * @property \App\Model\Table\QuizAlternativasTable&\Cake\ORM\Association\HasMany $QuizAlternativas
 */
class QuizQuestoesTable extends Table
{
    public const TIPOS = ['multipla_escolha', 'completar'];

    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('quiz_questoes');
        $this->setDisplayField('enunciado');
        $this->setPrimaryKey('id');
        $this->setEntityClass(QuizQuestao::class);

        $this->belongsTo('Quizes', [
            'foreignKey' => 'quiz_id',
            'joinType' => 'INNER',
        ]);

        // O banco já apaga em cascata; dependent mantém o ORM coerente
        $this->hasMany('QuizAlternativas', [
            'foreignKey' => 'questao_id',
            'sort' => ['QuizAlternativas.ordem' => 'ASC', 'QuizAlternativas.id' => 'ASC'],
            'dependent' => true,
        ]);

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'criado_em' => 'new',
                    'atualizado_em' => 'always',
                ],
            ],
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('quiz_id')
            ->requirePresence('quiz_id', 'create')
            ->notEmptyString('quiz_id');

        $validator
            ->scalar('tipo')
            ->inList('tipo', self::TIPOS)
            ->requirePresence('tipo', 'create')
            ->notEmptyString('tipo');

        $validator
            ->scalar('enunciado')
            ->maxLength('enunciado', 2000)
            ->requirePresence('enunciado', 'create')
            ->notEmptyString('enunciado');

        $validator
            ->scalar('resposta_esperada')
            ->maxLength('resposta_esperada', 500)
            ->allowEmptyString('resposta_esperada');

        $validator
            ->scalar('explicacao')
            ->maxLength('explicacao', 2000)
            ->allowEmptyString('explicacao');

        $validator
            ->integer('ordem')
            ->allowEmptyString('ordem');

        return $validator;
    }
}
