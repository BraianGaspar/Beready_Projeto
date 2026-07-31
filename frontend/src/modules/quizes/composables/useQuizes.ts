import { ref } from 'vue'
import { quizService } from '../services/quizService'
import type { Quiz } from '@/core/types'
import { useAlert } from '@/shared/composables/useAlert'
import { useI18n } from 'vue-i18n'
import { usePlan } from '@/shared/composables/usePlan'

// Tipo para o erro da API
interface ApiError {
  response?: {
    data?: {
      message?: string
    }
  }
  message?: string
}

export function useQuizes() {
  const quizes = ref<Quiz[]>([])
  const loading = ref(false)
  const { success, error } = useAlert()
  const { t } = useI18n()
  const plan = usePlan()

  const canCreateMore = (): boolean => {
    return plan.canCreateMore('quizes', quizes.value.length)
  }

  const loadQuizes = async (usuarioId: number) => {
    loading.value = true
    try {
      const response = await quizService.getByUsuario(usuarioId)

      if (response.data.success) {
        quizes.value = response.data.data || []
      } else {
        error(response.data.message || t('quizes.errorLoad'))
      }
    } catch (err: unknown) {
      console.error('Erro ao carregar quizes:', err)
      const apiError = err as ApiError
      error(apiError.response?.data?.message || apiError.message || t('quizes.errorLoad'))
      quizes.value = []
    } finally {
      loading.value = false
    }
  }

  const createQuiz = async (data: Omit<Quiz, 'id' | 'criado_em' | 'atualizado_em'>) => {
    if (!canCreateMore()) {
      error(t('plan.limitReached', { recurso: 'quizes' }))
      throw new Error('Limite do plano atingido')
    }

    loading.value = true
    try {
      const response = await quizService.create(data)

      if (response.data.success) {
        success(t('quizes.successCreate'))
        return response.data.data
      } else {
        throw new Error(response.data.message || t('quizes.errorCreate'))
      }
    } catch (err: unknown) {
      console.error('Erro ao criar quiz:', err)
      const apiError = err as ApiError
      const errorMsg = apiError.response?.data?.message || apiError.message || t('quizes.errorCreate')
      error(errorMsg)
      throw new Error(errorMsg)
    } finally {
      loading.value = false
    }
  }

  const updateQuiz = async (
    id: number,
    data: Partial<Omit<Quiz, 'id' | 'criado_em' | 'atualizado_em'>>,
  ) => {
    loading.value = true
    try {
      const response = await quizService.update(id, data)

      if (response.data.success) {
        success(t('quizes.successUpdate'))
        return response.data.data
      } else {
        throw new Error(response.data.message || t('quizes.errorUpdate'))
      }
    } catch (err: unknown) {
      console.error('Erro ao atualizar quiz:', err)
      const apiError = err as ApiError
      const errorMsg = apiError.response?.data?.message || apiError.message || t('quizes.errorUpdate')
      error(errorMsg)
      throw new Error(errorMsg)
    } finally {
      loading.value = false
    }
  }

  const deleteQuiz = async (id: number) => {
    loading.value = true
    try {
      const response = await quizService.delete(id)

      if (response.data.success) {
        success(t('quizes.successDelete'))
        return true
      } else {
        throw new Error(response.data.message || t('quizes.errorDelete'))
      }
    } catch (err: unknown) {
      console.error('Erro ao excluir quiz:', err)
      const apiError = err as ApiError
      const errorMsg = apiError.response?.data?.message || apiError.message || t('quizes.errorDelete')
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
    createQuiz, 
    updateQuiz, 
    deleteQuiz,
    canCreateMore 
  }
}