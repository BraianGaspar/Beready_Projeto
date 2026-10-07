import { reactive, ref } from 'vue'
import axios from 'axios'
import { useI18n } from 'vue-i18n'
import { tagService } from '@/modules/tags/services/tagService'
import { useAlert } from '@/shared/composables/useAlert'
import type { Quiz, Tag } from '@/core/types'
import type { NivelDificuldade } from '@/shared/utils/nivelDificuldade'
import { quizService } from '../services/quizService'
import { getApiErrors } from './useQuestoesForm'

// Mesmas regras do backend (QuizGeradorService)
export const GERAR_MIN_FLASHCARDS = 4
export const GERAR_MAX_QUESTOES = 50

/**
 * Formulário do "Gerar quiz dos meus flashcards" (POST /quizes/gerar).
 * A permissão de criar e o limite do plano são verificados por quem abre o formulário.
 */
export function useGerarQuiz() {
  const { t } = useI18n()
  const { success } = useAlert()

  const form = reactive({
    quantidade: 10,
    nivel: '' as NivelDificuldade | '',
    tag_id: '' as number | '',
    titulo: '',
  })
  const tags = ref<Tag[]>([])
  const gerando = ref(false)
  const erro = ref('')
  const erroQuantidade = ref('')

  const carregarTags = async () => {
    try {
      const response = await tagService.getAll()
      tags.value = Array.isArray(response.data?.data) ? response.data.data : []
    } catch {
      tags.value = []
    }
  }

  const reset = () => {
    form.quantidade = 10
    form.nivel = ''
    form.tag_id = ''
    form.titulo = ''
    erro.value = ''
    erroQuantidade.value = ''
  }

  const mensagemDeErro = (err: unknown): string => {
    const errors = getApiErrors(err)
    if (errors.flashcards?.minimo) return t('quizGerar.erroMinimo', { n: GERAR_MIN_FLASHCARDS })
    if (errors.flashcards) return t('quizGerar.erroSemFlashcards')
    if (errors.quantidade) return t('quizGerar.erroQuantidade', { max: GERAR_MAX_QUESTOES })
    if (axios.isAxiosError(err) && err.response?.status === 404) return t('quizGerar.erroTag')
    if (axios.isAxiosError(err) && err.response?.status === 403) return t('plan.limitReached', { recurso: t('common.quizes') })
    return t('quizGerar.erro')
  }

  const gerar = async (): Promise<Quiz | null> => {
    erro.value = ''
    const quantidade = Number(form.quantidade)
    if (!Number.isInteger(quantidade) || quantidade < 1 || quantidade > GERAR_MAX_QUESTOES) {
      erroQuantidade.value = t('quizGerar.erroQuantidade', { max: GERAR_MAX_QUESTOES })
      return null
    }
    erroQuantidade.value = ''

    gerando.value = true
    try {
      const response = await quizService.gerar({
        quantidade,
        nivel: form.nivel || undefined,
        tag_id: form.tag_id === '' ? undefined : Number(form.tag_id),
        titulo: form.titulo.trim() || t('quizGerar.tituloPadrao'),
      })
      success(t('quizGerar.sucesso'))
      return response.data.data
    } catch (err) {
      erro.value = mensagemDeErro(err)
      return null
    } finally {
      gerando.value = false
    }
  }

  return { form, tags, gerando, erro, erroQuantidade, carregarTags, reset, gerar }
}
