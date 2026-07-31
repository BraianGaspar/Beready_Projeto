<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\Query;
use Cake\Validation\Validator;

class RolesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('roles');
        $this->setDisplayField('nome');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created_at' => 'new',
                    'updated_at' => 'always'
                ]
            ]
        ]);

        $this->belongsToMany('Permissoes', [
            'foreignKey' => 'role_id',
            'targetForeignKey' => 'permissao_id',
            'joinTable' => 'role_permissoes',
        ]);

        $this->hasMany('Planos', [
            'foreignKey' => 'role_id',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
        ->notEmptyString('nome', 'O nome é obrigatório')
        ->add('nome', 'unique', [
            'rule' => 'validateUnique',
            'provider' => 'table',
            'message' => 'Já existe uma role com este nome'
        ]);

        return $validator;
    }

    /**
     * Obtém todas as roles com suas permissões
     */
    public function getWithPermissions(): array
    {
        return $this->find()
            ->contain(['Permissoes'])
            ->where(['is_ativo' => true])
            ->order(['nivel' => 'DESC'])
            ->toArray();
    }

    /**
     * Obtém permissões de uma role
     */
    public function getPermissions(int $roleId): array
    {
        $role = $this->get($roleId, ['contain' => ['Permissoes']]);
        return $role->permissoes ?? [];
    }

    /**
     * Verifica se é uma role do sistema
     */
    public function isSistema(int $roleId): bool
    {
        $role = $this->get($roleId);
        return (bool)$role->is_sistema;
    }
}