import { watch } from 'vue'
import { createI18n } from 'vue-i18n'
import ptBR from './pt-BR' // Português
import enUS from './en-US' // InglêS
import esES from './es-ES' // Espanhol
import frFR from './fr-FR' // Francês
import de from './de' // Alemão
import it from './it' // Italiano
import ja from './ja' // Japonês
import ko from './ko' // Coreano
import ru from './ru' // Russo
import nl from './nl' // Holandês
import sv from './sv' // Sueco
import pl from './pl' // Polonês
import tr from './tr' // Turco
import ar from './ar' // Árabe

// Códigos de idioma do app (chaves das mensagens e de `idiomas.*`)
export const SUPPORTED_LOCALES = ['pt', 'en', 'es', 'fr', 'de', 'it', 'ja', 'ko', 'ru', 'nl', 'sv', 'pl', 'tr', 'ar'] as const
export type AppLocale = (typeof SUPPORTED_LOCALES)[number]

/**
 * Converte um valor de idioma (do banco, do navegador ou do localStorage) para o
 * código do app: 'pt-BR' / 'pt_BR' / 'pt' -> 'pt', 'en-US' -> 'en'. Null se não suportado.
 */
export const toAppLocale = (value?: string | null): AppLocale | null => {
  const code = (String(value ?? '').split(/[-_]/)[0] ?? '').toLowerCase()
  return (SUPPORTED_LOCALES as readonly string[]).includes(code) ? (code as AppLocale) : null
}

/**
 * Valor gravado em users.idioma_preferido. Mantém 'pt-BR' (padrão do banco) para o português.
 */
export const toUserLanguage = (locale: AppLocale): string => (locale === 'pt' ? 'pt-BR' : locale)

/**
 * Opções de idioma do usuário (cadastro e perfil): os 14 idiomas do app, com o valor
 * no formato de users.idioma_preferido e o rótulo traduzido (`idiomas.*`).
 */
export const userLanguageOptions = (t: (key: string) => string): { value: string; label: string }[] =>
  SUPPORTED_LOCALES.map((code) => ({ value: toUserLanguage(code), label: t(`idiomas.${code}`) }))

// Rótulo traduzido de um valor de idioma salvo (ex.: 'pt-BR' -> "Português")
export const userLanguageLabel = (t: (key: string) => string, value?: string | null): string | null => {
  const locale = toAppLocale(value)
  return locale ? t(`idiomas.${locale}`) : null
}

// Antes do login: idioma salvo no localStorage ou o do navegador.
// Logado, vale users.idioma_preferido (aplicado pelo store de auth).
const getSavedLocale = (): AppLocale => {
  let saved: string | null = null
  try {
    saved = localStorage.getItem('app_locale')
  } catch {
    // Armazenamento indisponível
  }
  return toAppLocale(saved) ?? toAppLocale(navigator.language) ?? 'pt'
}

const i18n = createI18n({
  legacy: false,
  locale: getSavedLocale(),
  fallbackLocale: 'pt',
  messages: {
    pt: ptBR,
    en: enUS,
    es: esES,
    fr: frFR,
    de: de,
    it: it,
    ja: ja,
    ko: ko,
    ru: ru,
    nl: nl,
    sv: sv,
    pl: pl,
    tr: tr,
    ar: ar,
  },
})

// Idiomas escritos da direita para a esquerda
const RTL_LOCALES = ['ar']

/**
 * Ajusta lang e dir do <html> para o idioma atual (árabe => rtl).
 */
export const applyDocumentLocale = (locale: string): void => {
  document.documentElement.lang = locale
  document.documentElement.dir = RTL_LOCALES.includes(locale) ? 'rtl' : 'ltr'
}

/**
 * Troca o idioma do app, persiste a escolha e ajusta lang/dir do documento.
 */
export const setLocale = (value: string): void => {
  const locale = toAppLocale(value)
  if (!locale) return
  i18n.global.locale.value = locale
  try {
    localStorage.setItem('app_locale', locale)
  } catch {
    // Armazenamento indisponível: a troca vale só para esta sessão
  }
}

// Boot + qualquer troca posterior de idioma (de onde quer que venha)
applyDocumentLocale(i18n.global.locale.value)
watch(
  () => i18n.global.locale.value,
  (locale) => applyDocumentLocale(locale),
)

export default i18n
