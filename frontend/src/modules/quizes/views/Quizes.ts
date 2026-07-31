import { ref, reactive, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useQuizes } from '../composables/useQuizes'
import type { Quiz, User } from '@/core/types'
import { useI18n } from 'vue-i18n'
import { usePermissionStore } from '@/stores/permissionStore'

interface FormData {
  titulo: string
  descricao: string
  nivel_dificuldade: string
  tempo_limite: number | undefined
  total_questoes: number
  publico: boolean
  tipo_criacao: string
}

export function useQuizesView() {
  const router = useRouter()
  const { t } = useI18n()
  const permissionStore = usePermissionStore()
  const { quizes, loading, loadQuizes, createQuiz, updateQuiz, deleteQuiz, canCreateMore } = useQuizes()

  const showModal = ref(false)
  const showDeleteModal = ref(false)
  const isEditing = ref(false)
  const editingId = ref<number | null>(null)
  const deletingQuiz = ref<Quiz | null>(null)
  const submitting = ref(false)
  const deleting = ref(false)

  const form = reactive<FormData>({
    titulo: '',
    descricao: '',
    nivel_dificuldade: 'intermediario',
    tempo_limite: undefined,
    total_questoes: 0,
    publico: false,
    tipo_criacao: 'manual',
  })

  // Permissões
  const canView = computed(() => permissionStore.canView('quizes'))
  const canCreate = computed(() => permissionStore.canCreate('quizes'))
  const canEdit = computed(() => permissionStore.canEdit('quizes'))
  const canDelete = computed(() => permissionStore.canDelete('quizes'))
  
  const canCreateQuiz = computed(() => canCreate.value && canCreateMore())
  const canCreateMoreQuizes = computed(() => canCreateMore())

  const getDifficultyText = (level: string) => {
    const texts: Record<string, string> = {
      iniciante: t('common.iniciante'),
      intermediario: t('common.intermediario'),
      avancado: t('common.avancado'),
    }
    return texts[level] || level
  }

  const getLevelClass = (level: string) => {
    const classes: Record<string, string> = {
      iniciante: 'level-beginner',
      intermediario: 'level-intermediate',
      avancado: 'level-advanced',
    }
    return classes[level] || 'level-intermediate'
  }

  const resetForm = () => {
    form.titulo = ''
    form.descricao = ''
    form.nivel_dificuldade = 'intermediario'
    form.tempo_limite = undefined
    form.publico = false
    editingId.value = null
    isEditing.value = false
  }

  const openCreateModal = () => {
    if (!canCreate.value) {
      console.warn('Sem permissão para criar quizzes')
      return
    }
    if (!canCreateMore()) {
      console.warn('Limite de quizzes atingido')
      return
    }
    resetForm()
    isEditing.value = false
    showModal.value = true
  }

  const openEditModal = (quiz: Quiz) => {
    if (!canEdit.value) {
      console.warn('Sem permissão para editar quizzes')
      return
    }
    form.titulo = quiz.titulo
    form.descricao = quiz.descricao || ''
    form.nivel_dificuldade = quiz.nivel_dificuldade
    form.tempo_limite = quiz.tempo_limite ?? undefined
    form.publico = quiz.publico || false
    editingId.value = quiz.id
    isEditing.value = true
    showModal.value = true
  }

  const viewQuiz = (id: number) => {
    if (!canView.value) {
      console.warn('Sem permissão para visualizar quizzes')
      return
    }
    router.push(`/quizes/${id}`)
  }

  const playQuiz = (id: number) => {
    if (!canView.value) {
      console.warn('Sem permissão para visualizar quizzes')
      return
    }
    router.push(`/quizes/${id}/play`)
  }

  const confirmDelete = (quiz: Quiz) => {
    if (!canDelete.value) {
      console.warn('Sem permissão para excluir quizzes')
      return
    }
    deletingQuiz.value = quiz
    showDeleteModal.value = true
  }

  const handleDelete = async () => {
    if (!deletingQuiz.value) return
    deleting.value = true
    try {
      await deleteQuiz(deletingQuiz.value.id)
      showDeleteModal.value = false
      const userData = localStorage.getItem('user')
      if (userData) {
        const user = JSON.parse(userData) as User
        await loadQuizes(user.id)
      }
    } finally {
      deleting.value = false
      deletingQuiz.value = null
    }
  }

  const submitForm = async () => {
    const userData = localStorage.getItem('user')
    if (!userData) return

    let user: User
    try {
      user = JSON.parse(userData) as User
    } catch (e) {
      console.error('Erro ao fazer parse do userData:', e)
      return
    }

    submitting.value = true

    try {
      const data = {
        usuario_id: user.id,
        titulo: form.titulo,
        descricao: form.descricao,
        nivel_dificuldade: form.nivel_dificuldade,
        tempo_limite: form.tempo_limite,
        total_questoes: 0,
        publico: form.publico,
        tipo_criacao: 'manual',
      }

      if (isEditing.value && editingId.value) {
        await updateQuiz(editingId.value, data)
      } else {
        await createQuiz(data)
      }

      closeModal()
      await loadQuizes(user.id)
    } finally {
      submitting.value = false
    }
  }

  const closeModal = () => {
    showModal.value = false
    resetForm()
  }

  const getUserFromLocalStorage = (): User | null => {
    const userData = localStorage.getItem('user')
    if (!userData) return null
    try {
      return JSON.parse(userData) as User
    } catch {
      return null
    }
  }

  onMounted(async () => {
    await permissionStore.loadPermissions()
    const user = getUserFromLocalStorage()
    if (user?.id) {
      await loadQuizes(user.id)
    }
  })

  return {
    quizes,
    loading,
    showModal,
    showDeleteModal,
    isEditing,
    deletingQuiz,
    submitting,
    deleting,
    form,
    openCreateModal,
    openEditModal,
    viewQuiz,
    playQuiz,
    confirmDelete,
    handleDelete,
    submitForm,
    closeModal,
    getDifficultyText,
    getLevelClass,
    canView,
    canEdit,
    canDelete,
    canCreateQuiz,
    canCreateMoreQuizes,
  }
}