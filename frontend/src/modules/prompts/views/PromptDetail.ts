import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { promptService } from '@/modules/prompts/services/promptService'
import { useTraducoes } from '@/modules/traducoes/composables/useTraducoes'
import { useImagens } from '@/modules/imagens/composables/useImagens'
import { useFrases } from '@/modules/frases/composables/useFrases'
import { useI18n } from 'vue-i18n'
import { useAlert } from '@/shared/composables/useAlert'
import { formatDate as formatLocaleDate } from '@/shared/utils/intl'
import { getNivelLabelKey } from '@/shared/utils/nivelDificuldade'
import type { Prompt, Traducao, Imagem, Frase } from '@/core/types'

type DeleteItemType = 'traducao' | 'imagem' | 'frase'

interface DeleteItem {
  type: DeleteItemType
  id: number
}

export function usePromptDetail() {
  const route = useRoute()
  const { error } = useAlert()
  const { t, te } = useI18n()
  const prompt = ref<Prompt | null>(null)
  const activeTab = ref<'traducoes' | 'imagens' | 'frases'>('traducoes')

  const { traducoes, fetchTraducoes, deleteTraducao } = useTraducoes()
  const { imagens, fetchImagens, deleteImagem } = useImagens()

  const loadingTraducoes = ref(false)
  const loadingImagens = ref(false)
  const loadingFrases = ref(false)

  const confirmModalVisible = ref(false)
  const confirmMessage = ref('')
  const itemToDelete = ref<DeleteItem | null>(null)
  const deleting = ref(false)

  const promptId = ref<number>(0)
  const { frases, fetchFrases, deleteFrase } = useFrases()

  const fetchPrompt = async (): Promise<void> => {
    const idParam = route.params.id
    if (idParam) {
      promptId.value = Number(idParam)
      try {
        const response = await promptService.getById(promptId.value)
        if (response.data.data) {
          prompt.value = response.data.data
        }
      } catch (err: unknown) {
        const axiosError = err as { response?: { data?: { message?: string } } }
        error(axiosError.response?.data?.message || t('prompts.errorLoadOne'))
      }
    }
  }

  const loadTraducoes = async (): Promise<void> => {
    loadingTraducoes.value = true
    try {
      await fetchTraducoes(promptId.value)
    } catch {
      // Alerta de erro já exibido pelo composable (lista vazia não é erro)
    } finally {
      loadingTraducoes.value = false
    }
  }

  const loadImagens = async (): Promise<void> => {
    loadingImagens.value = true
    try {
      await fetchImagens(promptId.value)
    } catch {
      // Alerta de erro já exibido pelo composable (lista vazia não é erro)
    } finally {
      loadingImagens.value = false
    }
  }

  const loadFrases = async (): Promise<void> => {
    loadingFrases.value = true
    try {
      await fetchFrases(promptId.value)
    } catch {
      // Alerta de erro já exibido pelo composable (lista vazia não é erro)
    } finally {
      loadingFrases.value = false
    }
  }

  const formatDate = (date?: string): string => formatLocaleDate(date)

  // Rótulo traduzido do tipo da frase (o valor gravado no backend não muda)
  const getTipoLabel = (tipo?: string): string => {
    const key = `frases.tipos.${tipo || 'relacionada'}`
    return te(key) ? t(key) : tipo || ''
  }

  const confirmDeleteTraducao = (traducao: Traducao): void => {
    itemToDelete.value = { type: 'traducao', id: traducao.id }
    confirmMessage.value = t('traducoes.deleteMessage')
    confirmModalVisible.value = true
  }

  const confirmDeleteImagem = (imagem: Imagem): void => {
    itemToDelete.value = { type: 'imagem', id: imagem.id }
    confirmMessage.value = t('imagens.deleteMessage')
    confirmModalVisible.value = true
  }

  const confirmDeleteFrase = (frase: Frase): void => {
    itemToDelete.value = { type: 'frase', id: frase.id }
    confirmMessage.value = t('frases.deleteMessage')
    confirmModalVisible.value = true
  }

  const handleConfirmDelete = async (): Promise<void> => {
    if (!itemToDelete.value) return

    deleting.value = true
    try {
      switch (itemToDelete.value.type) {
        case 'traducao':
          await deleteTraducao(itemToDelete.value.id)
          break
        case 'imagem':
          await deleteImagem(itemToDelete.value.id)
          break
        case 'frase':
          await deleteFrase(itemToDelete.value.id)
          break
      }
    } catch {
      // Alerta de erro já exibido pelo composable
    } finally {
      deleting.value = false
      confirmModalVisible.value = false
      itemToDelete.value = null
    }
  }

  onMounted(async () => {
    await fetchPrompt()
    await loadTraducoes()
    await loadImagens()
    await loadFrases()
  })

  return {
    prompt,
    activeTab,
    traducoes,
    imagens,
    frases,
    loadingTraducoes,
    loadingImagens,
    loadingFrases,
    confirmModalVisible,
    confirmMessage,
    deleting,
    formatDate,
    getTipoLabel,
    getNivelLabelKey,
    confirmDeleteTraducao,
    confirmDeleteImagem,
    confirmDeleteFrase,
    handleConfirmDelete,
  }
}
