import { ref } from 'vue'
import { flashcardService } from '../services/flashcardService'
import type { Flashcard } from '../services/flashcardService'
import { useAlert } from '@/shared/composables/useAlert'
import { useI18n } from 'vue-i18n'
import { usePermissionStore } from '@/stores/permissionStore'
import { usePlan } from '@/shared/composables/usePlan'

// Tipo para os dados de criação (sem os campos auto-gerados)
type CreateFlashcardData = Omit<Flashcard, 'id' | 'criado_em' | 'atualizado_em'>

// Tipo para os dados de atualização (parcial)
type UpdateFlashcardData = Partial<Flashcard>

// Tipo para o erro da API
interface ApiError {
  response?: {
    data?: {
      message?: string
    }
  }
  message?: string
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

  const createFlashcard = async (data: {
    frente: string
    verso: string
    nivel_dificuldade?: 'facil' | 'medio' | 'dificil'
    usuario_id: number
    prompt_id?: number
    frase_id?: number
  }) => {
    if (!permissionStore.canCreate('flashcards')) {
      error(t('permissions.createDenied', { recurso: 'flashcards' }))
      throw new Error('Permissão negada')
    }

    if (!canCreateMore()) {
      error(t('plan.limitReached', { recurso: 'flashcards' }))
      throw new Error('Limite do plano atingido')
    }

    loading.value = true
    try {
      // Construir o objeto no formato que o service/backend espera
      const serviceData: CreateFlashcardData = {
        usuario_id: data.usuario_id,
        frente: data.frente,
        verso: data.verso,
        nivel_dificuldade: data.nivel_dificuldade || 'medio',
        prompt_id: data.prompt_id,
        frase_id: data.frase_id
      }

      const response = await flashcardService.create(serviceData)

      const newFlashcard: Flashcard = {
        id: response.data.data.id || 0,
        usuario_id: data.usuario_id,
        frente: response.data.data.frente || data.frente,
        verso: response.data.data.verso || data.verso,
        nivel_dificuldade: response.data.data.nivel_dificuldade || data.nivel_dificuldade || 'medio',
        prompt_id: response.data.data.prompt_id || data.prompt_id,
        frase_id: response.data.data.frase_id || data.frase_id,
        criado_em: response.data.data.criado_em || new Date().toISOString(),
        atualizado_em: response.data.data.atualizado_em || new Date().toISOString()
      }

      flashcards.value.unshift(newFlashcard)
      success(t('flashcards.successCreate'))
      return newFlashcard
    } catch (err: unknown) {
      const apiError = err as ApiError
      error(apiError.response?.data?.message || t('flashcards.errorCreate'))
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateFlashcard = async (id: number, data: Partial<{
    frente: string
    verso: string
    nivel_dificuldade?: 'facil' | 'medio' | 'dificil'
    usuario_id: number
    prompt_id?: number
    frase_id?: number
  }>) => {
    if (!permissionStore.canEdit('flashcards')) {
      error(t('permissions.editDenied', { recurso: 'flashcards' }))
      throw new Error('Permissão negada')
    }

    loading.value = true
    try {
      // Construir o objeto no formato que o service/backend espera
      const serviceData: UpdateFlashcardData = {}
      if (data.frente !== undefined) serviceData.frente = data.frente
      if (data.verso !== undefined) serviceData.verso = data.verso
      if (data.nivel_dificuldade !== undefined) serviceData.nivel_dificuldade = data.nivel_dificuldade
      if (data.usuario_id !== undefined) serviceData.usuario_id = data.usuario_id
      if (data.prompt_id !== undefined) serviceData.prompt_id = data.prompt_id
      if (data.frase_id !== undefined) serviceData.frase_id = data.frase_id

      const response = await flashcardService.update(id, serviceData)

      const index = flashcards.value.findIndex(f => f.id === id)
      if (index !== -1) {
        const current = flashcards.value[index]
        if (current) {
          flashcards.value[index] = {
            ...current,
            ...response.data.data
          }
        }
      }
      success(t('flashcards.successUpdate'))
      return response.data.data
    } catch (err: unknown) {
      const apiError = err as ApiError
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
      const apiError = err as ApiError
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