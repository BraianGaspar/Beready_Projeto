<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ImagensGeradas Model
 *
 * @property \App\Model\Table\PromptsTable&\Cake\ORM\Association\BelongsTo $Prompts
 *
 * @method \App\Model\Entity\ImagensGerada newEmptyEntity()
 * @method \App\Model\Entity\ImagensGerada newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\ImagensGerada> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ImagensGerada get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\ImagensGerada findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\ImagensGerada patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\ImagensGerada> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ImagensGerada|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\ImagensGerada saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\ImagensGerada>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ImagensGerada>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ImagensGerada>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ImagensGerada> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ImagensGerada>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ImagensGerada>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ImagensGerada>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ImagensGerada> deleteManyOrFail(iterable $entities, array $options = [])
 */
class ImagensGeradasTable extends Table
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

        $this->setTable('imagens_geradas');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Prompts', [
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
            ->integer('prompt_id')
            ->allowEmptyString('prompt_id');

        $validator
            ->integer('traducao_id')
            ->allowEmptyString('traducao_id');

        $validator
            ->scalar('url_imagem')
            ->maxLength('url_imagem', 500)
            ->allowEmptyString('url_imagem');

        $validator
            ->scalar('prompt_imagem')
            ->allowEmptyString('prompt_imagem');

        $validator
            ->scalar('servico_geracao')
            ->maxLength('servico_geracao', 50)
            ->allowEmptyString('servico_geracao');

        $validator
            ->scalar('qualidade_imagem')
            ->allowEmptyString('qualidade_imagem');

        $validator
            ->integer('tamanho_arquivo')
            ->allowEmptyString('tamanho_arquivo');

        $validator
            ->scalar('dimensoes')
            ->maxLength('dimensoes', 20)
            ->allowEmptyString('dimensoes');

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
