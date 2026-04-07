import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAlert } from '@/composables/useAlert'
import { useProgresso } from '@/composables/useProgresso'

export function useDashboard() {
  const router = useRouter()
  const { success, clearAllAlerts } = useAlert()
  const { progresso, fetchProgresso } = useProgresso()
  const loading = ref(false)
  const user = ref<any>(null)
  let welcomeShown = false

  // Estatísticas do dashboard
  const stats = ref({
    flashcardsCount: 0,
    acertoRate: 0,
    sequenciaAtual: 0,
    tempoEstudo: '0h',
    progressoGeral: 0,
  })

  const userName = computed(() => {
    const name = user.value?.nome || user.value?.name || 'Usuário'
    return name.split(' ')[0]
  })

  // Carregar estatísticas do progresso
  const loadStats = async () => {
    if (user.value?.id) {
      await fetchProgresso(user.value.id)
      if (progresso.value) {
        stats.value = {
          flashcardsCount: progresso.value.flashcards_concluidos || 0,
          acertoRate: progresso.value.vocabulario_aprendido
            ? Math.min(Math.round((progresso.value.vocabulario_aprendido / 100) * 100), 100)
            : 0,
          sequenciaAtual: progresso.value.sequencia_atual || 0,
          tempoEstudo: `${Math.floor((progresso.value.tempo_total_estudo || 0) / 60)}h ${(progresso.value.tempo_total_estudo || 0) % 60}m`,
          progressoGeral: progresso.value.vocabulario_aprendido
            ? Math.min(Math.round((progresso.value.vocabulario_aprendido / 100) * 100), 100)
            : 0,
        }
      }
    }
  }

  const handleLogout = async () => {
    loading.value = true
    try {
      const response = await fetch('http://localhost:8765/auth/logout', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
      })

      const data = await response.json()

      if (data.success) {
        localStorage.removeItem('user')
        localStorage.removeItem('token')

        success('Logout realizado com sucesso!')
        setTimeout(() => {
          clearAllAlerts()
          router.push('/login')
        }, 500)
      } else {
        console.error('Erro no logout:', data.message)
      }
    } catch (err) {
      console.error('Erro no logout:', err)
      localStorage.removeItem('user')
      localStorage.removeItem('token')
      router.push('/login')
    } finally {
      loading.value = false
    }
  }

  onMounted(() => {
    const userData = localStorage.getItem('user')
    if (userData) {
      try {
        user.value = JSON.parse(userData)
        if (!welcomeShown) {
          welcomeShown = true
          setTimeout(() => {
            success(`Bem-vindo de volta, ${user.value.nome}!`)
          }, 100)
        }
        loadStats()
      } catch (e) {
        console.error('Erro ao carregar usuário:', e)
      }
    }
    if (!user.value) router.push('/login')
  })

  return {
    user,
    loading,
    userName,
    stats,
    handleLogout,
  }
}
