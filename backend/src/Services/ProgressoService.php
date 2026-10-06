<?php

declare(strict_types=1);

namespace App\Services;

use Cake\ORM\Table;
use Cake\ORM\TableRegistry;

class ProgressoService
{
    private Table $table;
    private Table $respostasTable;
    private Table $flashcardsTable;
    private Table $quizesTable;

    public function __construct()
    {
        $locator = TableRegistry::getTableLocator();
        $this->table = $locator->get('ProgressoUsuario');
        $this->respostasTable = $locator->get('RespostasUsuario');
        $this->flashcardsTable = $locator->get('Flashcards');
        $this->quizesTable = $locator->get('Quizes');
    }

    /**
     * Progresso salvo + taxa de acerto e progresso geral calculados.
     */
    public function getResumo(int $usuarioId): array
    {
        $progresso = $this->find($usuarioId);

        $data = $progresso
            ? $progresso->toArray()
            : [
                'usuario_id' => $usuarioId,
                'vocabulario_aprendido' => 0,
                'flashcards_concluidos' => 0,
                'quizes_concluidos' => 0,
                'tempo_total_estudo' => 0,
                'sequencia_atual' => 0,
                'maior_sequencia' => 0,
                'ultima_atividade' => null,
                'progresso_nivel' => null,
            ];

        $totalRespostas = $this->respostasTable->find()
            ->where(['usuario_id' => $usuarioId])
            ->count();

        $respostasCorretas = $this->respostasTable->find()
            ->where(['usuario_id' => $usuarioId, 'correto' => true])
            ->count();

        $data['taxa_acerto'] = $totalRespostas > 0
            ? (int)round(($respostasCorretas / $totalRespostas) * 100)
            : 0;

        $totalFlashcards = $this->flashcardsTable->find()
            ->where(['usuario_id' => $usuarioId])
            ->count();

        $totalQuizes = $this->quizesTable->find()
            ->where(['usuario_id' => $usuarioId])
            ->count();

        $percFlashcards = $totalFlashcards > 0
            ? ($data['flashcards_concluidos'] / $totalFlashcards)
            : 0;

        $percQuizes = $totalQuizes > 0
            ? ($data['quizes_concluidos'] / $totalQuizes)
            : 0;

        $progressoCalculado = (int)round((($percFlashcards + $percQuizes) / 2) * 100);
        $data['progresso_geral'] = min(100, $progressoCalculado);

        return $data;
    }

    public function save(int $usuarioId, array $data): array
    {
        $data['usuario_id'] = $usuarioId;

        $existing = $this->find($usuarioId);
        $entity = $existing
            ? $this->table->patchEntity($existing, $data)
            : $this->table->newEntity($data);

        $this->table->saveOrFail($entity);

        return $entity->toArray();
    }

    public function incrementarFlashcards(int $usuarioId, int $quantidade): array
    {
        if ($quantidade <= 0) {
            throw new \InvalidArgumentException('Dados inválidos para incrementar progresso');
        }

        $this->ensureExists($usuarioId);

        $this->table->updateAll(
            ['flashcards_concluidos = flashcards_concluidos + ' . $quantidade],
            ['usuario_id' => $usuarioId]
        );

        $this->atualizarSequencia($usuarioId);

        return $this->find($usuarioId)->toArray();
    }

    public function incrementarTempo(int $usuarioId, int $segundos): array
    {
        if ($segundos <= 0) {
            throw new \InvalidArgumentException('Dados inválidos para incrementar tempo de estudo');
        }

        $this->ensureExists($usuarioId);

        $this->table->updateAll(
            ['tempo_total_estudo = tempo_total_estudo + ' . $segundos],
            ['usuario_id' => $usuarioId]
        );

        return $this->find($usuarioId)->toArray();
    }

    private function find(int $usuarioId)
    {
        return $this->table->find()
            ->where(['usuario_id' => $usuarioId])
            ->first();
    }

    private function ensureExists(int $usuarioId): void
    {
        if (!$this->find($usuarioId)) {
            $this->table->saveOrFail($this->table->newEntity(['usuario_id' => $usuarioId]));
        }
    }

    /**
     * Dias seguidos com atividade: mesmo dia mantém, dia seguinte soma 1, intervalo maior reinicia.
     */
    private function atualizarSequencia(int $usuarioId): void
    {
        $progresso = $this->find($usuarioId);

        if (!$progresso) {
            return;
        }

        $hoje = new \DateTime('today');
        $ultimaAtividade = $progresso->ultima_atividade
            ? new \DateTime($progresso->ultima_atividade->format('Y-m-d'))
            : null;

        if ($ultimaAtividade === null) {
            $novaSequencia = 1;
        } else {
            $diffDays = (int)$hoje->diff($ultimaAtividade)->format('%a');

            if ($diffDays === 0) {
                return;
            } elseif ($diffDays === 1) {
                $novaSequencia = ($progresso->sequencia_atual ?? 0) + 1;
            } else {
                $novaSequencia = 1;
            }
        }

        $maiorSequencia = max($progresso->maior_sequencia ?? 0, $novaSequencia);

        $this->table->updateAll(
            [
                'sequencia_atual' => $novaSequencia,
                'maior_sequencia' => $maiorSequencia,
                'ultima_atividade' => $hoje->format('Y-m-d H:i:s'),
            ],
            ['usuario_id' => $usuarioId]
        );
    }
}
