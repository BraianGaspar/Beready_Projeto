import type { Flashcard, Preferencia, Progresso, Quiz, QuizQuestao, Tag, User } from '../../src/core/types'

/**
 * Fixtures no formato devolvido pela API (CakePHP). Cada função devolve um objeto novo,
 * então os testes podem alterar à vontade.
 */

export const isoDaysFromNow = (days: number): string => new Date(Date.now() + days * 86_400_000).toISOString()

export const makeUser = (overrides: Partial<User> = {}): User => ({
  id: 1,
  uuid: '3f1c2b9e-1111-4a2b-9c3d-000000000001',
  nome: 'Maria Teste',
  email: 'maria@beready.test',
  role: 'user',
  telefone: '(11) 98765-4321',
  nivel_ingles: 'intermediario',
  idioma_preferido: 'pt-BR',
  objetivos_aprendizado: 'Viajar',
  status: 'ativo',
  foto_perfil: null,
  criado_em: '2026-01-10T12:00:00+00:00',
  atualizado_em: '2026-01-10T12:00:00+00:00',
  ultimo_login: '2026-10-01T12:00:00+00:00',
  ...overrides,
})

export const makeAdmin = (overrides: Partial<User> = {}): User =>
  makeUser({ id: 99, nome: 'Ana Admin', email: 'admin@beready.test', role: 'admin', ...overrides })

const RECURSOS = ['flashcards', 'quizes', 'prompts', 'tags'] as const
const ACOES = ['view', 'create', 'edit', 'delete'] as const

/** Permissões de um usuário comum (GET /user/permissions devolve a lista de nomes). */
export const userPermissions = (): string[] => RECURSOS.flatMap((recurso) => ACOES.map((acao) => `${recurso}.${acao}`))

// Planos e assinatura: mesmos campos de permissionStore.ts (Postgres devolve numeric como string)
export interface PlanoFixture {
  id: number
  nome: string
  descricao: string
  role_id: number | null
  preco_mensal: number | string
  preco_anual: number | string
  dias_trial: number
  recursos: string[]
  limites: Record<string, number>
  is_ativo: boolean
  ordem: number
}

export interface AssinaturaFixture {
  id: number
  usuario_id: number
  plano_id: number
  status: 'pending' | 'active' | 'canceled' | 'expired' | 'trial'
  data_inicio: string
  data_fim: string | null
  data_cancelamento: string | null
  recorrente?: boolean
  stripe_status?: string | null
  cancelar_no_fim_periodo?: boolean
  plano?: PlanoFixture
}

export const PLANO_GRATUITO_ID = 1
export const PLANO_TRIAL_ID = 2
export const PLANO_PREMIUM_ID = 3

export const makePlanos = (): PlanoFixture[] => [
  {
    id: PLANO_GRATUITO_ID,
    nome: 'Gratuito',
    descricao: 'Para começar',
    role_id: null,
    preco_mensal: '0.00',
    preco_anual: '0.00',
    dias_trial: 0,
    recursos: ['flashcards_basico', 'quizes_basico'],
    limites: { flashcards: 50, quizes: 10, prompts: 10 },
    is_ativo: true,
    ordem: 1,
  },
  {
    id: PLANO_TRIAL_ID,
    nome: 'Trial',
    descricao: 'Experimente o Premium',
    role_id: null,
    preco_mensal: '0.00',
    preco_anual: '0.00',
    dias_trial: 7,
    recursos: ['flashcards_ilimitados', 'quizes_ilimitados'],
    limites: { flashcards: 200, quizes: 50, prompts: 50 },
    is_ativo: true,
    ordem: 2,
  },
  {
    id: PLANO_PREMIUM_ID,
    nome: 'Premium',
    descricao: 'Tudo liberado',
    role_id: null,
    preco_mensal: '29.90',
    preco_anual: '299.00',
    dias_trial: 0,
    recursos: ['flashcards_ilimitados', 'quizes_ilimitados', 'ia_prompts'],
    limites: { flashcards: 999999, quizes: 999999, prompts: 999999 },
    is_ativo: true,
    ordem: 3,
  },
]

const planoById = (id: number): PlanoFixture => {
  const plano = makePlanos().find((p) => p.id === id)
  if (!plano) throw new Error(`Plano ${id} não existe nas fixtures`)
  return plano
}

/** Assinatura gratuita (sem vencimento). */
export const makeAssinaturaGratuita = (overrides: Partial<AssinaturaFixture> = {}): AssinaturaFixture => ({
  id: 10,
  usuario_id: 1,
  plano_id: PLANO_GRATUITO_ID,
  status: 'active',
  data_inicio: '2026-01-10T12:00:00+00:00',
  data_fim: null,
  data_cancelamento: null,
  recorrente: false,
  stripe_status: null,
  cancelar_no_fim_periodo: false,
  plano: planoById(PLANO_GRATUITO_ID),
  ...overrides,
})

/** Premium recorrente (Stripe Subscription ativa, renova em ~1 mês). */
export const makeAssinaturaPremiumRecorrente = (overrides: Partial<AssinaturaFixture> = {}): AssinaturaFixture => ({
  id: 11,
  usuario_id: 1,
  plano_id: PLANO_PREMIUM_ID,
  status: 'active',
  data_inicio: '2026-10-07T12:00:00+00:00',
  data_fim: '2026-11-07T12:00:00+00:00',
  data_cancelamento: null,
  recorrente: true,
  stripe_status: 'active',
  cancelar_no_fim_periodo: false,
  plano: planoById(PLANO_PREMIUM_ID),
  ...overrides,
})

export const makePreferencias = (overrides: Partial<Preferencia> = {}): Preferencia => ({
  id: 1,
  usuario_id: 1,
  tema: 'claro',
  modo_daltonico: false,
  notificacoes_ativas: true,
  som_ativo: true,
  traducao_automatica: true,
  preferencia_dificuldade: 'intermediario',
  meta_diaria_minutos: 45,
  ...overrides,
})

export const makeProgresso = (overrides: Partial<Progresso> = {}): Progresso => ({
  id: 1,
  usuario_id: 1,
  vocabulario_aprendido: 12,
  flashcards_concluidos: 8,
  quizes_concluidos: 2,
  tempo_total_estudo: 3600,
  sequencia_atual: 4,
  maior_sequencia: 6,
  taxa_acerto: 75,
  progresso_geral: 40,
  ...overrides,
})

export const makeFlashcard = (id: number, overrides: Partial<Flashcard> = {}): Flashcard => ({
  id,
  usuario_id: 1,
  frente: `Pergunta ${id}`,
  verso: `Resposta ${id}`,
  nivel_dificuldade: 'iniciante',
  ultima_revisao: null,
  proxima_revisao: isoDaysFromNow(3),
  intervalo_dias: 3,
  fator_ease: 2.5,
  repeticoes: 1,
  criado_em: '2026-09-01T12:00:00+00:00',
  atualizado_em: '2026-09-01T12:00:00+00:00',
  ...overrides,
})

/** Três flashcards, sendo `vencidos` deles com revisão já vencida (entram na fila). */
export const makeFlashcards = (vencidos = 0): Flashcard[] =>
  [
    makeFlashcard(1, { frente: 'apple', verso: 'maçã' }),
    makeFlashcard(2, { frente: 'book', verso: 'livro' }),
    makeFlashcard(3, { frente: 'house', verso: 'casa' }),
  ].map((card, i) => (i < vencidos ? { ...card, proxima_revisao: isoDaysFromNow(-1) } : card))

export const makeQuiz = (id: number, overrides: Partial<Quiz> = {}): Quiz => ({
  id,
  usuario_id: 1,
  titulo: `Quiz ${id}`,
  descricao: 'Vocabulário básico',
  tipo_criacao: 'manual',
  nivel_dificuldade: 'iniciante',
  total_questoes: 0,
  tempo_limite: null,
  publico: false,
  criado_em: '2026-09-01T12:00:00+00:00',
  atualizado_em: '2026-09-01T12:00:00+00:00',
  ...overrides,
})

/** Questões COM gabarito (como no editor do dono); o mock remove o gabarito no GET /quizes/{id}. */
export const makeQuestoes = (quizId: number): QuizQuestao[] => [
  {
    id: 101,
    quiz_id: quizId,
    tipo: 'multipla_escolha',
    enunciado: 'Como se diz "maçã" em inglês?',
    ordem: 1,
    resposta_esperada: null,
    explicacao: 'Apple é maçã.',
    alternativas: [
      { id: 1001, texto: 'apple', ordem: 1, correta: true },
      { id: 1002, texto: 'grape', ordem: 2, correta: false },
      { id: 1003, texto: 'pear', ordem: 3, correta: false },
    ],
  },
  {
    id: 102,
    quiz_id: quizId,
    tipo: 'completar',
    enunciado: 'The book is ___ the table.',
    ordem: 2,
    resposta_esperada: 'on',
    explicacao: null,
    alternativas: [],
  },
]

export const makeTags = (): Tag[] => [
  { id: 1, criado_por: 1, nome: 'Viagem', cor: '#4f46e5', descricao: '', tag_sistema: false },
]
