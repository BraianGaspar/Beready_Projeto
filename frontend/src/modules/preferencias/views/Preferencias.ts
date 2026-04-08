import { ref, onMounted } from 'vue'
import { useAlert } from '@/shared/composables/useAlert'
import {
  preferenciaService,
  type Preferencia,
} from '@/modules/preferencias/services/preferenciaService'

export function usePreferencias() {
  const { success, error } = useAlert()
  const loading = ref(false)
  const saving = ref(false)

  const form = ref<Preferencia>({
    usuario_id: 0,
    tema: 'claro',
    modo_daltonico: false,
    notificacoes_ativas: true,
    som_ativo: true,
    traducao_automatica: true,
    preferencia_dificuldade: 'adaptativo',
    meta_diaria_minutos: 30,
  })

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

  const fetchPreferencias = async () => {
    const userId = getCurrentUserId()
    if (!userId) return

    loading.value = true
    try {
      const response = await preferenciaService.getByUsuario(userId)
      if (response.data.data) {
        form.value = { ...form.value, ...response.data.data }
      }
    } catch (err: any) {
      if (err.response?.status !== 400) {
        error(err.response?.data?.message || 'Erro ao carregar preferências')
      }
    } finally {
      loading.value = false
    }
  }

  const save = async () => {
    const userId = getCurrentUserId()
    if (!userId) {
      error('Usuário não autenticado')
      return
    }

    form.value.usuario_id = userId
    saving.value = true

    try {
      await preferenciaService.save(form.value)
      success('Preferências salvas com sucesso!')
    } catch (err: any) {
      error(err.response?.data?.message || 'Erro ao salvar preferências')
    } finally {
      saving.value = false
    }
  }

  onMounted(() => {
    fetchPreferencias()
  })

  return {
    form,
    loading,
    saving,
    save,
  }
}
