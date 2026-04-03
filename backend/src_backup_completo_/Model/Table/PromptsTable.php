<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Prompts Model
 *
 * @property \App\Model\Table\UsuariosTable&\Cake\ORM\Association\BelongsTo $Usuarios
 * @property \App\Model\Table\FlashcardsTable&\Cake\ORM\Association\HasMany $Flashcards
 * @property \App\Model\Table\FrasesSemelhantesTable&\Cake\ORM\Association\HasMany $FrasesSemelhantes
 * @property \App\Model\Table\ImagensGeradasTable&\Cake\ORM\Association\HasMany $ImagensGeradas
 * @property \App\Model\Table\TraducoesTable&\Cake\ORM\Association\HasMany $Traducoes
 *
 * @method \App\Model\Entity\Prompt newEmptyEntity()
 * @method \App\Model\Entity\Prompt newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Prompt> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Prompt get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Prompt findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Prompt patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Prompt> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Prompt|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Prompt saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Prompt>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Prompt>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Prompt>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Prompt> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Prompt>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Prompt>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Prompt>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Prompt> deleteManyOrFail(iterable $entities, array $options = [])
 */
class PromptsTable extends Table
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

        $this->setTable('prompts');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Usuarios', [
            'foreignKey' => 'usuario_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Flashcards', [
            'foreignKey' => 'prompt_id',
        ]);
        $this->hasMany('FrasesSemelhantes', [
            'foreignKey' => 'prompt_id',
        ]);
        $this->hasMany('ImagensGeradas', [
            'foreignKey' => 'prompt_id',
        ]);
        $this->hasMany('Traducoes', [
            'foreignKey' => 'prompt_id',
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
            ->scalar('texto_original')
            ->requirePresence('texto_original', 'create')
            ->notEmptyString('texto_original');

        $validator
            ->scalar('idioma_original')
            ->maxLength('idioma_original', 10)
            ->allowEmptyString('idioma_original');

        $validator
            ->scalar('contexto')
            ->allowEmptyString('contexto');

        $validator
            ->integer('midia_origem_id')
            ->allowEmptyString('midia_origem_id');

        $validator
            ->scalar('sessao_id')
            ->maxLength('sessao_id', 100)
            ->allowEmptyString('sessao_id');

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
        $rules->add($rules->existsIn(['usuario_id'], 'Usuarios'), ['errorField' => 'usuario_id']);

        return $rules;
    }
}
