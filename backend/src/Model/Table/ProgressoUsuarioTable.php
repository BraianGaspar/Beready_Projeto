<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ProgressoUsuario Model
 *
 * @property \App\Model\Table\UsuariosTable&\Cake\ORM\Association\BelongsTo $Usuarios
 *
 * @method \App\Model\Entity\ProgressoUsuario newEmptyEntity()
 * @method \App\Model\Entity\ProgressoUsuario newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\ProgressoUsuario> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ProgressoUsuario get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\ProgressoUsuario findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\ProgressoUsuario patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\ProgressoUsuario> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ProgressoUsuario|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\ProgressoUsuario saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\ProgressoUsuario>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ProgressoUsuario>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ProgressoUsuario>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ProgressoUsuario> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ProgressoUsuario>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ProgressoUsuario>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ProgressoUsuario>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ProgressoUsuario> deleteManyOrFail(iterable $entities, array $options = [])
 */
class ProgressoUsuarioTable extends Table
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

        $this->setTable('progresso_usuario');
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
            ->notEmptyString('usuario_id')
            ->add('usuario_id', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->integer('vocabulario_aprendido')
            ->allowEmptyString('vocabulario_aprendido');

        $validator
            ->integer('flashcards_concluidos')
            ->allowEmptyString('flashcards_concluidos');

        $validator
            ->integer('quizes_concluidos')
            ->allowEmptyString('quizes_concluidos');

        $validator
            ->integer('tempo_total_estudo')
            ->allowEmptyString('tempo_total_estudo');

        $validator
            ->integer('sequencia_atual')
            ->allowEmptyString('sequencia_atual');

        $validator
            ->integer('maior_sequencia')
            ->allowEmptyString('maior_sequencia');

        $validator
            ->dateTime('ultima_atividade')
            ->allowEmptyDateTime('ultima_atividade');

        $validator
            ->allowEmptyString('progresso_nivel');

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
        $rules->add($rules->isUnique(['usuario_id']), ['errorField' => 'usuario_id']);
        $rules->add($rules->existsIn(['usuario_id'], 'Usuarios'), ['errorField' => 'usuario_id']);

        return $rules;
    }
}
