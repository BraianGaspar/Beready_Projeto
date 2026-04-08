import { ref } from 'vue'
import { imagemService } from '@/modules/imagens/services/imagemService'
import type { Imagem } from '@/core/types'
import { useAlert } from '@/shared/composables/useAlert'

export function useImagens() {
  const imagens = ref<Imagem[]>([])
  const loading = ref(false)
  const { success, error } = useAlert()

  const fetchImagens = async (promptId: number): Promise<Imagem[]> => {
    loading.value = true
    try {
      const response = await imagemService.getByPrompt(promptId)
      imagens.value = response.data.data || []
      return imagens.value
    } catch (err: unknown) {
      const axiosError = err as { response?: { status?: number; data?: { message?: string } } }
      if (axiosError.response?.status === 400) {
        imagens.value = []
        return imagens.value
      }
      error(axiosError.response?.data?.message || 'Erro ao carregar imagens')
      throw err
    } finally {
      loading.value = false
    }
  }

  const createImagem = async (data: Omit<Imagem, 'id' | 'criado_em'>): Promise<Imagem> => {
    loading.value = true
    try {
      const response = await imagemService.create(data)
      const newImagem = response.data.data
      imagens.value.unshift(newImagem)
      success('Imagem criada com sucesso!')
      return newImagem
    } catch (err: unknown) {
      const axiosError = err as { response?: { data?: { message?: string } } }
      error(axiosError.response?.data?.message || 'Erro ao criar imagem')
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteImagem = async (id: number): Promise<void> => {
    loading.value = true
    try {
      await imagemService.delete(id)
      imagens.value = imagens.value.filter((i) => i.id !== id)
      success('Imagem excluída com sucesso!')
    } catch (err: unknown) {
      const axiosError = err as { response?: { data?: { message?: string } } }
      error(axiosError.response?.data?.message || 'Erro ao excluir imagem')
      throw err
    } finally {
      loading.value = false
    }
  }

  return { imagens, loading, fetchImagens, createImagem, deleteImagem }
}
