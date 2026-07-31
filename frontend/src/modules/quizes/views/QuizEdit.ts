import { ref, reactive, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useQuizes } from '../composables/useQuizes'
import { useAlert } from '@/shared/composables/useAlert'

// Tipo para o erro da API
interface ApiError {
  response?: {
    data?: {
      message?: string
    }
  }
  message?: string
}

// Tipo para o usuário do localStorage
interface UserData {
  id: number
  nome?: string
  email?: string
}

export function useQuizEdit() {
  const router = useRouter()
  const route = useRoute()
  const { success, error } = useAlert()
  const { updateQuiz, deleteQuiz, loadQuizes } = useQuizes()

  const loading = ref(false)
  const deleteLoading = ref(false)
  const showDeleteModal = ref(false)

  const form = reactive({
    id: null as number | null,
    titulo: '',
    descricao: '',
    nivel_dificuldade: 'intermediario',
    tempo_limite: null as number | null,
    total_questoes: 0,
    publico: false,
    tipo_criacao: 'manual',
  })

  const errors = reactive({
    titulo: '',
    descricao: '',
  })

  const validateForm = (): boolean => {
    let isValid = true
    if (!form.titulo.trim()) {
      errors.titulo = 'Título é obrigatório'
      isValid = false
    } else {
      errors.titulo = ''
    }
    return isValid
  }

  const loadQuiz = async () => {
    const id = route.params.id
    if (!id) return

    loading.value = true
    try {
      // Buscar quiz por ID (implementar no service se necessário)
      const userData = localStorage.getItem('user')
      if (userData) {
        const user = JSON.parse(userData) as UserData
        await loadQuizes(user.id)
        // Atualizar form com os dados do quiz
      }
    } catch (err: unknown) {
      console.error('Erro ao carregar quiz:', err)
      const apiError = err as ApiError
      error(apiError.message || 'Erro ao carregar quiz')
    } finally {
      loading.value = false
    }
  }

  const handleSubmit = async () => {
    if (!validateForm()) return

    const userData = localStorage.getItem('user')
    if (!userData) {
      error('Usuário não autenticado')
      return
    }

    const user = JSON.parse(userData) as UserData
    loading.value = true

    try {
      const data = {
        usuario_id: user.id,
        titulo: form.titulo,
        descricao: form.descricao,
        nivel_dificuldade: form.nivel_dificuldade,
        tempo_limite: form.tempo_limite ?? undefined,
        total_questoes: 0,
        publico: form.publico,
        tipo_criacao: 'manual' as const,
      }

      if (form.id) {
        await updateQuiz(form.id, data)
        success('Quiz atualizado com sucesso!')
      }
      router.push('/quizes')
    } catch (err: unknown) {
      const apiError = err as ApiError
      error(apiError.message || 'Erro ao salvar quiz')
    } finally {
      loading.value = false
    }
  }

  const handleDelete = () => {
    showDeleteModal.value = true
  }

  const confirmDelete = async () => {
    if (!form.id) return

    deleteLoading.value = true
    try {
      await deleteQuiz(form.id)
      success('Quiz excluído com sucesso!')
      router.push('/quizes')
    } catch (err: unknown) {
      const apiError = err as ApiError
      error(apiError.message || 'Erro ao excluir quiz')
      showDeleteModal.value = false
    } finally {
      deleteLoading.value = false
      showDeleteModal.value = false
    }
  }

  onMounted(() => {
    loadQuiz()
  })

  return {
    form,
    errors,
    loading,
    deleteLoading,
    showDeleteModal,
    handleSubmit,
    handleDelete,
    confirmDelete,
  }
}