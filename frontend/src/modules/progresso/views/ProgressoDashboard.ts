import { ref, onMounted } from 'vue'
import { useAlert } from '@/shared/composables/useAlert'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '@/stores/auth'
import { progressoService } from '../services/progressoService'
import { formatTempoEstudo } from '@/shared/utils/formatTempoEstudo'

export function useProgressoDashboard() {
  const authStore = useAuthStore()
  const { error } = useAlert()
  const { t } = useI18n()

  const loading = ref(false)
  const progresso = ref({
    vocabulario_aprendido: 0,
    flashcards_concluidos: 0,
    quizes_concluidos: 0,
    tempo_total_estudo: 0,
    sequencia_atual: 0,
    maior_sequencia: 0,
  })

  const loadProgresso = async () => {
    const userId = authStore.user?.id
    if (!userId) return

    loading.value = true
    try {
      const response = await progressoService.getByUsuario(userId)
      const data = response.data.data
      if (response.data.success && data) {
        progresso.value = {
          vocabulario_aprendido: data.vocabulario_aprendido ?? 0,
          flashcards_concluidos: data.flashcards_concluidos ?? 0,
          quizes_concluidos: data.quizes_concluidos ?? 0,
          tempo_total_estudo: data.tempo_total_estudo ?? 0,
          sequencia_atual: data.sequencia_atual ?? 0,
          maior_sequencia: data.maior_sequencia ?? 0,
        }
      }
    } catch {
      error(t('progresso.errorLoad'))
    } finally {
      loading.value = false
    }
  }

  onMounted(() => {
    loadProgresso()
  })

  return {
    progresso,
    loading,
    formatarTempo: formatTempoEstudo,
    loadProgresso,
  }
}
