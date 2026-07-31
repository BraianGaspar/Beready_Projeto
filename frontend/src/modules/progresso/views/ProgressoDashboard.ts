import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAlert } from '@/shared/composables/useAlert'
import api from '@/core/services/api'
import { useI18n } from 'vue-i18n'

export function useProgressoDashboard() {
  const router = useRouter()
  const { success, error } = useAlert()
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

  const formatarTempo = (totalSegundos: number): string => {
    if (totalSegundos <= 0) return '0 min'
    const minutos = Math.floor(totalSegundos / 60)
    if (minutos < 60) return `${minutos} min`
    const horas = Math.floor(minutos / 60)
    const minRestante = minutos % 60
    if (minRestante === 0) return `${horas}h`
    return `${horas}h ${minRestante}min`
  }

  const loadProgresso = async () => {
    const userData = localStorage.getItem('user')
    if (!userData) return

    loading.value = true
    try {
      const user = JSON.parse(userData)
      const response = await api.get(`/progresso/usuario/${user.id}`)
      if (response.data.success) {
        progresso.value = response.data.data
      }
    } catch (err) {
      console.error('Erro ao carregar progresso:', err)
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
    formatarTempo,
    loadProgresso
  }
}