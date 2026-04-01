<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\RulesChecker;

class UsersTable extends Table
{
    /**
     * Initialize method
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('users');
        $this->setDisplayField('nome');
        $this->setPrimaryKey('id');

        // Remove o Timestamp behavior pois seu banco já tem DEFAULT CURRENT_TIMESTAMP
    }

    /**
     * Regras de validação padrão - SUPER SIMPLIFICADA
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->notEmptyString('nome', 'O nome é obrigatório.')
            ->maxLength('nome', 100, 'O nome deve ter no máximo 100 caracteres.');

        $validator
            ->notEmptyString('email', 'O e-mail é obrigatório.')
            ->email('email', false, 'Digite um e-mail válido.');

        $validator
            ->notEmptyString('senha', 'A senha é obrigatória.', 'create')
            ->minLength('senha', 6, 'A senha deve ter pelo menos 6 caracteres.');

        $validator
            ->add('confirmar_senha', 'custom', [
                'rule' => function ($value, $context) {
                    return $value === ($context['data']['senha'] ?? '');
                },
                'message' => 'As senhas não coincidem.'
            ])
            ->notEmptyString('confirmar_senha', 'Confirme sua senha.', 'create');

        return $validator;
    }

    /**
     * Regras de integridade da aplicação
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['email']), [
            'errorField' => 'email', 
            'message' => 'Este e-mail já está cadastrado.'
        ]);

        return $rules;
    }
}