<?php

declare(strict_types=1);

namespace App\Services;

use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;

class AssinaturaService
{
    public const CICLOS = ['mensal', 'anual'];

    private $assinaturasTable;
    private $planosTable;

    public function __construct()
    {
        $this->assinaturasTable = TableRegistry::getTableLocator()->get('Assinaturas');
        $this->planosTable = TableRegistry::getTableLocator()->get('Planos');
    }

    /**
     * Retorna a assinatura ativa do usuário. Se ela já passou da data_fim, é marcada como expirada.
     */
    public function getAtiva(int $usuarioId): ?EntityInterface
    {
        $assinatura = $this->assinaturasTable->find()
            ->contain(['Planos'])
            ->where([
                'Assinaturas.usuario_id' => $usuarioId,
                'Assinaturas.status' => 'active',
            ])
            ->orderBy(['Assinaturas.id' => 'DESC'])
            ->first();

        if ($assinatura && $assinatura->data_fim && $assinatura->data_fim->isPast()) {
            $assinatura->status = 'expired';
            $assinatura->is_ativo = false;
            $this->assinaturasTable->saveOrFail($assinatura);
            return null;
        }

        return $assinatura;
    }

    /**
     * Retorna a assinatura ativa; se não houver, coloca o usuário no plano gratuito.
     */
    public function getAtivaOuGratuita(int $usuarioId): ?EntityInterface
    {
        return $this->getAtiva($usuarioId) ?? $this->ativarPlanoGratuito($usuarioId);
    }

    public function getPreco(EntityInterface $plano, string $ciclo): float
    {
        return (float)($ciclo === 'anual' ? $plano->preco_anual : $plano->preco_mensal);
    }

    public function isGratuito(EntityInterface $plano): bool
    {
        return (float)$plano->preco_mensal <= 0 && (float)$plano->preco_anual <= 0 && (int)$plano->dias_trial === 0;
    }

    /**
     * Trial só pode ser usado uma vez por usuário.
     */
    public function jaUsouTrial(int $usuarioId, EntityInterface $plano): bool
    {
        if ((int)$plano->dias_trial === 0) {
            return false;
        }

        return $this->assinaturasTable->exists([
            'usuario_id' => $usuarioId,
            'plano_id' => $plano->id,
        ]);
    }

    public function pagamentoJaProcessado(string $paymentId): bool
    {
        return $this->assinaturasTable->exists(['payment_id' => $paymentId]);
    }

    /**
     * Ativa o plano para o usuário, encerrando qualquer assinatura ativa anterior.
     */
    public function ativar(int $usuarioId, EntityInterface $plano, string $ciclo, ?string $paymentId = null): EntityInterface
    {
        return $this->assinaturasTable->getConnection()->transactional(function () use ($usuarioId, $plano, $ciclo, $paymentId) {
            $this->encerrarAtivas($usuarioId);

            $assinatura = $this->assinaturasTable->newEntity([
                'usuario_id' => $usuarioId,
                'plano_id' => $plano->id,
                'status' => 'active',
                'is_ativo' => true,
                'data_inicio' => date('Y-m-d H:i:s'),
                'data_fim' => $this->calcularDataFim($plano, $ciclo),
                'payment_id' => $paymentId,
                'payment_gateway' => $paymentId ? 'stripe' : null,
            ]);

            $this->assinaturasTable->saveOrFail($assinatura);
            $assinatura->plano = $plano;

            return $assinatura;
        });
    }

    /**
     * Atribui o plano gratuito, caso o usuário ainda não tenha nenhuma assinatura ativa.
     */
    public function ativarPlanoGratuito(int $usuarioId): ?EntityInterface
    {
        $atual = $this->getAtiva($usuarioId);
        if ($atual) {
            return $atual;
        }

        $planoGratuito = $this->planosTable->find()
            ->where(['LOWER(nome)' => 'gratuito'])
            ->first();

        if (!$planoGratuito) {
            error_log("Plano 'Gratuito' não encontrado; usuário {$usuarioId} ficou sem assinatura.");
            return null;
        }

        return $this->ativar($usuarioId, $planoGratuito, 'mensal');
    }

    /**
     * Cancela a assinatura ativa e devolve o usuário ao plano gratuito.
     *
     * @throws \DomainException quando não há assinatura cancelável
     */
    public function cancelar(int $usuarioId): EntityInterface
    {
        $atual = $this->getAtiva($usuarioId);

        if (!$atual || $this->isGratuito($atual->plano)) {
            throw new \DomainException('Nenhuma assinatura paga ativa para cancelar');
        }

        return $this->assinaturasTable->getConnection()->transactional(function () use ($usuarioId) {
            $this->encerrarAtivas($usuarioId);

            return $this->ativarPlanoGratuito($usuarioId);
        });
    }

    private function encerrarAtivas(int $usuarioId): void
    {
        $this->assinaturasTable->updateAll(
            [
                'status' => 'canceled',
                'is_ativo' => false,
                'data_cancelamento' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            ['usuario_id' => $usuarioId, 'status' => 'active']
        );
    }

    private function calcularDataFim(EntityInterface $plano, string $ciclo): ?string
    {
        if ($this->getPreco($plano, $ciclo) > 0) {
            return date('Y-m-d H:i:s', strtotime($ciclo === 'anual' ? '+1 year' : '+1 month'));
        }

        // Plano sem custo: expira no fim do trial, ou nunca (gratuito)
        $diasTrial = (int)$plano->dias_trial;

        return $diasTrial > 0 ? date('Y-m-d H:i:s', strtotime("+{$diasTrial} days")) : null;
    }
}
