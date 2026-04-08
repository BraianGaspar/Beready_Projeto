import { ref, onMounted } from 'vue'
import { quizService } from '../services/quizService'
import { useAlert } from '@/shared/composables/useAlert'

export function useQuizes() {
  const quizes = ref<any[]>([])
  const loading = ref(false)
  const { success, error } = useAlert()

  const loadQuizes = async (usuarioId: number) => {
    loading.value = true
    try {
      const response = await quizService.getByUsuario(usuarioId)
      quizes.value = response.data.data || []
      return quizes.value
    } catch (err: any) {
      console.error('Erro ao carregar quizes:', err)
      error('Erro ao carregar quizes')
      quizes.value = []
    } finally {
      loading.value = false
    }
  }

  const createQuiz = async (data: any) => {
    loading.value = true
    try {
      const response = await quizService.create(data)
      success('Quiz criado com sucesso!')
      return response.data.data
    } catch (err: any) {
      error(err.response?.data?.message || 'Erro ao criar quiz')
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateQuiz = async (id: number, data: any) => {
    loading.value = true
    try {
      const response = await quizService.update(id, data)
      success('Quiz atualizado com sucesso!')
      return response.data.data
    } catch (err: any) {
      error(err.response?.data?.message || 'Erro ao atualizar quiz')
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteQuiz = async (id: number) => {
    loading.value = true
    try {
      await quizService.delete(id)
      success('Quiz excluído com sucesso!')
      return true
    } catch (err: any) {
      error(err.response?.data?.message || 'Erro ao excluir quiz')
      throw err
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
  }
}
