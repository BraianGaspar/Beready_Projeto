import { ref, reactive, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useFlashcards } from '../composables/useFlashcards'
import type { User } from '@/core/types'
import { useI18n } from 'vue-i18n'
import { usePermissionStore } from '@/stores/permissionStore'

// Definir Flashcard localmente para compatibilidade
interface Flashcard {
  id: number
  usuario_id?: number
  frente: string
  verso: string
  nivel_dificuldade?: 'facil' | 'medio' | 'dificil'
  criado_em?: string
  atualizado_em?: string
}

interface FormData {
  frente: string
  verso: string
  nivel_dificuldade: 'facil' | 'medio' | 'dificil'
}

export function useFlashcardsView() {
  const router = useRouter()
  const { t } = useI18n()
  const permissionStore = usePermissionStore()
  
  const { 
    flashcards, 
    loading, 
    loadFlashcards, 
    createFlashcard, 
    updateFlashcard, 
    deleteFlashcard,
    canCreateMore
  } = useFlashcards()

  const showModal = ref(false)
  const showDeleteModal = ref(false)
  const isEditing = ref(false)
  const editingId = ref<number | null>(null)
  const deletingFlashcard = ref<Flashcard | null>(null)
  const submitting = ref(false)
  const deleting = ref(false)

  const form = reactive<FormData>({
    frente: '',
    verso: '',
    nivel_dificuldade: 'medio',
  })

  const flashcardsCount = computed(() => flashcards.value.length)
  
  // Computeds para o template - usando o permissionStore diretamente
  const canView = computed(() => permissionStore.canView('flashcards'))
  const canCreate = computed(() => permissionStore.canCreate('flashcards'))
  const canEdit = computed(() => permissionStore.canEdit('flashcards'))
  const canDelete = computed(() => permissionStore.canDelete('flashcards'))
  
  // Verifica se pode criar (permissão + limite)
  const canCreateFlashcard = computed(() => {
    return canCreate.value && canCreateMore()
  })
  
  const canCreateMoreFlashcards = computed(() => canCreateMore())

  const resetForm = (): void => {
    form.frente = ''
    form.verso = ''
    form.nivel_dificuldade = 'medio'
    editingId.value = null
    isEditing.value = false
  }

  const openCreateModal = (): void => {
    if (!canCreate.value) {
      console.warn('Sem permissão para criar flashcards')
      return
    }
    
    if (!canCreateMore()) {
      console.warn('Limite de flashcards atingido')
      return
    }
    
    resetForm()
    isEditing.value = false
    showModal.value = true
  }

  const openEditModal = (flashcard: Flashcard): void => {
    if (!canEdit.value) {
      console.warn('Sem permissão para editar flashcards')
      return
    }
    form.frente = flashcard.frente
    form.verso = flashcard.verso
    form.nivel_dificuldade = flashcard.nivel_dificuldade || 'medio'
    editingId.value = flashcard.id
    isEditing.value = true
    showModal.value = true
  }

  const viewFlashcard = (id: number): void => {
    if (!canView.value) {
      console.warn('Sem permissão para visualizar flashcards')
      return
    }
    router.push(`/flashcards/${id}`)
  }

  const studyFlashcard = (id: number): void => {
    if (!canView.value) {
      console.warn('Sem permissão para visualizar flashcards')
      return
    }
    router.push(`/flashcards/${id}/study`)
  }

  const confirmDelete = (flashcard: Flashcard): void => {
    if (!canDelete.value) {
      console.warn('Sem permissão para excluir flashcards')
      return
    }
    deletingFlashcard.value = flashcard
    showDeleteModal.value = true
  }

  const handleDelete = async (): Promise<void> => {
    if (!deletingFlashcard.value) return
    deleting.value = true
    try {
      await deleteFlashcard(deletingFlashcard.value.id)
      showDeleteModal.value = false
      const userData = localStorage.getItem('user')
      if (userData) {
        const user = JSON.parse(userData) as User
        await loadFlashcards(user.id)
      }
    } catch (error) {
      console.error('Erro ao deletar flashcard:', error)
    } finally {
      deleting.value = false
      deletingFlashcard.value = null
    }
  }

  const submitForm = async (): Promise<void> => {
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
        frente: form.frente,
        verso: form.verso,
        nivel_dificuldade: form.nivel_dificuldade,
      }

      if (isEditing.value && editingId.value) {
        await updateFlashcard(editingId.value, data)
      } else {
        await createFlashcard(data)
      }

      closeModal()
      await loadFlashcards(user.id)
    } catch (error) {
      console.error('Erro ao salvar flashcard:', error)
    } finally {
      submitting.value = false
    }
  }

  const closeModal = (): void => {
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
    // Carregar permissões antes de tudo
    await permissionStore.loadPermissions()
    
    const user = getUserFromLocalStorage()
    if (user?.id) {
      await loadFlashcards(user.id)
    }
  })

  return {
    flashcards,
    loading,
    showModal,
    showDeleteModal,
    isEditing,
    editingId,
    deletingFlashcard,
    submitting,
    deleting,
    form,
    flashcardsCount,
    canCreateFlashcard,
    canCreateMoreFlashcards,
    canView,
    canEdit,
    canDelete,
    openCreateModal,
    openEditModal,
    viewFlashcard,
    studyFlashcard,
    confirmDelete,
    handleDelete,
    submitForm,
    closeModal,
  }
}