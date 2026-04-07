import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAlert } from '@/composables/useAlert'
import { promptService, type Prompt } from '@/services/promptService'

export function usePrompts() {
  const router = useRouter()
  const { success, error } = useAlert()
  const prompts = ref<Prompt[]>([])
  const loading = ref(false)
  const saving = ref(false)
  const modalOpen = ref(false)
  const editingPrompt = ref<Prompt | null>(null)
  
  // 🔥 Variáveis para o ConfirmModal
  const confirmModalVisible = ref(false)
  const promptToDelete = ref<Prompt | null>(null)
  const deleting = ref(false)

  const form = ref({
    texto_original: '',
    idioma_original: 'en',
    contexto: 'manual',
    sessao_id: ''
  })

  const getCurrentUserId = (): number | null => {
    const userData = localStorage.getItem('user')
    if (!userData) return null
    try {
      const user = JSON.parse(userData)
      return user.id
    } catch {
      return null
    }
  }

  const fetchPrompts = async () => {
    const userId = getCurrentUserId()
    if (!userId) {
      prompts.value = []
      return
    }
    
    loading.value = true
    try {
      const response = await promptService.getByUsuario(userId)
      prompts.value = response.data.data || []
    } catch (err: any) {
      if (err.response?.status === 400) {
        prompts.value = []
      } else {
        error(err.response?.data?.message || 'Erro ao carregar prompts')
      }
    } finally {
      loading.value = false
    }
  }

  const openModal = () => {
    editingPrompt.value = null
    form.value = { texto_original: '', idioma_original: 'en', contexto: 'manual', sessao_id: '' }
    modalOpen.value = true
  }

  const editPrompt = (prompt: Prompt) => {
    editingPrompt.value = prompt
    form.value = {
      texto_original: prompt.texto_original,
      idioma_original: prompt.idioma_original || 'en',
      contexto: prompt.contexto || 'manual',
      sessao_id: prompt.sessao_id || ''
    }
    modalOpen.value = true
  }

  const closeModal = () => {
    modalOpen.value = false
  }

  const savePrompt = async () => {
    const userId = getCurrentUserId()
    if (!userId) {
      error('Usuário não autenticado')
      return
    }
    
    saving.value = true
    
    try {
      if (editingPrompt.value) {
        await promptService.update(editingPrompt.value.id!, form.value)
        success('Prompt atualizado com sucesso!')
      } else {
        await promptService.create({ ...form.value, usuario_id: userId })
        success('Prompt criado com sucesso!')
      }
      await fetchPrompts()
      closeModal()
    } catch (err: any) {
      error(err.response?.data?.message || 'Erro ao salvar prompt')
    } finally {
      saving.value = false
    }
  }

  // 🔥 Função que abre o modal de confirmação
  const confirmDelete = (prompt: Prompt) => {
    promptToDelete.value = prompt
    confirmModalVisible.value = true
  }

  // 🔥 Função chamada quando o usuário confirma a exclusão
  const handleConfirmDelete = async () => {
    if (!promptToDelete.value) return
    
    deleting.value = true
    try {
      await promptService.delete(promptToDelete.value.id!)
      success('Prompt excluído com sucesso!')
      await fetchPrompts()
    } catch (err: any) {
      error(err.response?.data?.message || 'Erro ao excluir prompt')
    } finally {
      deleting.value = false
      confirmModalVisible.value = false
      promptToDelete.value = null
    }
  }

  const formatDate = (date: string) => {
    if (!date) return ''
    return new Date(date).toLocaleDateString('pt-BR')
  }

  const viewTranslations = (promptId: number) => {
    router.push(`/prompts/${promptId}/traducoes`)
  }

  onMounted(() => {
    fetchPrompts()
  })

  return {
    prompts,
    loading,
    modalOpen,
    saving,
    form,
    editingPrompt,
    confirmModalVisible,
    promptToDelete,
    deleting,
    openModal,
    editPrompt,
    closeModal,
    savePrompt,
    confirmDelete,
    handleConfirmDelete,
    formatDate,
    viewTranslations
  }
}