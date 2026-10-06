import i18n from '@/locales'

// Idioma do app (vue-i18n) -> tag BCP 47 usada pela API Intl
const INTL_LOCALES: Record<string, string> = {
  pt: 'pt-BR',
  en: 'en-US',
  es: 'es-ES',
  fr: 'fr-FR',
  de: 'de-DE',
  it: 'it-IT',
  ja: 'ja-JP',
  ko: 'ko-KR',
  ru: 'ru-RU',
  nl: 'nl-NL',
  sv: 'sv-SE',
  pl: 'pl-PL',
  tr: 'tr-TR',
  ar: 'ar',
}

/**
 * Locale Intl correspondente ao idioma ativo do i18n.
 * Lê o ref do locale, então é reativo quando usado em templates/computeds.
 */
export const getIntlLocale = (): string => {
  const locale = String(i18n.global.locale.value)
  return INTL_LOCALES[locale] ?? locale
}

type DateInput = string | number | Date | null | undefined

const toDate = (value: DateInput): Date | null => {
  if (value === null || value === undefined || value === '') return null
  const date = value instanceof Date ? value : new Date(value)
  return Number.isNaN(date.getTime()) ? null : date
}

// Data no formato curto do idioma ativo (ex.: 05/10/2026, 10/5/2026, 2026/10/05)
export const formatDate = (value: DateInput, options?: Intl.DateTimeFormatOptions): string => {
  const date = toDate(value)
  return date ? date.toLocaleDateString(getIntlLocale(), options) : ''
}

// Data e hora no formato do idioma ativo
export const formatDateTime = (value: DateInput, options?: Intl.DateTimeFormatOptions): string => {
  const date = toDate(value)
  return date ? date.toLocaleString(getIntlLocale(), options) : ''
}

/**
 * Valor monetário no formato do idioma ativo. A moeda padrão é BRL,
 * que é a moeda efetivamente cobrada no Stripe.
 */
export const formatCurrency = (
  value: number | string | null | undefined,
  currency = 'BRL',
): string => {
  const amount = Number(value ?? 0)
  return new Intl.NumberFormat(getIntlLocale(), { style: 'currency', currency }).format(
    Number.isFinite(amount) ? amount : 0,
  )
}
