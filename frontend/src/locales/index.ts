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

// Obtém o idioma salvo no localStorage ou do navegador
const getSavedLocale = (): string => {
  const saved = localStorage.getItem('app_locale')
  if (saved) return saved

  const browserLang = navigator.language?.split('-')[0] || 'pt'
  const supported = [
    'pt',
    'en',
    'es',
    'fr',
    'de',
    'it',
    'ja',
    'ko',
    'ru',
    'nl',
    'sv',
    'pl',
    'tr',
    'ar',
  ]
  return supported.includes(browserLang) ? browserLang : 'pt'
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
export const setLocale = (locale: string): void => {
  i18n.global.locale.value = locale as typeof i18n.global.locale.value
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
