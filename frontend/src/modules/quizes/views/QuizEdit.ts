import { ref, reactive, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useQuizes } from '../composables/useQuizes'
import { normalizeNivel, type NivelDificuldade } from '@/shared/utils/nivelDificuldade'

export function useQuizEdit() {
  const router = useRouter()
  const route = useRoute()
  const { t } = useI18n()
  // useQuizes já exibe os alertas de sucesso/erro
  const { getQuiz, updateQuiz, deleteQuiz } = useQuizes()

  const loading = ref(false)
  const deleteLoading = ref(false)
  const showDeleteModal = ref(false)

  const form = reactive({
    id: null as number | null,
    titulo: '',
    descricao: '',
    nivel_dificuldade: 'iniciante' as NivelDificuldade,
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
    if (!form.titulo.trim()) {
      errors.titulo = t('quizes.tituloRequired')
      return false
    }
    errors.titulo = ''
    return true
  }

  const loadQuiz = async () => {
    const id = Number(route.params.id)
    if (!id) {
      router.push('/quizes')
      return
    }

    loading.value = true
    try {
      const quiz = await getQuiz(id)
      if (!quiz) {
        router.push('/quizes')
        return
      }
      form.id = quiz.id
      form.titulo = quiz.titulo ?? ''
      form.descricao = quiz.descricao ?? ''
      form.nivel_dificuldade = normalizeNivel(quiz.nivel_dificuldade)
      form.tempo_limite = quiz.tempo_limite ?? null
      form.total_questoes = quiz.total_questoes ?? 0
      form.publico = !!quiz.publico
      form.tipo_criacao = quiz.tipo_criacao || 'manual'
    } finally {
      loading.value = false
    }
  }

  const handleSubmit = async () => {
    if (!form.id || !validateForm()) return

    loading.value = true
    try {
      await updateQuiz(form.id, {
        titulo: form.titulo,
        descricao: form.descricao,
        nivel_dificuldade: form.nivel_dificuldade,
        tempo_limite: form.tempo_limite ?? undefined,
        total_questoes: form.total_questoes,
        publico: form.publico,
        tipo_criacao: form.tipo_criacao,
      })
      router.push('/quizes')
    } catch {
      // Alerta de erro já exibido por useQuizes
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
      router.push('/quizes')
    } catch {
      // Alerta de erro já exibido por useQuizes
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
