import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'

export function useDashboard() {
  const router = useRouter()
  const loading = ref(false)
  const user = ref<any>(null)

  const userName = computed(() => {
    const name = user.value?.nome || user.value?.name || 'Usuário'
    return name.split(' ')[0]
  })

  const handleLogout = async () => {
    loading.value = true
    try {
      await fetch('http://localhost:8765/auth/logout', { method: 'POST' })
    } catch (err) {
      console.error('Erro no logout:', err)
    } finally {
      localStorage.removeItem('user')
      localStorage.removeItem('auth_token')
      router.push('/login')
      loading.value = false
    }
  }

  onMounted(() => {
    const userData = localStorage.getItem('user')
    if (userData) {
      try {
        user.value = JSON.parse(userData)
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
