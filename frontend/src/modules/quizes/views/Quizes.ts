import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useQuizes } from '../composables/useQuizes'
import { useGerarQuiz } from '../composables/useGerarQuiz'
import type { Quiz } from '@/core/types'
import { useI18n } from 'vue-i18n'
import { usePermissionStore } from '@/stores/permissionStore'
import { useAlert } from '@/shared/composables/useAlert'
import { getNivelLabelKey } from '@/shared/utils/nivelDificuldade'

export function useQuizesView() {
  const router = useRouter()
  const { t } = useI18n()
  const permissionStore = usePermissionStore()
  const { error } = useAlert()
  const { quizes, loading, loadQuizes, deleteQuiz, canCreateMore } = useQuizes()
  const gerador = useGerarQuiz()

  const showDeleteModal = ref(false)
  const showGerarModal = ref(false)
  const deletingQuiz = ref<Quiz | null>(null)
  const deleting = ref(false)

  // Permissões
  const canView = computed(() => permissionStore.canView('quizes'))
  const canCreate = computed(() => permissionStore.canCreate('quizes'))
  const canEdit = computed(() => permissionStore.canEdit('quizes'))
  const canDelete = computed(() => permissionStore.canDelete('quizes'))

  const canCreateQuiz = computed(() => canCreate.value && canCreateMore())
  const canCreateMoreQuizes = computed(() => canCreateMore())

  const getDifficultyText = (level: string) => t(getNivelLabelKey(level))

  // Criar = permissão + limite do plano (vale também para o quiz gerado dos flashcards)
  const ensureCanCreate = (): boolean => {
    if (!canCreate.value) {
      error(t('permissions.createDenied', { recurso: t('common.quizes') }))
      return false
    }
    if (!canCreateMore()) {
      error(t('quizes.limitReached'))
      return false
    }
    return true
  }

  // Criação e edição usam o editor completo (dados do quiz + questões)
  const openCreate = () => {
    if (ensureCanCreate()) router.push('/quizes/add')
  }

  const openEdit = (quiz: Quiz) => {
    if (!canEdit.value) {
      error(t('permissions.editDenied', { recurso: t('common.quizes') }))
      return
    }
    router.push(`/quizes/edit/${quiz.id}`)
  }

  const openGerarModal = () => {
    if (!ensureCanCreate()) return
    gerador.reset()
    gerador.carregarTags()
    showGerarModal.value = true
  }

  const submitGerar = async () => {
    const quiz = await gerador.gerar()
    if (!quiz) return
    showGerarModal.value = false
    router.push(`/quizes/${quiz.id}/play`)
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

  onMounted(async () => {
    await permissionStore.loadPermissions()
    await loadQuizes()
  })

  return {
    quizes,
    loading,
    showDeleteModal,
    showGerarModal,
    deletingQuiz,
    deleting,
    gerador,
    openCreate,
    openEdit,
    openGerarModal,
    submitGerar,
    viewQuiz,
    playQuiz,
    confirmDelete,
    handleDelete,
    getDifficultyText,
    canView,
    canEdit,
    canDelete,
    canCreateQuiz,
    canCreateMoreQuizes,
  }
}
