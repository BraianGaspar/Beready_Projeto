<?php
declare(strict_types=1);

use Migrations\BaseSeed;

/**
 * Permissoes seed.
 */
class PermissoesSeed extends BaseSeed
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
                'nome' => 'prompts.manage_all',
                'descricao' => 'Gerenciar todos os prompts',
                'recurso' => 'prompts',
                'acao' => 'manage_all',
                'is_ativo' => true,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'id' => 2,
                'nome' => 'flashcards.delete',
                'descricao' => 'Excluir flashcards',
                'recurso' => 'flashcards',
                'acao' => 'delete',
                'is_ativo' => true,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'id' => 3,
                'nome' => 'quizes.delete',
                'descricao' => 'Excluir quizes',
                'recurso' => 'quizes',
                'acao' => 'delete',
                'is_ativo' => true,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'id' => 4,
                'nome' => 'quizes.view',
                'descricao' => 'Visualizar quizes',
                'recurso' => 'quizes',
                'acao' => 'view',
                'is_ativo' => true,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'id' => 5,
                'nome' => 'flashcards.view',
                'descricao' => 'Visualizar flashcards',
                'recurso' => 'flashcards',
                'acao' => 'view',
                'is_ativo' => true,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'id' => 6,
                'nome' => 'flashcards.edit',
                'descricao' => 'Editar flashcards',
                'recurso' => 'flashcards',
                'acao' => 'edit',
                'is_ativo' => true,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'id' => 7,
                'nome' => 'flashcards.create',
                'descricao' => 'Criar flashcards',
                'recurso' => 'flashcards',
                'acao' => 'create',
                'is_ativo' => true,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'id' => 8,
                'nome' => 'quizes.edit',
                'descricao' => 'Editar quizes',
                'recurso' => 'quizes',
                'acao' => 'edit',
                'is_ativo' => true,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'id' => 9,
                'nome' => 'admin.manage_users',
                'descricao' => 'Gerenciar usuários',
                'recurso' => 'admin',
                'acao' => 'manage_users',
                'is_ativo' => true,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'id' => 10,
                'nome' => 'flashcards.manage_all',
                'descricao' => 'Gerenciar todos os flashcards',
                'recurso' => 'flashcards',
                'acao' => 'manage_all',
                'is_ativo' => true,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'id' => 11,
                'nome' => 'quizes.create',
                'descricao' => 'Criar quizes',
                'recurso' => 'quizes',
                'acao' => 'create',
                'is_ativo' => true,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'id' => 12,
                'nome' => 'quizes.manage_all',
                'descricao' => 'Gerenciar todos os quizes',
                'recurso' => 'quizes',
                'acao' => 'manage_all',
                'is_ativo' => true,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'id' => 13,
                'nome' => 'prompts.edit',
                'descricao' => 'Editar prompts',
                'recurso' => 'prompts',
                'acao' => 'edit',
                'is_ativo' => true,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'id' => 14,
                'nome' => 'admin.manage_roles',
                'descricao' => 'Gerenciar roles e permissões',
                'recurso' => 'admin',
                'acao' => 'manage_roles',
                'is_ativo' => true,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'id' => 15,
                'nome' => 'prompts.view',
                'descricao' => 'Visualizar prompts',
                'recurso' => 'prompts',
                'acao' => 'view',
                'is_ativo' => true,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'id' => 16,
                'nome' => 'admin.access',
                'descricao' => 'Acessar painel administrativo',
                'recurso' => 'admin',
                'acao' => 'access',
                'is_ativo' => true,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'id' => 17,
                'nome' => 'admin.manage_plans',
                'descricao' => 'Gerenciar planos e assinaturas',
                'recurso' => 'admin',
                'acao' => 'manage_plans',
                'is_ativo' => true,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'id' => 18,
                'nome' => 'prompts.delete',
                'descricao' => 'Excluir prompts',
                'recurso' => 'prompts',
                'acao' => 'delete',
                'is_ativo' => true,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'id' => 19,
                'nome' => 'prompts.create',
                'descricao' => 'Criar prompts com IA',
                'recurso' => 'prompts',
                'acao' => 'create',
                'is_ativo' => true,
                'created_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'id' => 20,
                'nome' => 'tags.view',
                'descricao' => 'Visualizar tags',
                'recurso' => 'tags',
                'acao' => 'view',
                'is_ativo' => true,
                'created_at' => '2026-07-31 13:29:45.340919',
            ],
            [
                'id' => 21,
                'nome' => 'tags.create',
                'descricao' => 'Criar tags',
                'recurso' => 'tags',
                'acao' => 'create',
                'is_ativo' => true,
                'created_at' => '2026-07-31 13:29:45.340919',
            ],
            [
                'id' => 22,
                'nome' => 'tags.edit',
                'descricao' => 'Editar tags',
                'recurso' => 'tags',
                'acao' => 'edit',
                'is_ativo' => true,
                'created_at' => '2026-07-31 13:29:45.340919',
            ],
            [
                'id' => 23,
                'nome' => 'tags.delete',
                'descricao' => 'Excluir tags',
                'recurso' => 'tags',
                'acao' => 'delete',
                'is_ativo' => true,
                'created_at' => '2026-07-31 13:29:45.340919',
            ],
        ];

        $table = $this->table('permissoes');
        $table->insert($data)->save();
    }
}
