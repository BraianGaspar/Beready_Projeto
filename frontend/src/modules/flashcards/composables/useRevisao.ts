import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { flashcardService } from '../services/flashcardService'
import { formatDate } from '@/shared/utils/intl'

/**
 * Quantidade de flashcards com revisão vencida ("X para revisar hoje").
 * Falha silenciosa: sem o número, as telas só não mostram o atalho.
 */
export function useRevisaoPendentes() {
  const total = ref(0)
  const loaded = ref(false)

  const carregar = async () => {
    try {
      // limite=1: só a contagem interessa aqui
      const response = await flashcardService.getDevidos(1)
      total.value = response.data.data?.total ?? 0
    } catch {
      total.value = 0
    } finally {
      loaded.value = true
    }
  }

  return { total, loaded, carregar }
}

export interface ProximaRevisaoInfo {
  // Revisão vencida: o card entra na fila do "Revisar agora"
  devido: boolean
  texto: string
}

const mesmoDia = (a: Date, b: Date) =>
  a.getFullYear() === b.getFullYear() && a.getMonth() === b.getMonth() && a.getDate() === b.getDate()

/**
 * Texto da próxima revisão de um card ("Para revisar agora", "Próxima revisão: amanhã"...).
 */
export function useProximaRevisao() {
  const { t } = useI18n()

  const proximaRevisao = (value: string | null | undefined, agora: Date = new Date()): ProximaRevisaoInfo => {
    const data = value ? new Date(value) : null

    if (!data || Number.isNaN(data.getTime()) || data.getTime() <= agora.getTime()) {
      return { devido: true, texto: t('revisao.devidoAgora') }
    }

    const amanha = new Date(agora)
    amanha.setDate(agora.getDate() + 1)

    let quando: string
    if (mesmoDia(data, agora)) {
      quando = t('revisao.hoje')
    } else if (mesmoDia(data, amanha)) {
      quando = t('revisao.amanha')
    } else {
      quando = formatDate(data)
    }

    return { devido: false, texto: t('revisao.proximaEm', { quando }) }
  }

  return { proximaRevisao }
}
