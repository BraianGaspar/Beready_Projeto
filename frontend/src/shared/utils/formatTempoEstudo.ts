import i18n from '@/locales'

// Unidades traduzidas (time.*) de acordo com o idioma ativo
export function formatTempoEstudo(totalSegundos: number): string {
  const { t } = i18n.global

  if (totalSegundos <= 0) {
    return t('time.seconds', { n: 0 })
  }

  if (totalSegundos < 60) {
    return t('time.seconds', { n: totalSegundos })
  }

  const minutos = Math.floor(totalSegundos / 60)

  if (minutos < 60) {
    return t('time.minutes', { n: minutos })
  }

  const horas = Math.floor(minutos / 60)
  const minutosRestantes = minutos % 60

  if (minutosRestantes === 0) {
    return t('time.hours', { n: horas })
  }

  return t('time.hoursMinutes', { h: horas, m: minutosRestantes })
}