import { ref } from 'vue'
import { progressoService, type Progresso } from '@/services/progressoService'
import { useAlert } from './useAlert'

export function useProgresso() {
  const progresso = ref<Progresso | null>(null)
  const loading = ref(false)
  const { success, error } = useAlert()

  const fetchProgresso = async (usuarioId: number) => {
    loading.value = true
    try {
      const response = await progressoService.getByUsuario(usuarioId)
      progresso.value = response.data.data
      return progresso.value
    } catch (err: any) {
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
