<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Assinatura Premium recorrente no Stripe (Checkout mode=subscription):
 * - users.stripe_customer_id: Customer do Stripe reutilizado nos checkouts e no Billing Portal.
 * - assinaturas.stripe_subscription_id: Subscription que renova a assinatura (webhooks invoice.* e
 *   customer.subscription.*). Nula nas assinaturas sem renovação (gratuito, trial, pagamentos únicos antigos).
 * - assinaturas.stripe_status: status da Subscription no Stripe (active, past_due, unpaid...). O acesso
 *   continua sendo decidido por status/data_fim; este campo só informa a situação da cobrança.
 * - assinaturas.cancelar_no_fim_periodo: cancelamento agendado (cancel_at_period_end); o acesso vai até data_fim.
 */
class AssinaturaRecorrenteStripe extends BaseMigration
{
    public function change(): void
    {
        $this->table('users')
            ->addColumn('stripe_customer_id', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addIndex(['stripe_customer_id'], ['unique' => true, 'name' => 'users_stripe_customer_id_unique'])
            ->update();

        $this->table('assinaturas')
            ->addColumn('stripe_subscription_id', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('stripe_status', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('cancelar_no_fim_periodo', 'boolean', [
                'default' => false,
                'null' => false,
            ])
            ->addIndex(['stripe_subscription_id'], ['name' => 'assinaturas_stripe_subscription_id_idx'])
            ->update();
    }
}
