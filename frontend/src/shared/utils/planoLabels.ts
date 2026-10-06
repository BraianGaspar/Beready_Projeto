import i18n from '@/locales'

// Converte uma chave técnica (ex.: "minha_chave") em rótulo legível ("Minha Chave")
const humanize = (key: string): string =>
  key.replace(/_/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase())

const translateOr = (key: string, fallback: string): string =>
  i18n.global.te(key) ? i18n.global.t(key) : fallback

/**
 * Rótulo traduzido de um recurso de plano (valor técnico vindo da API,
 * ex.: "flashcards_ilimitados"). Recursos desconhecidos são humanizados.
 */
export const formatRecursoPlano = (recurso: string): string =>
  translateOr(`planos.recursos.${recurso}`, humanize(recurso))

/**
 * Rótulo traduzido de uma chave de limite de plano (ex.: "flashcards").
 */
export const formatLimitePlano = (key: string): string =>
  translateOr(`planos.limites.${key}`, humanize(key))
