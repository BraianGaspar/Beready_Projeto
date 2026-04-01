<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * FrasesSemelhantes Model
 *
 * @property \App\Model\Table\PromptsTable&\Cake\ORM\Association\BelongsTo $Prompts
 *
 * @method \App\Model\Entity\FrasesSemelhante newEmptyEntity()
 * @method \App\Model\Entity\FrasesSemelhante newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\FrasesSemelhante> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\FrasesSemelhante get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\FrasesSemelhante findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\FrasesSemelhante patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\FrasesSemelhante> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\FrasesSemelhante|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\FrasesSemelhante saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\FrasesSemelhante>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\FrasesSemelhante>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\FrasesSemelhante>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\FrasesSemelhante> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\FrasesSemelhante>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\FrasesSemelhante>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\FrasesSemelhante>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\FrasesSemelhante> deleteManyOrFail(iterable $entities, array $options = [])
 */
class FrasesSemelhantesTable extends Table
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

        $this->setTable('frases_semelhantes');
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
            ->scalar('frase_semelhante')
            ->allowEmptyString('frase_semelhante');

        $validator
            ->decimal('pontuacao_semelhante')
            ->allowEmptyString('pontuacao_semelhante');

        $validator
            ->scalar('tipo_frase')
            ->allowEmptyString('tipo_frase');

        $validator
            ->scalar('nivel_dificuldade')
            ->allowEmptyString('nivel_dificuldade');

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
