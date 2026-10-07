<?php
declare(strict_types=1);

use Migrations\BaseMigration;
use Migrations\Db\Literal;

/**
 * Agendamento de revisão (repetição espaçada, SM-2 simplificado) direto em `flashcards`.
 *
 * Colunas na própria tabela, e não numa tabela à parte: cada flashcard tem um único dono e,
 * portanto, um único agendamento (relação 1:1). Assim a listagem dos devidos é um filtro
 * simples e indexado (usuario_id, proxima_revisao), sem join.
 *
 * Flashcards existentes recebem proxima_revisao = agora (ficam "devidos agora").
 */
class RepeticaoEspacadaFlashcards extends BaseMigration
{
    public function up(): void
    {
        $this->table('flashcards')
            ->addColumn('repeticoes', 'integer', [
                'default' => 0,
                'null' => false,
            ])
            ->addColumn('intervalo_dias', 'integer', [
                'default' => 0,
                'null' => false,
            ])
            ->addColumn('fator_ease', 'decimal', [
                'default' => '2.50',
                'precision' => 4,
                'scale' => 2,
                'null' => false,
            ])
            ->addColumn('proxima_revisao', 'timestamp', [
                'default' => Literal::from('now()'),
                'null' => false,
                'precision' => 6,
                'scale' => 6,
            ])
            ->addColumn('ultima_revisao', 'timestamp', [
                'default' => null,
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->addIndex(
                $this->index(['usuario_id', 'proxima_revisao'])
                    ->setName('idx_flashcards_usuario_proxima_revisao')
            )
            ->update();
    }

    public function down(): void
    {
        $this->table('flashcards')
            ->removeIndexByName('idx_flashcards_usuario_proxima_revisao')
            ->removeColumn('repeticoes')
            ->removeColumn('intervalo_dias')
            ->removeColumn('fator_ease')
            ->removeColumn('proxima_revisao')
            ->removeColumn('ultima_revisao')
            ->update();
    }
}
