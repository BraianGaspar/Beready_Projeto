import { onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useAuth } from './useAuth' // Assumindo que você tem este composable
import api from '@/core/services/api'

// Páginas públicas que NÃO devem ter tema escuro/daltônico
const publicRoutes = ['/', '/login', '/register', '/forgot-password', '/reset-password']

export function useTheme() {
  const route = useRoute()
  const { isAuthenticated, user } = useAuth()

  const loadUserPreferences = async () => {
    const currentRoute = window.location.pathname

    // Verifica se é uma rota pública
    const isPublicRoute = publicRoutes.some(
      (route) => currentRoute === route || currentRoute.startsWith('/reset-password'),
    )

    // Se for pública, remove os modos especiais e retorna
    if (isPublicRoute) {
      document.documentElement.classList.remove('dark-mode')
      document.body.classList.remove('dark-mode')
      document.documentElement.classList.remove('daltonico-mode')
      document.body.classList.remove('daltonico-mode')
      return
    }

    // Se não estiver autenticado, não carrega preferências
    if (!isAuthenticated.value || !user.value?.id) {
      return
    }

    try {
      const response = await api.get(`/preferencias/usuario/${user.value.id}`)
      if (response.data.success && response.data.data) {
        // Aplica Modo Escuro
        if (response.data.data.tema === 'escuro') {
          document.documentElement.classList.add('dark-mode')
          document.body.classList.add('dark-mode')
        } else {
          document.documentElement.classList.remove('dark-mode')
          document.body.classList.remove('dark-mode')
        }

        // Aplica Modo Daltonico
        if (response.data.data.modo_daltonico) {
          document.documentElement.classList.add('daltonico-mode')
          document.body.classList.add('daltonico-mode')
        } else {
          document.documentElement.classList.remove('daltonico-mode')
          document.body.classList.remove('daltonico-mode')
        }
      }
    } catch (err) {
      console.error('Erro ao carregar preferências:', err)
    }
  }

  // Watchers e Hooks de ciclo de vida
  watch(
    () => route.path,
    () => {
      loadUserPreferences()
    },
    { immediate: true },
  )

  watch(
    () => user.value,
    () => {
      loadUserPreferences()
    },
    { immediate: true },
  )

  onMounted(() => {
    loadUserPreferences()
  })

  // Se precisar expor algo para o template no futuro, retorne aqui
  return {}
}