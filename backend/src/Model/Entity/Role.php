<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Role extends Entity
{
    protected array $_virtual = ['permissoes_count'];
    
    protected array $_accessible = [
        'nome' => true,
        'descricao' => true,
        'nivel' => true,
        'is_sistema' => true,
        'is_ativo' => true,
        'created_at' => true,
        'updated_at' => true,
        'permissoes' => true,
        'planos' => true,
    ];

    protected function _getPermissoesCount(): int
    {
        return isset($this->permissoes) ? count($this->permissoes) : 0;
    }
}