<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Ramsey\Uuid\Uuid;
use Cake\Datasource\EntityInterface;
use Cake\Http\Session;

class UsersTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('users');
        $this->setDisplayField('nome');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'criado_em' => 'new',
                    'atualizado_em' => 'always'
                ]
            ]
        ]);
        
        // Relacionamento com Roles
        $this->belongsTo('Roles', [
            'foreignKey' => 'role_id',
            'joinType' => 'LEFT'
        ]);
        
        // Relacionamento com Permissões via Roles
        $this->belongsToMany('Permissions', [
            'through' => 'RolePermissions',
            'foreignKey' => 'role_id',
            'targetForeignKey' => 'permission_id',
            'joinType' => 'LEFT'
        ]);
    }

    public function beforeSave($event, $entity, $options)
    {
        if ($entity->isNew() && !$entity->uuid) {
            $entity->uuid = Uuid::uuid4()->toString();
        }
        return true;
    }

    /** MÉTODO QUE O SOCIAL AUTH USA PARA CRIAR/BUSCAR USUÁRIO */
    public function getUser(EntityInterface $profile, Session $session)
    {
        // Verifica se o email veio do Google
        if (!$profile->email) {
            throw new \RuntimeException('Não foi possível obter o email do perfil social.');
        }

        // Tenta encontrar usuário pelo email
        $user = $this->find()
            ->where(['email' => $profile->email])
            ->first();

        // Se não existe, cria um novo
        if (!$user) {
            
            // Pega o nome do perfil do Google
            $name = $profile->name ?? 'Usuário';
            
            // Pega a foto do perfil (se disponível)
            $fotoPerfil = $profile->picture ?? null;
            
            // Buscar role padrão 'user'
            $rolesTable = \Cake\ORM\TableRegistry::getTableLocator()->get('Roles');
            $defaultRole = $rolesTable->find()->where(['nome' => 'user'])->first();
            
            $user = $this->newEntity([
                'email' => $profile->email,
                'nome' => $name,
                'uuid' => Uuid::uuid4()->toString(),
                'status' => 'ativo',
                'role' => 'user',
                'role_id' => $defaultRole ? $defaultRole->id : null,
                'nivel_ingles' => 'iniciante',
                'idioma_preferido' => 'pt-BR',
                'foto_perfil' => $fotoPerfil,
            ]);
            $this->save($user);
        } else {
            
            // Atualiza nome e foto se estiverem vazios
            $updateData = [];
            if (empty($user->nome) || $user->nome === 'Usuário') {
                $updateData['nome'] = $profile->name ?? $user->nome;
            }
            if (empty($user->foto_perfil) && !empty($profile->picture)) {
                $updateData['foto_perfil'] = $profile->picture;
            }
            
            if (!empty($updateData)) {
                $user = $this->patchEntity($user, $updateData);
                $this->save($user);
                error_log("Usuário atualizado: " . print_r($updateData, true));
            }
        }

        // SALVA O USUÁRIO NA SESSÃO DO CAKEPHP
        $userArray = $user->toArray();
        unset($userArray['senha_hash']);
        $session->write('Auth', $userArray);
        
        return $user;
    }

    /**
     * Verifica se o usuário tem uma permissão específica
     */
    public function hasPermission(int $userId, string $permission): bool
    {
        // Buscar usuário com roles e permissões
        $user = $this->find()
            ->contain(['Roles', 'Permissions'])
            ->where(['Users.id' => $userId])
            ->first();
        
        if (!$user) {
            return false;
        }
        
        // Admin tem todas as permissões
        if ($user->role === 'admin') {
            return true;
        }
        
        // Verificar permissões da role
        if ($user->role && $user->role->permissions) {
            foreach ($user->role->permissions as $perm) {
                if ($perm->nome === $permission) {
                    return true;
                }
            }
        }
        
        // Verificar permissões diretas do usuário (se houver)
        if ($user->permissions) {
            foreach ($user->permissions as $perm) {
                if ($perm->nome === $permission) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Verifica se o usuário tem uma role específica
     */
    public function hasRole(int $userId, string $roleName): bool
    {
        $user = $this->get($userId);
        return $user->role === $roleName;
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('nome')
            ->maxLength('nome', 100)
            ->allowEmptyString('nome');

        $validator
            ->email('email')
            ->allowEmptyString('email');

        $validator
            ->scalar('senha_hash')
            ->maxLength('senha_hash', 255)
            ->allowEmptyString('senha_hash');

        $validator
            ->scalar('telefone')
            ->maxLength('telefone', 20)
            ->allowEmptyString('telefone');

        $validator
            ->scalar('nivel_ingles')
            ->maxLength('nivel_ingles', 20)
            ->allowEmptyString('nivel_ingles');

        $validator
            ->scalar('idioma_preferido')
            ->maxLength('idioma_preferido', 10)
            ->allowEmptyString('idioma_preferido');

        $validator
            ->scalar('status')
            ->maxLength('status', 20)
            ->allowEmptyString('status');

        return $validator;
    }
}
