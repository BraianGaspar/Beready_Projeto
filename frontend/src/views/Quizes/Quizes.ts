import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAlert } from '@/composables/useAlert'

export function useQuizes() {
  const router = useRouter()
  const { success, error } = useAlert()
  const quizes = ref<any[]>([])
  const loading = ref(true)
  const creating = ref(false)
  const deleting = ref(false)
  const editing = ref(false)
  const showConfirmModal = ref(false)
  const quizToDeleteId = ref<number | null>(null)
  const quizToDeleteTitle = ref('')

  const loadQuizes = async () => {
    loading.value = true
    try {
      const response = await fetch('http://localhost:8765/quizes', {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        }
      })
      
      const data = await response.json()
      
      if (data.success) {
        quizes.value = data.data
      } else {
        error(data.message || 'Erro ao carregar quizzes')
      }
    } catch (err) {
      console.error('Erro:', err)
      error('Erro de conexão com o servidor')
    } finally {
      loading.value = false
    }
  }

  const viewQuiz = (id: number) => {
    router.push(`/quizes/${id}`)
  }

  const startQuiz = (id: number) => {
    router.push(`/quizes/${id}/play`)
  }

  const addQuiz = () => {
    router.push('/quizes/add')
  }

  const editQuiz = (quiz: any) => {
    router.push(`/quizes/edit/${quiz.id}`)
  }

  const openDeleteModal = (id: number, titulo: string) => {
    quizToDeleteId.value = id
    quizToDeleteTitle.value = titulo
    showConfirmModal.value = true
  }

  const confirmDelete = async () => {
    if (!quizToDeleteId.value) return
    
    deleting.value = true
    try {
      const response = await fetch(`http://localhost:8765/quizes/${quizToDeleteId.value}`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        }
      })
      
      const data = await response.json()
      
      if (data.success) {
        success('Quiz excluído com sucesso!')
        showConfirmModal.value = false
        quizToDeleteId.value = null
        quizToDeleteTitle.value = ''
        loadQuizes()
      } else {
        error(data.message || 'Erro ao excluir quiz')
      }
    } catch (err) {
      console.error('Erro:', err)
      error('Erro de conexão com o servidor')
    } finally {
      deleting.value = false
    }
  }

  const createQuiz = async () => {
    router.push('/quizes/add')
  }

  const getLevelClass = (level: string) => {
    const classes: Record<string, string> = {
      iniciante: 'level-beginner',
      intermediario: 'level-intermediate',
      avancado: 'level-advanced'
    }
    return classes[level] || 'level-beginner'
  }

  const getLevelText = (level: string) => {
    const texts: Record<string, string> = {
      iniciante: 'Iniciante',
      intermediario: 'Intermediário',
      avancado: 'Avançado'
    }
    return texts[level] || level
  }

  const formatDate = (date: string) => {
    if (!date) return 'Data não informada'
    return new Date(date).toLocaleDateString('pt-BR')
  }

  onMounted(() => {
    loadQuizes()
  })

  return {
    quizes,
    loading,
    creating,
    deleting,
    editing,
    showConfirmModal,
    quizToDeleteId,
    quizToDeleteTitle,
    viewQuiz,
    startQuiz,
    addQuiz,
    editQuiz,
    openDeleteModal,
    confirmDelete,
    getLevelClass,
    getLevelText,
    formatDate
  }
}