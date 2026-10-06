import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useImagens } from '@/modules/imagens/composables/useImagens'
import { promptService } from '@/modules/prompts/services/promptService'
import { useI18n } from 'vue-i18n'
import { useAlert } from '@/shared/composables/useAlert'
import { formatDate as formatLocaleDate } from '@/shared/utils/intl'
import type { Imagem } from '@/core/types'

interface ImagemForm {
  url_imagem: string
  prompt_imagem: string
  servico_geracao: string
  qualidade_imagem: string
  dimensoes: string
}

export function useImagensPrompt() {
  const route = useRoute()
  const { error } = useAlert()
  const { t, te } = useI18n()
  const { imagens, loading, fetchImagens, createImagem, updateImagem, deleteImagem } = useImagens()

  const promptId = ref<number>(0)
  const promptTexto = ref<string>('')
  const modalOpen = ref(false)
  const saving = ref(false)
  const editingId = ref<number | null>(null)
  const deleting = ref(false)
  const confirmModalVisible = ref(false)
  const itemToDelete = ref<number | null>(null)

  const form = ref<ImagemForm>({
    url_imagem: '',
    prompt_imagem: '',
    servico_geracao: 'dalle',
    qualidade_imagem: 'media',
    dimensoes: '1024x1024',
  })

  const loadPrompt = async (): Promise<void> => {
    const idParam = route.params.promptId
    if (idParam) {
      promptId.value = Number(idParam)
      try {
        const response = await promptService.getById(promptId.value)
        if (response.data.data) {
          promptTexto.value = response.data.data.texto_original
        }
      } catch (err: unknown) {
        const axiosError = err as { response?: { data?: { message?: string } } }
        error(axiosError.response?.data?.message || t('prompts.errorLoadOne'))
      }
    }
  }

  const loadData = async (): Promise<void> => {
    if (promptId.value) {
      await fetchImagens(promptId.value).catch(() => {
        // Alerta de erro já exibido por useImagens
      })
    }
  }

  const openModal = (): void => {
    editingId.value = null
    form.value = {
      url_imagem: '',
      prompt_imagem: '',
      servico_geracao: 'dalle',
      qualidade_imagem: 'media',
      dimensoes: '1024x1024',
    }
    modalOpen.value = true
  }

  const editImagem = (imagem: Imagem): void => {
    editingId.value = imagem.id
    form.value = {
      url_imagem: imagem.url_imagem,
      prompt_imagem: imagem.prompt_imagem || '',
      servico_geracao: imagem.servico_geracao || 'dalle',
      qualidade_imagem: imagem.qualidade_imagem || 'media',
      dimensoes: imagem.dimensoes || '1024x1024',
    }
    modalOpen.value = true
  }

  const closeModal = (): void => {
    modalOpen.value = false
  }

  const save = async (): Promise<void> => {
    if (!form.value.url_imagem) {
      error(t('imagens.urlRequired'))
      return
    }

    saving.value = true
    try {
      if (editingId.value) {
        await updateImagem(editingId.value, {
          url_imagem: form.value.url_imagem,
          prompt_imagem: form.value.prompt_imagem,
          servico_geracao: form.value.servico_geracao,
          qualidade_imagem: form.value.qualidade_imagem,
          dimensoes: form.value.dimensoes,
        })
      } else {
        await createImagem({
          prompt_id: promptId.value,
          url_imagem: form.value.url_imagem,
          prompt_imagem: form.value.prompt_imagem,
          servico_geracao: form.value.servico_geracao,
          qualidade_imagem: form.value.qualidade_imagem,
          dimensoes: form.value.dimensoes,
        })
      }
      closeModal()
    } catch {
      // Alerta de erro já exibido por useImagens
    } finally {
      saving.value = false
    }
  }

  const confirmDelete = (imagem: Imagem): void => {
    itemToDelete.value = imagem.id
    confirmModalVisible.value = true
  }

  const handleConfirmDelete = async (): Promise<void> => {
    if (!itemToDelete.value) return

    deleting.value = true
    try {
      await deleteImagem(itemToDelete.value)
    } catch {
      // Alerta de erro já exibido por useImagens
    } finally {
      deleting.value = false
      confirmModalVisible.value = false
      itemToDelete.value = null
    }
  }

  const formatDate = (date?: string): string => formatLocaleDate(date)

  // Rótulo traduzido da qualidade (o valor gravado no backend não muda)
  const getQualidadeLabel = (qualidade?: string): string => {
    const key = `imagens.qualidades.${qualidade || 'media'}`
    return te(key) ? t(key) : qualidade || ''
  }

  onMounted(async () => {
    await loadPrompt()
    await loadData()
  })

  return {
    promptId,
    promptTexto,
    loading,
    imagens,
    modalOpen,
    saving,
    form,
    editingId,
    deleting,
    confirmModalVisible,
    formatDate,
    getQualidadeLabel,
    openModal,
    closeModal,
    editImagem,
    save,
    confirmDelete,
    handleConfirmDelete,
  }
}
