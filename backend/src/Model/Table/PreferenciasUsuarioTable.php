<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * PreferenciasUsuario Model
 *
 * @property \App\Model\Table\UsuariosTable&\Cake\ORM\Association\BelongsTo $Usuarios
 *
 * @method \App\Model\Entity\PreferenciasUsuario newEmptyEntity()
 * @method \App\Model\Entity\PreferenciasUsuario newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\PreferenciasUsuario> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\PreferenciasUsuario get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\PreferenciasUsuario findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\PreferenciasUsuario patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\PreferenciasUsuario> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\PreferenciasUsuario|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\PreferenciasUsuario saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\PreferenciasUsuario>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\PreferenciasUsuario>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\PreferenciasUsuario>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\PreferenciasUsuario> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\PreferenciasUsuario>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\PreferenciasUsuario>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\PreferenciasUsuario>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\PreferenciasUsuario> deleteManyOrFail(iterable $entities, array $options = [])
 */
class PreferenciasUsuarioTable extends Table
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

        $this->setTable('preferencias_usuario');
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
            ->scalar('tema')
            ->allowEmptyString('tema');

        $validator
            ->boolean('modo_daltonico')
            ->allowEmptyString('modo_daltonico');

        $validator
            ->boolean('notificacoes_ativas')
            ->allowEmptyString('notificacoes_ativas');

        $validator
            ->boolean('som_ativo')
            ->allowEmptyString('som_ativo');

        $validator
            ->boolean('traducao_automatica')
            ->allowEmptyString('traducao_automatica');

        $validator
            ->scalar('preferencia_dificuldade')
            ->allowEmptyString('preferencia_dificuldade');

        $validator
            ->integer('meta_diaria_minutos')
            ->allowEmptyString('meta_diaria_minutos');

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
