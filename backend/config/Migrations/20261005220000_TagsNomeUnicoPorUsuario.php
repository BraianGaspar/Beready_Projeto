<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * O nome da tag deixa de ser único na tabela inteira e passa a ser único por dono.
 * Antes um usuário não conseguia criar uma tag com o mesmo nome da tag de outro usuário
 * (e o erro revelava que o nome existia).
 */
class TagsNomeUnicoPorUsuario extends BaseMigration
{
    public function up(): void
    {
        $this->execute('ALTER TABLE tags DROP CONSTRAINT IF EXISTS tags_nome_key');
        $this->execute('DROP INDEX IF EXISTS tags_nome_key');
        $this->execute('CREATE UNIQUE INDEX tags_criado_por_nome_key ON tags (criado_por, nome) WHERE criado_por IS NOT NULL');
        $this->execute('CREATE UNIQUE INDEX tags_sem_dono_nome_key ON tags (nome) WHERE criado_por IS NULL');
    }

    public function down(): void
    {
        $this->execute('DROP INDEX IF EXISTS tags_criado_por_nome_key');
        $this->execute('DROP INDEX IF EXISTS tags_sem_dono_nome_key');
        $this->execute('ALTER TABLE tags ADD CONSTRAINT tags_nome_key UNIQUE (nome)');
    }
}
