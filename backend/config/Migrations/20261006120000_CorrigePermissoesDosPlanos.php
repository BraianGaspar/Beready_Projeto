<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Corrige as permissões das roles dos planos:
 * - Gratuito (role "free") só podia visualizar, embora o plano tenha limites de
 *   50 flashcards, 10 quizzes e 5 prompts. Passa a criar e editar (os limites
 *   continuam valendo) e a ver prompts.
 * - Premium não podia excluir o que cria (o Trial podia). Passa a excluir.
 *
 * Em banco novo as roles ainda não existem quando esta migration roda; os mesmos
 * pares estão no RolePermissoesSeed.
 */
class CorrigePermissoesDosPlanos extends BaseMigration
{
    private const PERMISSOES = [
        'free' => [
            'flashcards.create', 'flashcards.edit',
            'quizes.create', 'quizes.edit',
            'prompts.view', 'prompts.create', 'prompts.edit',
            'tags.create', 'tags.edit',
        ],
        'premium' => ['flashcards.delete', 'quizes.delete', 'prompts.delete', 'tags.delete'],
    ];

    public function up(): void
    {
        foreach (self::PERMISSOES as $role => $permissoes) {
            $this->execute(sprintf(
                "INSERT INTO role_permissoes (role_id, permissao_id, created_at)
                 SELECT r.id, p.id, NOW()
                 FROM roles r JOIN permissoes p ON p.nome IN (%s)
                 WHERE r.nome = '%s'
                   AND NOT EXISTS (
                       SELECT 1 FROM role_permissoes rp WHERE rp.role_id = r.id AND rp.permissao_id = p.id
                   )",
                $this->lista($permissoes),
                $role
            ));
        }
    }

    public function down(): void
    {
        foreach (self::PERMISSOES as $role => $permissoes) {
            $this->execute(sprintf(
                "DELETE FROM role_permissoes rp
                 USING roles r, permissoes p
                 WHERE rp.role_id = r.id AND rp.permissao_id = p.id
                   AND r.nome = '%s' AND p.nome IN (%s)",
                $role,
                $this->lista($permissoes)
            ));
        }
    }

    private function lista(array $nomes): string
    {
        return implode(', ', array_map(fn (string $nome) => "'" . $nome . "'", $nomes));
    }
}
