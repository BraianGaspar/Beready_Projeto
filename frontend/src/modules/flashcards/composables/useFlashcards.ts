import { ref } from 'vue'
import { flashcardService } from '../services/flashcardService'
import type { Flashcard, FlashcardInput } from '@/core/types'
import { getApiErrorMessage } from '@/core/services/api'
import { useAlert } from '@/shared/composables/useAlert'
import { useI18n } from 'vue-i18n'
import { usePermissionStore } from '@/stores/permissionStore'
import { usePlan } from '@/shared/composables/usePlan'
import { normalizeNivel, type NivelDificuldade } from '@/shared/utils/nivelDificuldade'

export interface FlashcardFormData {
  frente: string
  verso: string
  nivel_dificuldade?: NivelDificuldade
  usuario_id: number
  prompt_id?: number
  frase_id?: number
}

export function useFlashcards() {
  const flashcards = ref<Flashcard[]>([])
  const loading = ref(false)
  const { success, error } = useAlert()
  const { t } = useI18n()
  const permissionStore = usePermissionStore()
  const plan = usePlan()

  const canCreateMore = (): boolean => {
    return plan.canCreateMore('flashcards', flashcards.value.length)
  }

  const loadFlashcards = async () => {
    loading.value = true
    try {
      const response = await flashcardService.getAll()
      flashcards.value = response.data.data || []
      return flashcards.value
    } catch (err: unknown) {
      error(getApiErrorMessage(err) || t('flashcards.errorLoad'))
      flashcards.value = []
    } finally {
      loading.value = false
    }
  }

  const createFlashcard = async (data: FlashcardFormData) => {
    if (!permissionStore.canCreate('flashcards')) {
      error(t('permissions.createDenied', { recurso: t('common.flashcards') }))
      throw new Error('Permissão negada')
    }

    if (!canCreateMore()) {
      error(t('plan.limitReached', { recurso: t('common.flashcards') }))
      throw new Error('Limite do plano atingido')
    }

    loading.value = true
    try {
      const payload: FlashcardInput = {
        usuario_id: data.usuario_id,
        frente: data.frente,
        verso: data.verso,
        nivel_dificuldade: normalizeNivel(data.nivel_dificuldade),
        prompt_id: data.prompt_id,
        frase_id: data.frase_id,
      }

      const response = await flashcardService.create(payload)
      const created: Flashcard = { ...payload, ...response.data.data }

      flashcards.value.unshift(created)
      success(t('flashcards.successCreate'))
      return created
    } catch (err: unknown) {
      error(getApiErrorMessage(err) || t('flashcards.errorCreate'))
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateFlashcard = async (id: number, data: Partial<FlashcardFormData>) => {
    if (!permissionStore.canEdit('flashcards')) {
      error(t('permissions.editDenied', { recurso: t('common.flashcards') }))
      throw new Error('Permissão negada')
    }

    loading.value = true
    try {
      const payload: Partial<FlashcardInput> = {}
      if (data.frente !== undefined) payload.frente = data.frente
      if (data.verso !== undefined) payload.verso = data.verso
      if (data.nivel_dificuldade !== undefined) {
        payload.nivel_dificuldade = normalizeNivel(data.nivel_dificuldade)
      }
      if (data.prompt_id !== undefined) payload.prompt_id = data.prompt_id
      if (data.frase_id !== undefined) payload.frase_id = data.frase_id

      const response = await flashcardService.update(id, payload)

      const index = flashcards.value.findIndex((f) => f.id === id)
      const current = flashcards.value[index]
      if (current) {
        flashcards.value[index] = { ...current, ...response.data.data }
      }
      success(t('flashcards.successUpdate'))
      return response.data.data
    } catch (err: unknown) {
      error(getApiErrorMessage(err) || t('flashcards.errorUpdate'))
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteFlashcard = async (id: number) => {
    if (!permissionStore.canDelete('flashcards')) {
      error(t('permissions.deleteDenied', { recurso: t('common.flashcards') }))
      throw new Error('Permissão negada')
    }

    loading.value = true
    try {
      await flashcardService.delete(id)
      flashcards.value = flashcards.value.filter((f) => f.id !== id)
      success(t('flashcards.successDelete'))
      return true
    } catch (err: unknown) {
      error(getApiErrorMessage(err) || t('flashcards.errorDelete'))
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
