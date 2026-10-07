<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * User Entity
 *
 * senha_hash, uuid, role, status, token, campos de reset e stripe_customer_id não são atribuíveis em massa:
 * quem precisa deles (registro, troca de senha, admin) define explicitamente.
 */
class User extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     */
    protected array $_accessible = [
        'nome' => true,
        'email' => true,
        'telefone' => true,
        'nivel_ingles' => true,
        'idioma_preferido' => true,
        'objetivos_aprendizado' => true,
        'foto_perfil' => true,
        'ultimo_login' => true,
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     */
    protected array $_hidden = [
        'senha_hash',
        'token',
        'token_expires',
        'reset_token',
        'reset_token_expires',
        'stripe_customer_id',
    ];
}
