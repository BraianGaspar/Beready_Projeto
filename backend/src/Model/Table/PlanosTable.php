<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class PlanosTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('planos');
        $this->setDisplayField('nome');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created_at' => 'new',
                    'updated_at' => 'always',
                ],
            ],
        ]);

        // Relacionamento com Roles
        $this->belongsTo('Roles', [
            'foreignKey' => 'role_id',
            'className' => 'Roles',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('nome')
            ->maxLength('nome', 50)
            ->requirePresence('nome', 'create')
            ->notEmptyString('nome')
            ->add('nome', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'Este nome já está em uso',
            ]);

        $validator
            ->scalar('descricao')
            ->allowEmptyString('descricao');

        $validator
            ->integer('role_id')
            ->allowEmptyString('role_id');

        $validator
            ->decimal('preco_mensal')
            ->allowEmptyString('preco_mensal');

        $validator
            ->decimal('preco_anual')
            ->allowEmptyString('preco_anual');

        $validator
            ->integer('dias_trial')
            ->allowEmptyString('dias_trial');

        $validator
            ->boolean('is_ativo')
            ->allowEmptyString('is_ativo');

        $validator
            ->integer('ordem')
            ->allowEmptyString('ordem');

        return $validator;
    }

    // Método para buscar planos com suas roles
    public function findWithRoles(\Cake\ORM\Query $query, array $options = [])
    {
        return $query->contain(['Roles' => function ($q) {
            return $q->select(['id', 'nome', 'nivel', 'descricao', 'is_sistema']);
        }]);
    }

    // Método para buscar planos ativos ordenados
    public function findActiveOrdered(\Cake\ORM\Query $query, array $options = [])
    {
        return $query
            ->where(['is_ativo' => true])
            ->orderBy(['ordem' => 'ASC']);
    }
}