import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/core/services/api'
import axios, { AxiosError } from 'axios'
import { useI18n } from 'vue-i18n'
import type { User } from '@/shared/composables/useAuth'

interface ProgressData {
  total_flashcards_estudados?: number
  flashcards_concluidos?: number
  total_estudados?: number
  flashcards_count?: number
  total?: number
  sequencia_dias?: number
  sequencia_atual?: number
  sequencia?: number
  tempo_total_estudo?: number
  taxa_acerto?: number
  acerto_rate?: number
  progresso_geral?: number
}

interface StatsData {
  flashcardsCount: number
  acertoRate: number
  sequenciaAtual: number
  tempoEstudo: string
  progressoGeral: number
}

export function useDashboard() {
  const router = useRouter()
  const { t } = useI18n()
  // Alterar o tipo para User | null ao invés do tipo parcial
  const user = ref<User | null>(null)
  const loading = ref(false)
  const stats = ref<StatsData>({
    flashcardsCount: 0,
    acertoRate: 0,
    sequenciaAtual: 0,
    tempoEstudo: '0min',
    progressoGeral: 0,
  })

  const userName = computed(() => {
    if (user.value?.nome) {
      return user.value.nome.split(' ')[0]
    }
    return t('common.usuario')
  })

  const motivationalMessage = computed(() => {
    const dias = stats.value.sequenciaAtual
    if (dias >= 7) {
      return t('dashboard.motivacional.alta', { dias })
    }
    if (dias >= 3) {
      return t('dashboard.motivacional.media', { dias })
    }
    return t('dashboard.motivacional.padrao')
  })

  const formatTempoEstudo = (totalSegundos: number): string => {
    if (totalSegundos <= 0) {
      return '0 s'
    }

    if (totalSegundos < 60) {
      return `${totalSegundos} s`
    }

    const minutos = Math.floor(totalSegundos / 60)

    if (minutos < 60) {
      return `${minutos} min`
    }

    const horas = Math.floor(minutos / 60)
    const minutosRestantes = minutos % 60

    if (minutosRestantes === 0) {
      return `${horas} h`
    }

    return `${horas} h ${minutosRestantes} min`
  }

  const loadUserData = async (): Promise<void> => {
    const userData = localStorage.getItem('user')
    if (!userData) return

    loading.value = true
    try {
      // Fazer o parse com o tipo User completo
      const parsedUser = JSON.parse(userData) as User
      
      // Verificar se o usuário tem todas as propriedades necessárias
      if (parsedUser && parsedUser.id && parsedUser.nome && parsedUser.email && parsedUser.role) {
        user.value = parsedUser
      } else {
        console.warn('Dados do usuário incompletos:', parsedUser)
        user.value = null
        return
      }

      const response = await api.get<{ data: ProgressData }>(`/progresso/usuario/${parsedUser.id}`)

      if (response.data && response.data.data) {
        const data = response.data.data

        stats.value.flashcardsCount =
          data.total_flashcards_estudados ||
          data.flashcards_concluidos ||
          data.total_estudados ||
          data.flashcards_count ||
          data.total ||
          0

        stats.value.sequenciaAtual =
          data.sequencia_dias || data.sequencia_atual || data.sequencia || 0

        const totalSegundos = data.tempo_total_estudo || 0
        stats.value.tempoEstudo = formatTempoEstudo(totalSegundos)

        stats.value.acertoRate = data.taxa_acerto ?? data.acerto_rate ?? 0
        stats.value.progressoGeral = Math.min(100, data.progresso_geral ?? data.taxa_acerto ?? 0)
      }
    } catch (err: unknown) {
      console.error('Erro ao carregar estatisticas:', err)

      if (axios.isAxiosError(err)) {
        const axiosError = err as AxiosError
        if (axiosError.response?.status === 401) {
          router.push('/login')
        }
      }
    } finally {
      loading.value = false
    }
  }

  const handleLogout = (): void => {
    localStorage.removeItem('user')
    localStorage.removeItem('access_token')
    localStorage.removeItem('refresh_token')
    router.push('/login')
  }

  onMounted(() => {
    loadUserData()
  })

  return {
    user,
    loading,
    userName,
    stats,
    motivationalMessage,
    handleLogout,
    formatTempoEstudo,
  }
}