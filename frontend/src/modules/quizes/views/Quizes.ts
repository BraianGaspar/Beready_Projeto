import { ref, reactive, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useQuizes } from '../composables/useQuizes'
import type { Quiz } from '@/core/types'
import { useI18n } from 'vue-i18n'
import { usePermissionStore } from '@/stores/permissionStore'
import { useAuthStore } from '@/stores/auth'
import { useAlert } from '@/shared/composables/useAlert'
import { getNivelLabelKey, normalizeNivel, type NivelDificuldade } from '@/shared/utils/nivelDificuldade'

interface FormData {
  titulo: string
  descricao: string
  nivel_dificuldade: NivelDificuldade
  tempo_limite: number | undefined
  total_questoes: number
  publico: boolean
  tipo_criacao: string
}

export function useQuizesView() {
  const router = useRouter()
  const { t } = useI18n()
  const permissionStore = usePermissionStore()
  const authStore = useAuthStore()
  const { error } = useAlert()
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
    nivel_dificuldade: 'iniciante',
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

  const getDifficultyText = (level: string) => t(getNivelLabelKey(level))

  const resetForm = () => {
    form.titulo = ''
    form.descricao = ''
    form.nivel_dificuldade = 'iniciante'
    form.tempo_limite = undefined
    form.publico = false
    editingId.value = null
    isEditing.value = false
  }

  const openCreateModal = () => {
    if (!canCreate.value) {
      error(t('permissions.createDenied', { recurso: t('common.quizes') }))
      return
    }
    if (!canCreateMore()) {
      error(t('quizes.limitReached'))
      return
    }
    resetForm()
    isEditing.value = false
    showModal.value = true
  }

  const openEditModal = (quiz: Quiz) => {
    if (!canEdit.value) {
      error(t('permissions.editDenied', { recurso: t('common.quizes') }))
      return
    }
    form.titulo = quiz.titulo
    form.descricao = quiz.descricao || ''
    form.nivel_dificuldade = normalizeNivel(quiz.nivel_dificuldade)
    form.tempo_limite = quiz.tempo_limite ?? undefined
    form.publico = quiz.publico || false
    editingId.value = quiz.id
    isEditing.value = true
    showModal.value = true
  }

  const viewQuiz = (id: number) => {
    if (!canView.value) {
      error(t('permissions.viewDenied', { recurso: t('common.quizes') }))
      return
    }
    router.push(`/quizes/${id}`)
  }

  const playQuiz = (id: number) => {
    if (!canView.value) {
      error(t('permissions.viewDenied', { recurso: t('common.quizes') }))
      return
    }
    router.push(`/quizes/${id}/play`)
  }

  const confirmDelete = (quiz: Quiz) => {
    if (!canDelete.value) {
      error(t('permissions.deleteDenied', { recurso: t('common.quizes') }))
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
      await loadQuizes()
    } catch {
      // Alerta de erro já exibido por useQuizes
    } finally {
      deleting.value = false
      deletingQuiz.value = null
    }
  }

  const submitForm = async () => {
    const user = authStore.user
    if (!user) return

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
      await loadQuizes()
    } catch {
      // Alerta de erro já exibido por useQuizes
    } finally {
      submitting.value = false
    }
  }

  const closeModal = () => {
    showModal.value = false
    resetForm()
  }

  onMounted(async () => {
    await permissionStore.loadPermissions()
    await loadQuizes()
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
    canView,
    canEdit,
    canDelete,
    canCreateQuiz,
    canCreateMoreQuizes,
  }
}