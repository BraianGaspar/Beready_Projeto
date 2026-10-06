import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAlert } from '@/shared/composables/useAlert'
import { getApiErrorMessage } from '@/core/services/api'
import { flashcardService } from '../services/flashcardService'
import { useFlashcards } from '../composables/useFlashcards'
import { usePermissionStore } from '@/stores/permissionStore'
import { getNivelLabelKey } from '@/shared/utils/nivelDificuldade'
import type { Flashcard } from '@/core/types'
import { formatDate as formatLocaleDate } from '@/shared/utils/intl'

export function useFlashcardView() {
  const router = useRouter()
  const route = useRoute()
  const { t } = useI18n()
  const { error } = useAlert()
  const permissionStore = usePermissionStore()
  // deleteFlashcard verifica permissão e exibe os alertas de sucesso/erro
  const { deleteFlashcard } = useFlashcards()

  const flashcard = ref<Flashcard | null>(null)
  const loading = ref(true)
  const deleting = ref(false)
  const showConfirmModal = ref(false)

  const loadFlashcard = async () => {
    const id = Number(route.params.id)
    if (!id) {
      router.push('/flashcards')
      return
    }

    loading.value = true
    try {
      const { data } = await flashcardService.getById(id)
      if (data.success && data.data) {
        flashcard.value = data.data
      } else {
        error(data.message || t('flashcards.errorLoad'))
        router.push('/flashcards')
      }
    } catch (err) {
      error(getApiErrorMessage(err) || t('flashcards.errorLoad'))
      router.push('/flashcards')
    } finally {
      loading.value = false
    }
  }

  const goBack = () => {
    router.push('/flashcards')
  }

  const studyFlashcard = () => {
    if (!flashcard.value) return
    router.push(`/flashcards/${flashcard.value.id}/study`)
  }

  const openDeleteModal = () => {
    showConfirmModal.value = true
  }

  const confirmDelete = async () => {
    if (!flashcard.value) return

    deleting.value = true
    try {
      await deleteFlashcard(flashcard.value.id)
      showConfirmModal.value = false
      router.push('/flashcards')
    } catch {
      // Alerta de erro já exibido por useFlashcards
    } finally {
      deleting.value = false
    }
  }

  const getLevelText = (level?: string) => t(getNivelLabelKey(level))

  const formatDate = (date?: string) => formatLocaleDate(date)

  onMounted(() => {
    loadFlashcard()
  })

  return {
    flashcard,
    loading,
    deleting,
    showConfirmModal,
    canDelete: () => permissionStore.canDelete('flashcards'),
    goBack,
    studyFlashcard,
    openDeleteModal,
    confirmDelete,
    getLevelText,
    formatDate,
  }
}
