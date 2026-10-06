<?php
declare(strict_types=1);

use Migrations\BaseSeed;

/**
 * Planos seed.
 */
class PlanosSeed extends BaseSeed
{
    public function getDependencies(): array
    {
        return ['RolesSeed'];
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
                'id' => 2,
                'nome' => 'Gratuito',
                'descricao' => 'Plano básico para começar',
                'role_id' => 3,
                'preco_mensal' => '0.00',
                'preco_anual' => '0.00',
                'dias_trial' => 0,
                'recursos' => '["flashcards_basico", "quizes_basico"]',
                'limites' => '{"quizes": 10, "prompts": 5, "flashcards": 50}',
                'is_ativo' => true,
                'ordem' => 0,
                'created_at' => '2026-07-22 14:55:38.1889',
                'updated_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'id' => 3,
                'nome' => 'Premium',
                'descricao' => 'Acesso completo',
                'role_id' => 2,
                'preco_mensal' => '29.90',
                'preco_anual' => '299.90',
                'dias_trial' => 0,
                'recursos' => '["flashcards_ilimitados", "quizes_ilimitados", "ia_prompts", "exportacao"]',
                'limites' => '{"quizes": 99999, "prompts": 999, "flashcards": 99999}',
                'is_ativo' => true,
                'ordem' => 2,
                'created_at' => '2026-07-22 14:55:38.1889',
                'updated_at' => '2026-07-22 14:55:38.1889',
            ],
            [
                'id' => 1,
                'nome' => 'Trial',
                'descricao' => 'Período de teste',
                'role_id' => 4,
                'preco_mensal' => '0.00',
                'preco_anual' => '0.00',
                'dias_trial' => 7,
                'recursos' => '["flashcards_ilimitados", "quizes_ilimitados"]',
                'limites' => '{"quizes": 999, "prompts": 20, "flashcards": 999}',
                'is_ativo' => true,
                'ordem' => 1,
                'created_at' => '2026-07-22 14:55:38.1889',
                'updated_at' => '2026-07-31 14:33:19.776206',
            ],
        ];

        $table = $this->table('planos');
        $table->insert($data)->save();
    }
}
