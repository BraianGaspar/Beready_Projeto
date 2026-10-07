<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ValidationException;
use App\Model\Entity\QuizQuestao;
use App\Model\Table\QuizQuestoesTable;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;

/**
 * CRUD e validação das questões de um quiz. O chamador valida o dono do quiz antes.
 *
 * Regras:
 *  - 'multipla_escolha': de 2 a 6 alternativas com texto, exatamente 1 correta;
 *  - 'completar': resposta esperada obrigatória (a correção ignora maiúsculas, acentos e espaços extras).
 *
 * `quizes.total_questoes` é sempre a contagem das questões e é atualizado a cada alteração.
 * Sem gabarito (comGabarito = false) as questões não trazem `correta`, `resposta_esperada`
 * nem `explicacao`: é o formato enviado a quem vai jogar.
 */
class QuizQuestaoService
{
    public const MIN_ALTERNATIVAS = 2;
    public const MAX_ALTERNATIVAS = 6;
    public const MAX_QUESTOES = 100;

    private Table $questoes;
    private Table $alternativas;
    private Table $quizes;

    public function __construct()
    {
        $locator = TableRegistry::getTableLocator();
        $this->questoes = $locator->get('QuizQuestoes');
        $this->alternativas = $locator->get('QuizAlternativas');
        $this->quizes = $locator->get('Quizes');
    }

    public function listar(int $quizId, bool $comGabarito): array
    {
        return array_map(
            fn (QuizQuestao $questao) => $this->formatar($questao, $comGabarito),
            $this->buscarEntidades($quizId)
        );
    }

    /**
     * @return \App\Model\Entity\QuizQuestao[]
     */
    public function buscarEntidades(int $quizId): array
    {
        return $this->questoes->find()
            ->contain(['QuizAlternativas'])
            ->where(['QuizQuestoes.quiz_id' => $quizId])
            ->orderBy(['QuizQuestoes.ordem' => 'ASC', 'QuizQuestoes.id' => 'ASC'])
            ->all()
            ->toList();
    }

    /**
     * Questão do quiz informado; de outro quiz responde 404, como se não existisse.
     */
    public function buscarEntidade(int $quizId, int $questaoId): QuizQuestao
    {
        $questao = $this->questoes->find()
            ->contain(['QuizAlternativas'])
            ->where(['QuizQuestoes.id' => $questaoId, 'QuizQuestoes.quiz_id' => $quizId])
            ->first();

        if (!$questao instanceof QuizQuestao) {
            throw new \RuntimeException('Questão não encontrada', 404);
        }

        return $questao;
    }

    public function criar(int $quizId, array $data): array
    {
        $normalizada = $this->normalizar($data);

        $questao = ConnectionManager::get('default')->transactional(function () use ($quizId, $normalizada) {
            if ($this->questoes->find()->where(['quiz_id' => $quizId])->count() >= self::MAX_QUESTOES) {
                throw new ValidationException('Limite de questões do quiz atingido', [
                    'questoes' => ['maximo' => sprintf('Um quiz pode ter no máximo %d questões', self::MAX_QUESTOES)],
                ]);
            }

            $normalizada['ordem'] ??= $this->proximaOrdem($quizId);
            $questao = $this->inserir($quizId, $normalizada);
            $this->sincronizarTotal($quizId);

            return $questao;
        });

        return $this->formatar($this->buscarEntidade($quizId, (int)$questao->id), true);
    }

    /**
     * Campos ausentes mantêm o valor atual; `alternativas`, quando enviadas, substituem as atuais.
     */
    public function atualizar(int $quizId, int $questaoId, array $data): array
    {
        $atual = $this->formatar($this->buscarEntidade($quizId, $questaoId), true);
        $normalizada = $this->normalizar(array_merge($atual, array_intersect_key($data, array_flip([
            'tipo', 'enunciado', 'resposta_esperada', 'explicacao', 'ordem', 'alternativas',
        ]))));

        ConnectionManager::get('default')->transactional(function () use ($quizId, $questaoId, $normalizada) {
            $questao = $this->buscarEntidade($quizId, $questaoId);
            $this->questoes->patchEntity($questao, [
                'tipo' => $normalizada['tipo'],
                'enunciado' => $normalizada['enunciado'],
                'resposta_esperada' => $normalizada['resposta_esperada'],
                'explicacao' => $normalizada['explicacao'],
                'ordem' => $normalizada['ordem'] ?? $questao->ordem,
            ], ['associated' => []]);
            $questao->unset('quiz_alternativas');
            $this->questoes->saveOrFail($questao, ['associated' => false]);

            $this->alternativas->deleteAll(['questao_id' => $questaoId]);
            $this->inserirAlternativas($questaoId, $normalizada['alternativas']);
        });

        return $this->formatar($this->buscarEntidade($quizId, $questaoId), true);
    }

    public function excluir(int $quizId, int $questaoId): void
    {
        $questao = $this->buscarEntidade($quizId, $questaoId);

        ConnectionManager::get('default')->transactional(function () use ($quizId, $questao) {
            $this->questoes->deleteOrFail($questao);
            $this->sincronizarTotal($quizId);
        });
    }

    /**
     * Troca todas as questões do quiz pela lista recebida (ordem = posição na lista).
     * Valida tudo antes de gravar; erros vêm como "questoes.{i}.{campo}".
     */
    public function substituirTodas(int $quizId, mixed $questoes): array
    {
        $normalizadas = $this->validarLista($questoes);

        ConnectionManager::get('default')->transactional(function () use ($quizId, $normalizadas) {
            $this->questoes->deleteAll(['quiz_id' => $quizId]);
            $this->inserirLista($quizId, $normalizadas);
        });

        return $this->listar($quizId, true);
    }

    /**
     * Valida uma lista de questões sem gravar nada.
     *
     * @return array<int, array> questões normalizadas
     */
    public function validarLista(mixed $questoes): array
    {
        if (!is_array($questoes) || !array_is_list($questoes)) {
            throw new ValidationException('Questões inválidas', [
                'questoes' => ['array' => 'Envie as questões como uma lista'],
            ]);
        }

        if (count($questoes) > self::MAX_QUESTOES) {
            throw new ValidationException('Questões inválidas', [
                'questoes' => ['maximo' => sprintf('Um quiz pode ter no máximo %d questões', self::MAX_QUESTOES)],
            ]);
        }

        $normalizadas = [];
        $erros = [];
        foreach ($questoes as $i => $questao) {
            try {
                $normalizadas[] = $this->normalizar(is_array($questao) ? $questao : [], "questoes.{$i}.");
            } catch (ValidationException $e) {
                $erros += $e->getErrors();
            }
        }

        if ($erros) {
            throw new ValidationException('Há questões inválidas', $erros);
        }

        return $normalizadas;
    }

    /**
     * Grava questões já normalizadas (validarLista), na ordem da lista, e atualiza o total.
     */
    public function inserirLista(int $quizId, array $normalizadas): void
    {
        foreach (array_values($normalizadas) as $i => $questao) {
            $questao['ordem'] = $i + 1;
            $this->inserir($quizId, $questao);
        }
        $this->sincronizarTotal($quizId);
    }

    /**
     * Valida e normaliza uma questão. Lança ValidationException com os erros de todos os campos.
     *
     * @return array{tipo: string, enunciado: string, resposta_esperada: ?string, explicacao: ?string, ordem: ?int, alternativas: array<int, array{texto: string, correta: bool}>}
     */
    public function normalizar(array $data, string $prefixo = ''): array
    {
        $erros = [];

        $tipo = $data['tipo'] ?? 'multipla_escolha';
        if (!is_string($tipo) || !in_array($tipo, QuizQuestoesTable::TIPOS, true)) {
            $erros[$prefixo . 'tipo']['inList'] = 'Tipo inválido. Use multipla_escolha ou completar';
            $tipo = 'multipla_escolha';
        }

        $enunciado = $this->texto($data['enunciado'] ?? null);
        if ($enunciado === '') {
            $erros[$prefixo . 'enunciado']['_empty'] = 'O enunciado é obrigatório';
        } elseif (mb_strlen($enunciado) > 2000) {
            $erros[$prefixo . 'enunciado']['maxLength'] = 'O enunciado pode ter no máximo 2000 caracteres';
        }

        $explicacao = $this->texto($data['explicacao'] ?? null);
        if (mb_strlen($explicacao) > 2000) {
            $erros[$prefixo . 'explicacao']['maxLength'] = 'A explicação pode ter no máximo 2000 caracteres';
        }

        $ordem = null;
        if (isset($data['ordem']) && $data['ordem'] !== '') {
            if (!is_numeric($data['ordem']) || (int)$data['ordem'] < 0) {
                $erros[$prefixo . 'ordem']['integer'] = 'A ordem deve ser um número inteiro positivo';
            } else {
                $ordem = (int)$data['ordem'];
            }
        }

        $respostaEsperada = null;
        $alternativas = [];

        if ($tipo === 'completar') {
            $respostaEsperada = $this->texto($data['resposta_esperada'] ?? null);
            if ($respostaEsperada === '') {
                $erros[$prefixo . 'resposta_esperada']['_empty'] = 'Informe a resposta esperada';
            } elseif (mb_strlen($respostaEsperada) > 500) {
                $erros[$prefixo . 'resposta_esperada']['maxLength'] = 'A resposta esperada pode ter no máximo 500 caracteres';
            }
        } else {
            $recebidas = $data['alternativas'] ?? [];
            if (!is_array($recebidas)) {
                $recebidas = [];
            }

            $corretas = 0;
            foreach (array_values($recebidas) as $i => $alternativa) {
                $alternativa = is_array($alternativa) ? $alternativa : [];
                $texto = $this->texto($alternativa['texto'] ?? null);
                $correta = filter_var($alternativa['correta'] ?? false, FILTER_VALIDATE_BOOLEAN);

                if ($texto === '') {
                    $erros[$prefixo . "alternativas.{$i}.texto"]['_empty'] = 'A alternativa precisa de um texto';
                } elseif (mb_strlen($texto) > 2000) {
                    $erros[$prefixo . "alternativas.{$i}.texto"]['maxLength'] = 'A alternativa pode ter no máximo 2000 caracteres';
                }

                $corretas += $correta ? 1 : 0;
                $alternativas[] = ['texto' => $texto, 'correta' => $correta];
            }

            if (count($alternativas) < self::MIN_ALTERNATIVAS) {
                $erros[$prefixo . 'alternativas']['minimo'] = sprintf('A questão precisa de pelo menos %d alternativas', self::MIN_ALTERNATIVAS);
            } elseif (count($alternativas) > self::MAX_ALTERNATIVAS) {
                $erros[$prefixo . 'alternativas']['maximo'] = sprintf('A questão pode ter no máximo %d alternativas', self::MAX_ALTERNATIVAS);
            }

            if (count($alternativas) >= self::MIN_ALTERNATIVAS && $corretas !== 1) {
                $erros[$prefixo . 'alternativas']['umaCorreta'] = 'Marque exatamente uma alternativa correta';
            }
        }

        if ($erros) {
            throw new ValidationException('Questão inválida', $erros);
        }

        return [
            'tipo' => $tipo,
            'enunciado' => $enunciado,
            'resposta_esperada' => $respostaEsperada,
            'explicacao' => $explicacao === '' ? null : $explicacao,
            'ordem' => $ordem,
            'alternativas' => $alternativas,
        ];
    }

    public function formatar(QuizQuestao $questao, bool $comGabarito): array
    {
        $alternativas = array_map(function ($alternativa) use ($comGabarito) {
            $item = [
                'id' => (int)$alternativa->id,
                'texto' => (string)$alternativa->texto,
                'ordem' => (int)$alternativa->ordem,
            ];
            if ($comGabarito) {
                $item['correta'] = (bool)$alternativa->correta;
            }

            return $item;
        }, $questao->quiz_alternativas ?? []);

        $dados = [
            'id' => (int)$questao->id,
            'quiz_id' => (int)$questao->quiz_id,
            'tipo' => (string)$questao->tipo,
            'enunciado' => (string)$questao->enunciado,
            'ordem' => (int)$questao->ordem,
            'alternativas' => $questao->tipo === 'multipla_escolha' ? $alternativas : [],
        ];

        if ($comGabarito) {
            $dados['resposta_esperada'] = $questao->resposta_esperada;
            $dados['explicacao'] = $questao->explicacao;
        }

        return $dados;
    }

    private function inserir(int $quizId, array $normalizada): QuizQuestao
    {
        // quiz_id vem da URL (fora do _accessible da entidade)
        $questao = $this->questoes->newEntity([
            'quiz_id' => $quizId,
            'tipo' => $normalizada['tipo'],
            'enunciado' => $normalizada['enunciado'],
            'resposta_esperada' => $normalizada['resposta_esperada'],
            'explicacao' => $normalizada['explicacao'],
            'ordem' => $normalizada['ordem'] ?? 0,
        ], ['accessibleFields' => ['quiz_id' => true]]);
        $this->questoes->saveOrFail($questao);

        $this->inserirAlternativas((int)$questao->id, $normalizada['alternativas']);

        return $questao;
    }

    private function inserirAlternativas(int $questaoId, array $alternativas): void
    {
        foreach (array_values($alternativas) as $i => $alternativa) {
            // questao_id vem do serviço (fora do _accessible da entidade)
            $entidade = $this->alternativas->newEntity([
                'questao_id' => $questaoId,
                'texto' => $alternativa['texto'],
                'correta' => $alternativa['correta'],
                'ordem' => $i + 1,
            ], ['accessibleFields' => ['questao_id' => true]]);
            $this->alternativas->saveOrFail($entidade);
        }
    }

    private function proximaOrdem(int $quizId): int
    {
        $maior = $this->questoes->find()
            ->select(['maior' => $this->questoes->find()->func()->max('ordem')])
            ->where(['quiz_id' => $quizId])
            ->disableHydration()
            ->first();

        return (int)($maior['maior'] ?? 0) + 1;
    }

    private function sincronizarTotal(int $quizId): void
    {
        $total = $this->questoes->find()->where(['quiz_id' => $quizId])->count();
        $this->quizes->updateAll(['total_questoes' => $total], ['id' => $quizId]);
    }

    private function texto(mixed $valor): string
    {
        return is_scalar($valor) ? trim((string)$valor) : '';
    }
}
