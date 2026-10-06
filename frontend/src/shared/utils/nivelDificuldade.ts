/**
 * Níveis de dificuldade usados em todo o app (flashcards, quizes, frases,
 * preferências). São os mesmos valores gravados pelo backend (default
 * 'iniciante' nas colunas `nivel_dificuldade`).
 */
export const NIVEIS_DIFICULDADE = ['iniciante', 'intermediario', 'avancado'] as const

export type NivelDificuldade = (typeof NIVEIS_DIFICULDADE)[number]

// Valores antigos gravados por versões anteriores do formulário de flashcards
const LEGADO: Record<string, NivelDificuldade> = {
  facil: 'iniciante',
  medio: 'intermediario',
  dificil: 'avancado',
}

export const normalizeNivel = (nivel?: string | null): NivelDificuldade => {
  const value = String(nivel ?? '').toLowerCase()
  if ((NIVEIS_DIFICULDADE as readonly string[]).includes(value)) {
    return value as NivelDificuldade
  }
  return LEGADO[value] ?? 'iniciante'
}

export type NivelVariant = 'success' | 'warning' | 'danger'

const VARIANTS: Record<NivelDificuldade, NivelVariant> = {
  iniciante: 'success',
  intermediario: 'warning',
  avancado: 'danger',
}

// Variante de BaseBadge do nível. O nível é sempre exibido como texto; a cor apenas reforça.
export const getNivelVariant = (nivel?: string | null): NivelVariant => VARIANTS[normalizeNivel(nivel)]

// Chave i18n do rótulo do nível (common.iniciante | common.intermediario | common.avancado)
export const getNivelLabelKey = (nivel?: string | null): string => `common.${normalizeNivel(nivel)}`
