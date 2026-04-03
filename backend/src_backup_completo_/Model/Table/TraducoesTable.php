<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Traducoes Model
 *
 * @property \App\Model\Table\PromptsTable&\Cake\ORM\Association\BelongsTo $Prompts
 *
 * @method \App\Model\Entity\Traduco newEmptyEntity()
 * @method \App\Model\Entity\Traduco newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Traduco> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Traduco get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Traduco findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Traduco patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Traduco> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Traduco|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Traduco saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Traduco>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Traduco>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Traduco>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Traduco> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Traduco>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Traduco>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Traduco>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Traduco> deleteManyOrFail(iterable $entities, array $options = [])
 */
class TraducoesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('traducoes');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Prompts', [
            'foreignKey' => 'prompt_id',
            'joinType' => 'INNER',
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
            ->integer('prompt_id')
            ->notEmptyString('prompt_id');

        $validator
            ->scalar('texto_traduzido')
            ->allowEmptyString('texto_traduzido');

        $validator
            ->scalar('idioma_destino')
            ->maxLength('idioma_destino', 10)
            ->allowEmptyString('idioma_destino');

        $validator
            ->decimal('pontuacao_confianca')
            ->allowEmptyString('pontuacao_confianca');

        $validator
            ->scalar('servico_traducao')
            ->maxLength('servico_traducao', 50)
            ->allowEmptyString('servico_traducao');

        $validator
            ->allowEmptyString('traducoes_alternativas');

        $validator
            ->dateTime('criado_em')
            ->allowEmptyDateTime('criado_em');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['prompt_id'], 'Prompts'), ['errorField' => 'prompt_id']);

        return $rules;
    }
}
