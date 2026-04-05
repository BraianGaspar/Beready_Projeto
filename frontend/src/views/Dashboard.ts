import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAlert } from '@/composables/useAlert'

export function useDashboard() {
  const router = useRouter()
  const { success, clearAllAlerts } = useAlert()
  const loading = ref(false)
  const user = ref<any>(null)
  let welcomeShown = false

  const userName = computed(() => {
    const name = user.value?.nome || user.value?.name || 'Usuário'
    return name.split(' ')[0]
  })

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
        // Limpa o localStorage
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
      // Mesmo com erro, limpa o localStorage e redireciona
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
        // Mostrar bem-vindo apenas uma vez e sem conflito
        if (!welcomeShown) {
          welcomeShown = true
          setTimeout(() => {
            success(`Bem-vindo de volta, ${user.value.nome}!`)
          }, 100)
        }
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
    handleLogout,
  }
}
