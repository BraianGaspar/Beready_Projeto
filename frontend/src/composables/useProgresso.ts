import { ref } from 'vue'
import { progressoService, type Progresso } from '@/services/progressoService'
import { useAlert } from './useAlert'

export function useProgresso() {
  const progresso = ref<Progresso | null>(null)
  const loading = ref(false)
  const { success, error } = useAlert()

  const getCurrentUserId = (): number | null => {
    const userData = localStorage.getItem('user')
    if (!userData) return null
    try {
      const user = JSON.parse(userData)
      return user.id
    } catch {
      return null
    }
  }

  const fetchProgresso = async (usuarioId?: number) => {
    const userId = usuarioId || getCurrentUserId()
    if (!userId) {
      console.log('Usuário não autenticado')
      return null
    }
    
    loading.value = true
    try {
      const response = await progressoService.getByUsuario(userId)
      progresso.value = response.data.data
      return progresso.value
    } catch (err: any) {
      if (err.response?.status === 400) {
        // Usuário não tem progresso ainda, retorna valores padrão
        progresso.value = {
          usuario_id: userId,
          vocabulario_aprendido: 0,
          flashcards_concluidos: 0,
          quizes_concluidos: 0,
          tempo_total_estudo: 0,
          sequencia_atual: 0,
          maior_sequencia: 0
        }
        return progresso.value
      }
      error(err.response?.data?.message || 'Erro ao carregar progresso')
      throw err
    } finally {
      loading.value = false
    }
  }

  const saveProgresso = async (data: Progresso) => {
    loading.value = true
    try {
      const response = await progressoService.save(data)
      progresso.value = response.data.data
      success('Progresso salvo com sucesso!')
      return progresso.value
    } catch (err: any) {
      error(err.response?.data?.message || 'Erro ao salvar progresso')
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateEstatisticas = async (usuarioId: number, stats: Partial<Progresso>) => {
    const current = await fetchProgresso(usuarioId)
    if (current) {
      return saveProgresso({ ...current, ...stats, usuario_id: usuarioId })
    }
    return saveProgresso({ usuario_id: usuarioId, ...stats })
  }

  return { progresso, loading, fetchProgresso, saveProgresso, updateEstatisticas }
}