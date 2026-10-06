<?php
declare(strict_types=1);

use Migrations\BaseSeed;

/**
 * Roles seed.
 */
class RolesSeed extends BaseSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * https://book.cakephp.org/migrations/4/en/seeding.html
     *
     * @return void
     */
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'nome' => 'admin',
                'descricao' => 'Administrador do sistema',
                'nivel' => 100,
                'is_sistema' => true,
                'is_ativo' => true,
                'created_at' => '2026-07-22 14:55:38.1889',
                'updated_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'id' => 2,
                'nome' => 'premium',
                'descricao' => 'Usuário com assinatura paga',
                'nivel' => 50,
                'is_sistema' => true,
                'is_ativo' => true,
                'created_at' => '2026-07-22 14:55:38.1889',
                'updated_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'id' => 3,
                'nome' => 'free',
                'descricao' => 'Usuário gratuito',
                'nivel' => 0,
                'is_sistema' => true,
                'is_ativo' => true,
                'created_at' => '2026-07-22 14:55:38.1889',
                'updated_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'id' => 4,
                'nome' => 'trial',
                'descricao' => 'Usuário em período de teste',
                'nivel' => 10,
                'is_sistema' => true,
                'is_ativo' => true,
                'created_at' => '2026-07-22 14:55:38.1889',
                'updated_at' => '2026-07-22 14:55:38.1889',
            ],
        ];

        $table = $this->table('roles');
        $table->insert($data)->save();
    }
}
