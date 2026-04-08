import { ref } from 'vue'
import { fraseService } from '@/modules/frases/services/fraseService'
import type { Frase } from '@/core/types'
import { useAlert } from '@/shared/composables/useAlert'

export function useFrases() {
  const frases = ref<Frase[]>([])
  const loading = ref(false)
  const { success, error } = useAlert()

  const fetchFrases = async (promptId: number): Promise<Frase[]> => {
    loading.value = true
    try {
      const response = await fraseService.getByPrompt(promptId)
      frases.value = response.data.data || []
      return frases.value
    } catch (err: unknown) {
      const axiosError = err as { response?: { status?: number; data?: { message?: string } } }
      if (axiosError.response?.status === 400) {
        frases.value = []
        return frases.value
      }
      error(axiosError.response?.data?.message || 'Erro ao carregar frases')
      throw err
    } finally {
      loading.value = false
    }
  }

  const createFrase = async (data: Omit<Frase, 'id' | 'criado_em'>): Promise<Frase> => {
    loading.value = true
    try {
      const response = await fraseService.create(data)
      const newFrase = response.data.data
      frases.value.unshift(newFrase)
      success('Frase criada com sucesso!')
      return newFrase
    } catch (err: unknown) {
      const axiosError = err as { response?: { data?: { message?: string } } }
      error(axiosError.response?.data?.message || 'Erro ao criar frase')
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteFrase = async (id: number): Promise<void> => {
    loading.value = true
    try {
      await fraseService.delete(id)
      frases.value = frases.value.filter((f) => f.id !== id)
      success('Frase excluída com sucesso!')
    } catch (err: unknown) {
      const axiosError = err as { response?: { data?: { message?: string } } }
      error(axiosError.response?.data?.message || 'Erro ao excluir frase')
      throw err
    } finally {
      loading.value = false
    }
  }

  return { frases, loading, fetchFrases, createFrase, deleteFrase }
}
