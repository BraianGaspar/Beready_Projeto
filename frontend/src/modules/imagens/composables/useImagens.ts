import { ref } from 'vue'
import axios from 'axios'
import { imagemService } from '../services/imagemService'
import type { Imagem } from '@/core/types'
import { getApiErrorMessage } from '@/core/services/api'
import { useAlert } from '@/shared/composables/useAlert'
import { useI18n } from 'vue-i18n'

type ImagemInput = Omit<Imagem, 'id' | 'criado_em'>

/**
 * CRUD de imagens geradas. Este composable já exibe os alertas de
 * sucesso/erro; as views não devem repeti-los (apenas tratar o fluxo).
 */
export function useImagens() {
  const imagens = ref<Imagem[]>([])
  const loading = ref(false)
  const { success, error } = useAlert()
  const { t } = useI18n()

  const fail = (err: unknown, fallback: string): never => {
    error(getApiErrorMessage(err) || (err instanceof Error && err.message) || fallback)
    throw err
  }

  const fetchImagens = async (promptId: number): Promise<Imagem[]> => {
    loading.value = true
    try {
      const response = await imagemService.getByPrompt(promptId)
      imagens.value = response.data || []
      return imagens.value
    } catch (err) {
      imagens.value = []
      // 400 = prompt sem imagens (não é erro para o usuário)
      if (axios.isAxiosError(err) && err.response?.status === 400) return imagens.value
      return fail(err, t('imagens.errorLoad'))
    } finally {
      loading.value = false
    }
  }

  const createImagem = async (data: ImagemInput): Promise<Imagem> => {
    loading.value = true
    try {
      const response = await imagemService.create(data)
      if (!response.success) throw new Error(response.message)
      imagens.value.unshift(response.data)
      success(t('imagens.successCreate'))
      return response.data
    } catch (err) {
      return fail(err, t('imagens.errorCreate'))
    } finally {
      loading.value = false
    }
  }

  const updateImagem = async (id: number, data: Partial<ImagemInput>): Promise<Imagem> => {
    loading.value = true
    try {
      const response = await imagemService.update(id, data)
      if (!response.success) throw new Error(response.message)
      const index = imagens.value.findIndex((i) => i.id === id)
      if (index !== -1) imagens.value[index] = response.data
      success(t('imagens.successUpdate'))
      return response.data
    } catch (err) {
      return fail(err, t('imagens.errorUpdate'))
    } finally {
      loading.value = false
    }
  }

  const deleteImagem = async (id: number): Promise<void> => {
    loading.value = true
    try {
      const response = await imagemService.delete(id)
      if (!response.success) throw new Error(response.message)
      imagens.value = imagens.value.filter((i) => i.id !== id)
      success(t('imagens.successDelete'))
    } catch (err) {
      fail(err, t('imagens.errorDelete'))
    } finally {
      loading.value = false
    }
  }

  return { imagens, loading, fetchImagens, createImagem, updateImagem, deleteImagem }
}
