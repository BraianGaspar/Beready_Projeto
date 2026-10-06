import { onScopeDispose, watch } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/core/services/api'
import { useAuthStore } from '@/stores/auth'

// Páginas públicas: seguem o tema do sistema (prefers-color-scheme), sem daltônico
const publicRoutes = ['/', '/login', '/register', '/forgot-password', '/oauth-callback']

const isPublicRoute = (path: string): boolean =>
  publicRoutes.includes(path) || path.startsWith('/reset-password')

export interface ThemeState {
  /** Tema escuro (preferência tema === 'escuro' ou sistema escuro em rotas públicas) */
  dark: boolean
  /** Paleta segura para daltonismo (preferência modo_daltonico) */
  daltonico: boolean
}

const setMode = (className: string, active: boolean): void => {
  document.documentElement.classList.toggle(className, active)
  document.body.classList.toggle(className, active)
}

/**
 * Único ponto que escreve as classes de tema em <html> e <body>.
 * Os tokens de src/styles/tokens.css reagem a .dark-mode e .daltonico-mode.
 */
export const applyTheme = ({ dark, daltonico }: ThemeState): void => {
  setMode('dark-mode', dark)
  setMode('daltonico-mode', daltonico)
}

/** Converte os campos salvos em /preferencias para o estado de tema. */
export const themeFromPreferences = (prefs: {
  tema?: string | null
  modo_daltonico?: boolean | null
}): ThemeState => ({
  dark: prefs.tema === 'escuro',
  daltonico: !!prefs.modo_daltonico,
})

/**
 * Aplica o tema conforme a rota e o usuário:
 * - rotas públicas ou sem sessão: segue o sistema (prefers-color-scheme), sem daltônico;
 * - logado: preferências de /preferencias/usuario/{id}, buscadas no máximo uma vez
 *   por usuário (e novamente apenas ao voltar de uma página pública);
 * - logado sem preferências salvas (404): padrão claro, igual ao formulário de preferências.
 */
export function useTheme() {
  const route = useRoute()
  const authStore = useAuthStore()
  const systemDark = window.matchMedia('(prefers-color-scheme: dark)')

  // Usuário cujas preferências já foram buscadas/aplicadas
  let appliedForUserId: number | null = null
  // true enquanto o tema segue o sistema (rota pública / sem sessão)
  let followingSystem = false

  const applySystemTheme = (): void => {
    applyTheme({ dark: systemDark.matches, daltonico: false })
  }

  const loadUserPreferences = async (userId: number): Promise<void> => {
    appliedForUserId = userId
    try {
      const response = await api.get(`/preferencias/usuario/${userId}`)
      // Usuário mudou (logout/troca) durante a requisição: descarta
      if (appliedForUserId !== userId || followingSystem) return
      if (response.data.success && response.data.data) {
        applyTheme(themeFromPreferences(response.data.data))
      }
    } catch (err) {
      if (appliedForUserId !== userId || followingSystem) return
      // 404 = usuário ainda sem preferências salvas
      const status = (err as { response?: { status?: number } }).response?.status
      if (status === 404) {
        applyTheme({ dark: false, daltonico: false })
      } else {
        console.error('Erro ao carregar preferências:', err)
      }
    }
  }

  const sync = (): void => {
    const userId = authStore.user?.id

    if (isPublicRoute(route.path) || !authStore.isAuthenticated || !userId) {
      followingSystem = true
      appliedForUserId = null
      applySystemTheme()
      return
    }

    followingSystem = false
    if (appliedForUserId === userId) return
    loadUserPreferences(userId)
  }

  const onSystemChange = (): void => {
    if (followingSystem) applySystemTheme()
  }
  systemDark.addEventListener('change', onSystemChange)
  onScopeDispose(() => systemDark.removeEventListener('change', onSystemChange))

  watch([() => route.path, () => authStore.user?.id, () => authStore.isAuthenticated], sync, {
    immediate: true,
  })

  return {}
}
