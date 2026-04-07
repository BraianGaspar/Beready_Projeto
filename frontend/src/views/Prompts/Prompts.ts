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

  const form = ref({
    texto_original: '',
    idioma_original: 'en',
    contexto: 'manual',
    sessao_id: '',
  })

  const fetchPrompts = async () => {
    const userData = localStorage.getItem('user')
    if (!userData) return

    const user = JSON.parse(userData)
    loading.value = true
    try {
      const response = await promptService.getByUsuario(user.id)
      prompts.value = response.data.data
    } catch (err: any) {
      error(err.response?.data?.message || 'Erro ao carregar prompts')
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
      sessao_id: prompt.sessao_id || '',
    }
    modalOpen.value = true
  }

  const closeModal = () => {
    modalOpen.value = false
  }

  const savePrompt = async () => {
    const userData = localStorage.getItem('user')
    if (!userData) return

    const user = JSON.parse(userData)
    saving.value = true

    try {
      if (editingPrompt.value) {
        await promptService.update(editingPrompt.value.id!, form.value)
        success('Prompt atualizado com sucesso!')
      } else {
        await promptService.create({ ...form.value, usuario_id: user.id })
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

  const confirmDelete = async (prompt: Prompt) => {
    if (confirm(`Tem certeza que deseja excluir este prompt?`)) {
      try {
        await promptService.delete(prompt.id!)
        success('Prompt excluído com sucesso!')
        await fetchPrompts()
      } catch (err: any) {
        error(err.response?.data?.message || 'Erro ao excluir prompt')
      }
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
    openModal,
    editPrompt,
    closeModal,
    savePrompt,
    confirmDelete,
    formatDate,
    viewTranslations,
  }
}
