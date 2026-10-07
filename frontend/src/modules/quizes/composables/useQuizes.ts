import { ref } from 'vue'
import { quizService, type QuizInput } from '../services/quizService'
import type { Quiz } from '@/core/types'
import { getApiErrorMessage } from '@/core/services/api'
import { useAlert } from '@/shared/composables/useAlert'
import { useI18n } from 'vue-i18n'
import { usePlan } from '@/shared/composables/usePlan'
import { usePermissionStore } from '@/stores/permissionStore'
import { getApiErrors } from './useQuestoesForm'

/**
 * Erro de requisição de quiz com os erros de validação (422) da API, para a tela marcar os campos.
 */
export class QuizRequestError extends Error {
  constructor(
    message: string,
    public readonly errors: Record<string, Record<string, string>> = {},
  ) {
    super(message)
    this.name = 'QuizRequestError'
  }
}

/**
 * CRUD de quizes do usuário logado. Exibe os alertas de sucesso/erro;
 * as views não devem repeti-los.
 */
export function useQuizes() {
  const quizes = ref<Quiz[]>([])
  const loaded = ref(false)
  const loading = ref(false)
  const { success, error } = useAlert()
  const { t } = useI18n()
  const plan = usePlan()
  const permissionStore = usePermissionStore()

  const canCreateMore = (): boolean => {
    return plan.canCreateMore('quizes', quizes.value.length)
  }

  const errorMessage = (err: unknown, fallback: string): string =>
    getApiErrorMessage(err) || (err instanceof Error && err.message) || fallback

  const loadQuizes = async () => {
    loading.value = true
    try {
      const response = await quizService.getAll()

      if (response.data.success) {
        quizes.value = response.data.data || []
        loaded.value = true
      } else {
        error(response.data.message || t('quizes.errorLoad'))
      }
    } catch (err: unknown) {
      error(errorMessage(err, t('quizes.errorLoad')))
      quizes.value = []
    } finally {
      loading.value = false
    }
  }

  const getQuiz = async (id: number): Promise<Quiz | null> => {
    try {
      const response = await quizService.getById(id)
      if (response.data.success && response.data.data) {
        return response.data.data
      }
      error(response.data.message || t('quizes.errorLoad'))
    } catch (err: unknown) {
      error(errorMessage(err, t('quizes.errorLoad')))
    }
    return null
  }

  const createQuiz = async (data: QuizInput) => {
    if (!permissionStore.canCreate('quizes')) {
      const msg = t('permissions.createDenied', { recurso: t('common.quizes') })
      error(msg)
      throw new Error(msg)
    }

    // O limite do plano depende da quantidade atual de quizes
    if (!loaded.value) {
      await loadQuizes()
    }

    if (!canCreateMore()) {
      const msg = t('plan.limitReached', { recurso: t('common.quizes') })
      error(msg)
      throw new Error(msg)
    }

    loading.value = true
    try {
      const response = await quizService.create(data)

      if (response.data.success) {
        quizes.value.push(response.data.data)
        success(t('quizes.successCreate'))
        return response.data.data
      }
      throw new Error(response.data.message || t('quizes.errorCreate'))
    } catch (err: unknown) {
      const errorMsg = errorMessage(err, t('quizes.errorCreate'))
      error(errorMsg)
      throw new QuizRequestError(errorMsg, getApiErrors(err))
    } finally {
      loading.value = false
    }
  }

  const updateQuiz = async (id: number, data: Partial<QuizInput>) => {
    loading.value = true
    try {
      const response = await quizService.update(id, data)

      if (response.data.success) {
        success(t('quizes.successUpdate'))
        return response.data.data
      }
      throw new Error(response.data.message || t('quizes.errorUpdate'))
    } catch (err: unknown) {
      const errorMsg = errorMessage(err, t('quizes.errorUpdate'))
      error(errorMsg)
      throw new QuizRequestError(errorMsg, getApiErrors(err))
    } finally {
      loading.value = false
    }
  }

  const deleteQuiz = async (id: number) => {
    loading.value = true
    try {
      const response = await quizService.delete(id)

      if (response.data.success) {
        quizes.value = quizes.value.filter((q) => q.id !== id)
        success(t('quizes.successDelete'))
        return true
      }
      throw new Error(response.data.message || t('quizes.errorDelete'))
    } catch (err: unknown) {
      const errorMsg = errorMessage(err, t('quizes.errorDelete'))
      error(errorMsg)
      throw new Error(errorMsg)
    } finally {
      loading.value = false
    }
  }

  return {
    quizes,
    loading,
    loadQuizes,
    getQuiz,
    createQuiz,
    updateQuiz,
    deleteQuiz,
    canCreateMore,
  }
}
