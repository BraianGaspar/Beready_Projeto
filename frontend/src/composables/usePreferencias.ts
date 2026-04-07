import { ref } from 'vue'
import { preferenciaService, type Preferencia } from '@/services/preferenciaService'
import { useAlert } from './useAlert'

export function usePreferencias() {
  const preferencias = ref<Preferencia | null>(null)
  const loading = ref(false)
  const { success, error } = useAlert()

  const fetchPreferencias = async (usuarioId: number) => {
    loading.value = true
    try {
      const response = await preferenciaService.getByUsuario(usuarioId)
      preferencias.value = response.data.data
      return preferencias.value
    } catch (err: any) {
      error(err.response?.data?.message || 'Erro ao carregar preferências')
      throw err
    } finally {
      loading.value = false
    }
  }

  const savePreferencias = async (data: Preferencia) => {
    loading.value = true
    try {
      const response = await preferenciaService.save(data)
      preferencias.value = response.data.data
      success('Preferências salvas com sucesso!')
      return preferencias.value
    } catch (err: any) {
      error(err.response?.data?.message || 'Erro ao salvar preferências')
      throw err
    } finally {
      loading.value = false
    }
  }

  return { preferencias, loading, fetchPreferencias, savePreferencias }
}
