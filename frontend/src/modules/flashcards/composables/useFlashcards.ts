import { ref } from 'vue'
import { flashcardService } from '../services/flashcardService'
import { useAlert } from '@/shared/composables/useAlert'
import { useI18n } from 'vue-i18n'
import { usePermissionStore } from '@/stores/permissionStore'
import { usePlan } from '@/shared/composables/usePlan'

export interface Flashcard {
  id: number
  frente: string
  verso: string
  nivel_dificuldade?: string
  usuario_id?: number
  created_at?: string
  updated_at?: string
}

export interface FlashcardData {
  frente: string
  verso: string
  nivel_dificuldade?: string
  usuario_id?: number
}

export function useFlashcards() {
  const flashcards = ref<Flashcard[]>([])
  const loading = ref(false)
  const { success, error } = useAlert()
  const { t } = useI18n()
  const permissionStore = usePermissionStore()
  const plan = usePlan()

  // Verificação de limite do plano para criação
  const canCreateMore = (): boolean => {
    return plan.canCreateMore('flashcards', flashcards.value.length)
  }

  const loadFlashcards = async (usuarioId: number) => {
    loading.value = true
    try {
      const response = await flashcardService.getByUsuario(usuarioId)
      flashcards.value = response.data.data || []
      return flashcards.value
    } catch (err: unknown) {
      console.error('Erro ao carregar flashcards:', err)
      error(t('flashcards.errorLoad'))
      flashcards.value = []
    } finally {
      loading.value = false
    }
  }

  const createFlashcard = async (data: FlashcardData) => {
    // Verificar permissão de criação
    if (!permissionStore.canCreate('flashcards')) {
      error(t('permissions.createDenied', { recurso: 'flashcards' }))
      throw new Error('Permissão negada')
    }

    // Verificar limite do plano
    if (!canCreateMore()) {
      error(t('plan.limitReached', { recurso: 'flashcards' }))
      throw new Error('Limite do plano atingido')
    }

    loading.value = true
    try {
      const response = await flashcardService.create(data)
      flashcards.value.unshift(response.data.data)
      success(t('flashcards.successCreate'))
      return response.data.data
    } catch (err: unknown) {
      const apiError = err as { response?: { data?: { message?: string } } }
      error(apiError.response?.data?.message || t('flashcards.errorCreate'))
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateFlashcard = async (id: number, data: Partial<FlashcardData>) => {
    if (!permissionStore.canEdit('flashcards')) {
      error(t('permissions.editDenied', { recurso: 'flashcards' }))
      throw new Error('Permissão negada')
    }

    loading.value = true
    try {
      const response = await flashcardService.update(id, data)
      const index = flashcards.value.findIndex(f => f.id === id)
      if (index !== -1) {
        flashcards.value[index] = { ...flashcards.value[index], ...response.data.data }
      }
      success(t('flashcards.successUpdate'))
      return response.data.data
    } catch (err: unknown) {
      const apiError = err as { response?: { data?: { message?: string } } }
      error(apiError.response?.data?.message || t('flashcards.errorUpdate'))
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteFlashcard = async (id: number) => {
    if (!permissionStore.canDelete('flashcards')) {
      error(t('permissions.deleteDenied', { recurso: 'flashcards' }))
      throw new Error('Permissão negada')
    }

    loading.value = true
    try {
      await flashcardService.delete(id)
      flashcards.value = flashcards.value.filter(f => f.id !== id)
      success(t('flashcards.successDelete'))
      return true
    } catch (err: unknown) {
      const apiError = err as { response?: { data?: { message?: string } } }
      error(apiError.response?.data?.message || t('flashcards.errorDelete'))
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    flashcards,
    loading,
    loadFlashcards,
    createFlashcard,
    updateFlashcard,
    deleteFlashcard,
    canCreateMore,
  }
}