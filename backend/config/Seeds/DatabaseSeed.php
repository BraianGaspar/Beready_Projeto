<?php
declare(strict_types=1);

use Migrations\BaseSeed;

/**
 * Dados de referência para um banco novo: roles, permissões e planos.
 *
 * Uso: bin/cake migrations migrate && bin/cake migrations seed
 */
class DatabaseSeed extends BaseSeed
{
    // Tabelas com id serial (role_permissoes usa chave composta)
    private const TABELAS = ['roles', 'permissoes', 'planos'];

    public function getDependencies(): array
    {
        return ['RolesSeed', 'PermissoesSeed', 'RolePermissoesSeed', 'PlanosSeed'];
    }

    public function run(): void
    {
        // Os seeds inserem IDs fixos; avança as sequences para os próximos inserts não colidirem
        foreach (self::TABELAS as $tabela) {
            $this->execute(sprintf(
                "SELECT setval(pg_get_serial_sequence('%1\$s', 'id'), COALESCE((SELECT MAX(id) FROM %1\$s), 1))",
                $tabela
            ));
        }
    }
}
