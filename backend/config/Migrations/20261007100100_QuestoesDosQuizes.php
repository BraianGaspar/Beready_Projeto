<?php
declare(strict_types=1);

use Migrations\BaseMigration;
use Migrations\Db\Literal;

/**
 * Questões reais dos quizzes.
 *
 * - quiz_questoes: uma linha por questão (tipo 'multipla_escolha' ou 'completar'). A resposta
 *   esperada do tipo "completar" fica na própria questão.
 * - quiz_alternativas: tabela própria (e não jsonb) porque cada alternativa precisa de um id
 *   estável — o jogador responde enviando o id, e o servidor esconde o campo `correta` até a
 *   correção. Com jsonb o id seria um índice de array, frágil a reordenações.
 *
 * Ambas com FK em cascata: excluir o quiz exclui as questões, que excluem as alternativas.
 * `quizes.total_questoes` passa a ser mantido pelo servidor (contagem das questões); como ainda
 * não existe nenhuma questão, os valores digitados à mão até agora são zerados.
 */
class QuestoesDosQuizes extends BaseMigration
{
    public function up(): void
    {
        $this->table('quiz_questoes')
            ->addColumn('quiz_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('tipo', 'string', [
                'default' => 'multipla_escolha',
                'limit' => 20,
                'null' => false,
            ])
            ->addColumn('enunciado', 'text', [
                'null' => false,
            ])
            ->addColumn('resposta_esperada', 'text', [
                'default' => null,
                'null' => true,
            ])
            ->addColumn('explicacao', 'text', [
                'default' => null,
                'null' => true,
            ])
            ->addColumn('ordem', 'integer', [
                'default' => 0,
                'null' => false,
            ])
            ->addColumn('criado_em', 'timestamp', [
                'default' => Literal::from('now()'),
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->addColumn('atualizado_em', 'timestamp', [
                'default' => Literal::from('now()'),
                'null' => true,
                'precision' => 6,
                'scale' => 6,
            ])
            ->addIndex(
                $this->index(['quiz_id', 'ordem'])
                    ->setName('idx_quiz_questoes_quiz_ordem')
            )
            ->addForeignKey(
                $this->foreignKey('quiz_id')
                    ->setReferencedTable('quizes')
                    ->setReferencedColumns('id')
                    ->setOnDelete('CASCADE')
                    ->setOnUpdate('NO_ACTION')
                    ->setName('quiz_questoes_quiz_id_fkey')
            )
            ->create();

        $this->table('quiz_alternativas')
            ->addColumn('questao_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('texto', 'text', [
                'null' => false,
            ])
            ->addColumn('correta', 'boolean', [
                'default' => false,
                'null' => false,
            ])
            ->addColumn('ordem', 'integer', [
                'default' => 0,
                'null' => false,
            ])
            ->addIndex(
                $this->index(['questao_id', 'ordem'])
                    ->setName('idx_quiz_alternativas_questao_ordem')
            )
            ->addForeignKey(
                $this->foreignKey('questao_id')
                    ->setReferencedTable('quiz_questoes')
                    ->setReferencedColumns('id')
                    ->setOnDelete('CASCADE')
                    ->setOnUpdate('NO_ACTION')
                    ->setName('quiz_alternativas_questao_id_fkey')
            )
            ->create();

        $this->execute('UPDATE quizes SET total_questoes = 0');
    }

    public function down(): void
    {
        $this->table('quiz_alternativas')->drop()->save();
        $this->table('quiz_questoes')->drop()->save();
    }
}
