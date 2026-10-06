<?php
declare(strict_types=1);

use Migrations\BaseSeed;

/**
 * RolePermissoes seed.
 */
class RolePermissoesSeed extends BaseSeed
{
    public function getDependencies(): array
    {
        return ['RolesSeed', 'PermissoesSeed'];
    }

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
                'role_id' => 1,
                'permissao_id' => 6,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'role_id' => 1,
                'permissao_id' => 8,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'role_id' => 1,
                'permissao_id' => 3,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'role_id' => 1,
                'permissao_id' => 14,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'role_id' => 1,
                'permissao_id' => 9,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'role_id' => 1,
                'permissao_id' => 7,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'role_id' => 1,
                'permissao_id' => 16,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'role_id' => 1,
                'permissao_id' => 4,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'role_id' => 1,
                'permissao_id' => 1,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'role_id' => 1,
                'permissao_id' => 11,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'role_id' => 1,
                'permissao_id' => 17,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'role_id' => 1,
                'permissao_id' => 18,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'role_id' => 1,
                'permissao_id' => 12,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'role_id' => 1,
                'permissao_id' => 10,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'role_id' => 1,
                'permissao_id' => 2,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'role_id' => 1,
                'permissao_id' => 19,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'role_id' => 1,
                'permissao_id' => 15,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'role_id' => 1,
                'permissao_id' => 13,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'role_id' => 1,
                'permissao_id' => 5,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'role_id' => 2,
                'permissao_id' => 19,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'role_id' => 2,
                'permissao_id' => 5,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'role_id' => 2,
                'permissao_id' => 13,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'role_id' => 2,
                'permissao_id' => 15,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'role_id' => 2,
                'permissao_id' => 11,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'role_id' => 2,
                'permissao_id' => 7,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'role_id' => 2,
                'permissao_id' => 4,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'role_id' => 2,
                'permissao_id' => 6,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'role_id' => 2,
                'permissao_id' => 8,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'role_id' => 3,
                'permissao_id' => 4,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'role_id' => 3,
                'permissao_id' => 5,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'role_id' => 4,
                'permissao_id' => 2,
                'created_at' => '2026-07-30 17:54:16.577945',
            ],
            [
                'role_id' => 4,
                'permissao_id' => 3,
                'created_at' => '2026-07-30 17:54:16.759646',
            ],
            [
                'role_id' => 4,
                'permissao_id' => 4,
                'created_at' => '2026-07-30 17:54:16.945316',
            ],
            [
                'role_id' => 4,
                'permissao_id' => 5,
                'created_at' => '2026-07-30 17:54:17.125008',
            ],
            [
                'role_id' => 4,
                'permissao_id' => 6,
                'created_at' => '2026-07-30 17:54:17.304844',
            ],
            [
                'role_id' => 4,
                'permissao_id' => 7,
                'created_at' => '2026-07-30 17:54:17.488966',
            ],
            [
                'role_id' => 4,
                'permissao_id' => 8,
                'created_at' => '2026-07-30 17:54:17.670694',
            ],
            [
                'role_id' => 4,
                'permissao_id' => 11,
                'created_at' => '2026-07-30 17:54:17.855347',
            ],
            [
                'role_id' => 4,
                'permissao_id' => 13,
                'created_at' => '2026-07-30 17:54:18.040224',
            ],
            [
                'role_id' => 4,
                'permissao_id' => 15,
                'created_at' => '2026-07-30 17:54:18.224732',
            ],
            [
                'role_id' => 4,
                'permissao_id' => 18,
                'created_at' => '2026-07-30 17:54:18.405827',
            ],
            [
                'role_id' => 4,
                'permissao_id' => 19,
                'created_at' => '2026-07-30 17:54:18.586183',
            ],
            [
                'role_id' => 1,
                'permissao_id' => 23,
                'created_at' => '2026-07-31 13:30:07.08548',
            ],
            [
                'role_id' => 1,
                'permissao_id' => 22,
                'created_at' => '2026-07-31 13:30:07.08548',
            ],
            [
                'role_id' => 1,
                'permissao_id' => 20,
                'created_at' => '2026-07-31 13:30:07.08548',
            ],
            [
                'role_id' => 1,
                'permissao_id' => 21,
                'created_at' => '2026-07-31 13:30:07.08548',
            ],
            [
                'role_id' => 2,
                'permissao_id' => 21,
                'created_at' => '2026-07-31 13:30:14.01058',
            ],
            [
                'role_id' => 2,
                'permissao_id' => 20,
                'created_at' => '2026-07-31 13:30:14.01058',
            ],
            [
                'role_id' => 2,
                'permissao_id' => 22,
                'created_at' => '2026-07-31 13:30:14.01058',
            ],
            [
                'role_id' => 4,
                'permissao_id' => 22,
                'created_at' => '2026-07-31 13:30:19.326328',
            ],
            [
                'role_id' => 4,
                'permissao_id' => 20,
                'created_at' => '2026-07-31 13:30:19.326328',
            ],
            [
                'role_id' => 4,
                'permissao_id' => 21,
                'created_at' => '2026-07-31 13:30:19.326328',
            ],
            [
                'role_id' => 3,
                'permissao_id' => 20,
                'created_at' => '2026-07-31 13:30:24.611296',
            ],
        ];

        // Mesmos pares da migration CorrigePermissoesDosPlanos (bancos já existentes):
        // Gratuito (role 3) cria e edita dentro dos limites do plano; Premium (role 2) exclui.
        $correcoes = [
            3 => [7, 6, 11, 8, 15, 19, 13, 21, 22],
            2 => [2, 3, 18, 23],
        ];
        foreach ($correcoes as $roleId => $permissaoIds) {
            foreach ($permissaoIds as $permissaoId) {
                $data[] = [
                    'role_id' => $roleId,
                    'permissao_id' => $permissaoId,
                    'created_at' => '2026-10-06 00:00:00',
                ];
            }
        }

        $table = $this->table('role_permissoes');
        $table->insert($data)->save();
    }
}
