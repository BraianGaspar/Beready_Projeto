<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class UsuarioRolesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('usuario_roles');
        $this->setPrimaryKey('id');

        $this->belongsTo('Roles', [
            'foreignKey' => 'role_id',
        ]);
    }
}
