<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Assinatura Entity
 *
 * Os campos do Stripe (stripe_subscription_id, stripe_status, cancelar_no_fim_periodo) só são
 * gravados pelo AssinaturaService (webhooks/cancelamento), nunca por atribuição em massa.
 *
 * @property bool $recorrente assinatura renovada automaticamente pelo Stripe
 */
class Assinatura extends Entity
{
    protected array $_accessible = [
        'usuario_id' => true,
        'plano_id' => true,
        'status' => true,
        'data_inicio' => true,
        'data_fim' => true,
        'data_cancelamento' => true,
        'payment_id' => true,
        'payment_gateway' => true,
        'is_ativo' => true,
        'plano' => true,
    ];

    protected array $_hidden = [
        'stripe_subscription_id',
    ];

    protected array $_virtual = ['recorrente'];

    protected function _getRecorrente(): bool
    {
        return !empty($this->stripe_subscription_id);
    }
}
