import { ref } from 'vue'
import { traducaoService } from '@/modules/traducoes/services/traducaoService'
import type { Traducao } from '@/core/types'
import { useAlert } from '@/shared/composables/useAlert'

export function useTraducoes() {
  const traducoes = ref<Traducao[]>([])
  const loading = ref(false)
  const { success, error } = useAlert()

  const fetchTraducoes = async (promptId: number): Promise<Traducao[]> => {
    loading.value = true
    try {
      const response = await traducaoService.getByPrompt(promptId)
      traducoes.value = response.data.data || []
      return traducoes.value
    } catch (err: unknown) {
      const axiosError = err as { response?: { status?: number; data?: { message?: string } } }
      if (axiosError.response?.status === 400) {
        // Nenhuma tradução encontrada, retorna array vazio
        traducoes.value = []
        return traducoes.value
      }
      error(axiosError.response?.data?.message || 'Erro ao carregar traduções')
      throw err
    } finally {
      loading.value = false
    }
  }

  const createTraducao = async (data: Omit<Traducao, 'id' | 'criado_em'>): Promise<Traducao> => {
    loading.value = true
    try {
      const response = await traducaoService.create(data)
      const newTraducao = response.data.data
      traducoes.value.unshift(newTraducao)
      success('Tradução criada com sucesso!')
      return newTraducao
    } catch (err: unknown) {
      const axiosError = err as { response?: { data?: { message?: string } } }
      error(axiosError.response?.data?.message || 'Erro ao criar tradução')
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteTraducao = async (id: number): Promise<void> => {
    loading.value = true
    try {
      await traducaoService.delete(id)
      traducoes.value = traducoes.value.filter((t) => t.id !== id)
      success('Tradução excluída com sucesso!')
    } catch (err: unknown) {
      const axiosError = err as { response?: { data?: { message?: string } } }
      error(axiosError.response?.data?.message || 'Erro ao excluir tradução')
      throw err
    } finally {
      loading.value = false
    }
  }

  return { traducoes, loading, fetchTraducoes, createTraducao, deleteTraducao }
}
