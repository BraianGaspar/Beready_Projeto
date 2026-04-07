import { ref } from 'vue'
import { useRouter } from 'vue-router'

export function useAuth() {
  const router = useRouter()
  const loading = ref(false)
  const user = ref<any>(null)

  const login = async (email: string, password: string) => {
    loading.value = true
    try {
      const response = await fetch('http://localhost:8765/auth/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password }),
      })
      const data = await response.json()
      
      console.log('Login response:', data) // Debug
      
      if (data.success) {
        // 🔥 Salva o usuário completo no localStorage
        localStorage.setItem('user', JSON.stringify(data.user))
        user.value = data.user
        router.push('/dashboard')
        return { success: true }
      }
      return { success: false, message: data.message }
    } catch (error) {
      console.error('Login error:', error)
      return { success: false, message: 'Erro de conexão' }
    } finally {
      loading.value = false
    }
  }

  const logout = async () => {
    loading.value = true
    try {
      await fetch('http://localhost:8765/auth/logout', { method: 'POST' })
    } catch (error) {
      console.error('Erro no logout:', error)
    } finally {
      localStorage.removeItem('user')
      user.value = null
      router.push('/login')
      loading.value = false
    }
  }

  const loadUser = () => {
    const userData = localStorage.getItem('user')
    if (userData) {
      try {
        user.value = JSON.parse(userData)
        console.log('User loaded:', user.value) // Debug
      } catch (e) {
        console.error('Erro ao carregar usuário:', e)
      }
    }
    return user.value
  }

  loadUser()

  return { user, loading, login, logout, loadUser }
}