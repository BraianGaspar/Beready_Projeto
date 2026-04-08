import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAlert } from '@/shared/composables/useAlert'

export function useQuizEdit() {
  const router = useRouter()
  const route = useRoute()
  const { success, error } = useAlert()
  const loading = ref(false)
  const deleteLoading = ref(false)
  const showDeleteModal = ref(false)
  const quizId = ref<number | null>(null)

  const form = ref({
    titulo: '',
    descricao: '',
    tipo_criacao: 'manual',
    nivel_dificuldade: 'iniciante',
    total_questoes: 0,
    tempo_limite: null,
    publico: false,
  })

  const errors = ref({
    titulo: '',
  })

  const loadQuiz = async () => {
    const id = route.params.id
    if (!id) {
      error('ID do quiz não informado')
      router.push('/quizes')
      return
    }

    quizId.value = Number(id)

    try {
      const response = await fetch(`http://localhost:8765/quizes/${quizId.value}`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
        },
      })

      const data = await response.json()

      if (data.success) {
        form.value = {
          titulo: data.data.titulo || '',
          descricao: data.data.descricao || '',
          tipo_criacao: data.data.tipo_criacao || 'manual',
          nivel_dificuldade: data.data.nivel_dificuldade || 'iniciante',
          total_questoes: data.data.total_questoes || 0,
          tempo_limite: data.data.tempo_limite || null,
          publico: data.data.publico || false,
        }
      } else {
        error(data.message || 'Erro ao carregar quiz')
        router.push('/quizes')
      }
    } catch (err) {
      console.error('Erro:', err)
      error('Erro de conexão com o servidor')
      router.push('/quizes')
    }
  }

  const handleSubmit = async () => {
    if (!form.value.titulo) {
      errors.value.titulo = 'Título é obrigatório'
      return
    }

    loading.value = true

    try {
      const response = await fetch(`http://localhost:8765/quizes/${quizId.value}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
        },
        body: JSON.stringify(form.value),
      })

      const data = await response.json()

      if (data.success) {
        success('Quiz atualizado com sucesso!')
        setTimeout(() => router.push('/quizes'), 1500)
      } else {
        error(data.message || 'Erro ao atualizar quiz')
      }
    } catch (err) {
      console.error('Erro:', err)
      error('Erro de conexão com o servidor')
    } finally {
      loading.value = false
    }
  }

  const handleDelete = () => {
    showDeleteModal.value = true
  }

  const confirmDelete = async () => {
    deleteLoading.value = true

    try {
      const response = await fetch(`http://localhost:8765/quizes/${quizId.value}`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
        },
      })

      const data = await response.json()

      if (data.success) {
        success('Quiz excluído com sucesso!')
        setTimeout(() => router.push('/quizes'), 1500)
      } else {
        error(data.message || 'Erro ao excluir quiz')
      }
    } catch (err) {
      console.error('Erro:', err)
      error('Erro de conexão com o servidor')
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
