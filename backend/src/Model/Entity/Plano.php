<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Plano extends Entity
{
    protected array $_virtual = ['recursos_list', 'limites_array'];
    
    protected array $_accessible = [
        'nome' => true,
        'descricao' => true,
        'role_id' => true,
        'preco_mensal' => true,
        'preco_anual' => true,
        'dias_trial' => true,
        'recursos' => true,
        'limites' => true,
        'is_ativo' => true,
        'ordem' => true,
        'created_at' => true,
        'updated_at' => true,
        'role' => true,
    ];

    protected function _getRecursosList(): array
    {
        if (is_string($this->recursos)) {
            return json_decode($this->recursos, true) ?: [];
        }
        return $this->recursos ?: [];
    }

    protected function _getLimitesArray(): array
    {
        if (is_string($this->limites)) {
            return json_decode($this->limites, true) ?: [];
        }
        return $this->limites ?: [];
    }
}