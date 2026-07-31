import { ref } from 'vue'
import { flashcardService } from '../services/flashcardService'
import { useAlert } from '@/shared/composables/useAlert'
import { useI18n } from 'vue-i18n'
import { usePermissionStore } from '@/stores/permissionStore'
import { usePlan } from '@/shared/composables/usePlan'

export interface Flashcard {
  id: number
  usuario_id?: number
  frente: string
  verso: string
  nivel_dificuldade?: 'facil' | 'medio' | 'dificil'
  criado_em?: string
  atualizado_em?: string
}

export interface FlashcardData {
  frente: string
  verso: string
  nivel_dificuldade?: 'facil' | 'medio' | 'dificil'
  usuario_id?: number
}

// Tipo para o serviço
interface ServiceFlashcardData {
  pergunta: string
  resposta: string
  dificuldade: 'facil' | 'medio' | 'dificil'
  usuario_id: number
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
      // Garantir que usuario_id seja number
      const usuarioId = data.usuario_id || 0
      
      // Mapear os campos para o que o serviço espera
      const serviceData: ServiceFlashcardData = {
        pergunta: data.frente,
        resposta: data.verso,
        dificuldade: data.nivel_dificuldade || 'medio',
        usuario_id: usuarioId
      }
      const response = await flashcardService.create(serviceData)
      
      // Mapear a resposta de volta para o formato esperado
      const flashcard: Flashcard = {
        id: response.data.data.id || 0,
        frente: response.data.data.pergunta || data.frente,
        verso: response.data.data.resposta || data.verso,
        nivel_dificuldade: response.data.data.dificuldade || data.nivel_dificuldade || 'medio',
        usuario_id: usuarioId,
        criado_em: response.data.data.criado_em || new Date().toISOString(),
        atualizado_em: response.data.data.atualizado_em || new Date().toISOString()
      }
      flashcards.value.unshift(flashcard)
      success(t('flashcards.successCreate'))
      return flashcard
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
      // Mapear os campos para o que o serviço espera
      const serviceData: Partial<ServiceFlashcardData> = {}
      if (data.frente !== undefined) serviceData.pergunta = data.frente
      if (data.verso !== undefined) serviceData.resposta = data.verso
      if (data.nivel_dificuldade !== undefined) serviceData.dificuldade = data.nivel_dificuldade
      if (data.usuario_id !== undefined) serviceData.usuario_id = data.usuario_id

      const response = await flashcardService.update(id, serviceData)
      
      // Buscar o índice do flashcard
      const index = flashcards.value.findIndex(f => f.id === id)
      if (index !== -1) {
        const current = flashcards.value[index]
        // Garantir que current existe
        if (current) {
          flashcards.value[index] = {
            ...current,
            frente: response.data.data.pergunta || current.frente,
            verso: response.data.data.resposta || current.verso,
            nivel_dificuldade: response.data.data.dificuldade || current.nivel_dificuldade,
            atualizado_em: response.data.data.atualizado_em || new Date().toISOString()
          }
        }
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