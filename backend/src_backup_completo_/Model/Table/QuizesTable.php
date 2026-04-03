<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Quizes Model
 *
 * @property \App\Model\Table\UsuariosTable&\Cake\ORM\Association\BelongsTo $Usuarios
 *
 * @method \App\Model\Entity\Quize newEmptyEntity()
 * @method \App\Model\Entity\Quize newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Quize> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Quize get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Quize findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Quize patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Quize> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Quize|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Quize saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Quize>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Quize>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Quize>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Quize> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Quize>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Quize>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Quize>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Quize> deleteManyOrFail(iterable $entities, array $options = [])
 */
class QuizesTable extends Table
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

        $this->setTable('quizes');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Usuarios', [
            'foreignKey' => 'usuario_id',
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
            ->integer('usuario_id')
            ->notEmptyString('usuario_id');

        $validator
            ->scalar('titulo')
            ->maxLength('titulo', 200)
            ->allowEmptyString('titulo');

        $validator
            ->scalar('descricao')
            ->allowEmptyString('descricao');

        $validator
            ->scalar('tipo_criacao')
            ->allowEmptyString('tipo_criacao');

        $validator
            ->scalar('nivel_dificuldade')
            ->allowEmptyString('nivel_dificuldade');

        $validator
            ->integer('total_questoes')
            ->allowEmptyString('total_questoes');

        $validator
            ->integer('tempo_limite')
            ->allowEmptyString('tempo_limite');

        $validator
            ->boolean('publico')
            ->allowEmptyString('publico');

        $validator
            ->dateTime('criado_em')
            ->allowEmptyDateTime('criado_em');

        $validator
            ->dateTime('atualizado_em')
            ->allowEmptyDateTime('atualizado_em');

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
        $rules->add($rules->existsIn(['usuario_id'], 'Usuarios'), ['errorField' => 'usuario_id']);

        return $rules;
    }
}
