import ptBR from '../../src/locales/pt-BR'

/**
 * Textos em pt-BR lidos do próprio arquivo de mensagens do app, para os seletores
 * (getByRole/getByLabel/getByText) acompanharem mudanças de texto sem quebrar.
 *
 * Suporta a interpolação usada no app: `{nome}` e literais `{'@'}`.
 */
export const t = (key: string, params: Record<string, string | number> = {}): string => {
  const value = key.split('.').reduce<unknown>((node, part) => {
    if (node && typeof node === 'object' && part in node) {
      return (node as Record<string, unknown>)[part]
    }
    return undefined
  }, ptBR)

  if (typeof value !== 'string') {
    throw new Error(`Chave i18n inexistente em pt-BR: ${key}`)
  }

  return value.replace(/\{\s*(?:'([^']*)'|(\w+))\s*\}/g, (match, literal: string | undefined, name: string | undefined) => {
    if (literal !== undefined) return literal
    if (name !== undefined && name in params) return String(params[name])
    return match
  })
}

/** Escapa um texto para uso dentro de RegExp. */
export const escapeRegExp = (text: string): string => text.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')

/** RegExp que casa o texto traduzido em qualquer parte (ignora o restante do nome acessível). */
export const tx = (key: string, params: Record<string, string | number> = {}): RegExp =>
  new RegExp(escapeRegExp(t(key, params)))
