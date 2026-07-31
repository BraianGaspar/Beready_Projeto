import { ref, computed, onMounted } from 'vue'
import { useAuth } from '@/shared/composables/useAuth'
import { useAlert } from '@/shared/composables/useAlert'
import api from '@/core/services/api'
import type { AxiosError } from 'axios'

// TIPOS
interface User {
  id: number
  nome: string
  email: string
  role: 'admin' | 'user'
  status: 'ativo' | 'inativo'
  foto_perfil?: string
}

interface Stats {
  total_users: number
  admin_count: number
  user_count: number
  total_flashcards: number
  total_quizes: number
  total_prompts: number
  total_tags: number
  total_traducoes: number
  total_imagens: number
  total_frases: number
}

interface ApiResponse<T> {
  success: boolean
  data: T
  message?: string
}

export function useAdminPanel() {
  const { user, logout } = useAuth()
  const { success, error } = useAlert()
  const activeTab = ref<string>('users')
  const users = ref<User[]>([])
  const loadingUsers = ref<boolean>(false)
  const updatingRole = ref<number | null>(null)
  const searchQuery = ref<string>('')
  const stats = ref<Stats>({
    total_users: 0,
    admin_count: 0,
    user_count: 0,
    total_flashcards: 0,
    total_quizes: 0,
    total_prompts: 0,
    total_tags: 0,
    total_traducoes: 0,
    total_imagens: 0,
    total_frases: 0,
  })

  const currentUserId = computed<number | undefined>(() => user.value?.id)

  const filteredUsers = computed<User[]>(() => {
    if (!searchQuery.value) return users.value
    const query = searchQuery.value.toLowerCase()
    return users.value.filter(
      (u) => u.nome?.toLowerCase().includes(query) || u.email?.toLowerCase().includes(query),
    )
  })

  // Função para verificar se o erro é de autenticação
  const isAuthError = (err: unknown): boolean => {
    const axiosError = err as AxiosError
    return axiosError.response?.status === 401 || 
           axiosError.response?.status === 403 ||
           axiosError.message?.includes('Expired token') ||
           axiosError.message?.includes('Unauthorized')
  }

  // Função para tratar erros de autenticação
  const handleAuthError = async (err: unknown): Promise<void> => {
    if (isAuthError(err)) {
      error('Sua sessão expirou. Faça login novamente.')
      await logout()
      // Redirecionar para login após 2 segundos
      setTimeout(() => {
        window.location.href = '/login'
      }, 2000)
    }
  }

  const loadUsers = async (): Promise<void> => {
    loadingUsers.value = true
    try {
      const response = await api.get<ApiResponse<User[]>>('/admin/users')

      if (response.data && response.data.success) {
        users.value = response.data.data || []
      } else {
        console.error('Erro na resposta:', response.data?.message || 'Resposta inválida')
        users.value = []
        error(response.data?.message || 'Erro ao carregar usuários')
      }
    } catch (err: unknown) {
      console.error('Erro ao carregar usuários:', err)
      
      // Verificar se é erro de autenticação
      if (isAuthError(err)) {
        await handleAuthError(err)
        return
      }

      const axiosError = err as AxiosError<{ message?: string }>
      
      // Tratamento específico para erro 500
      if (axiosError.response?.status === 500) {
        error('Erro no servidor. Verifique se as rotas de admin estão configuradas corretamente.')
        console.error('Detalhes do erro 500:', axiosError.response?.data)
      } else {
        const errorMessage = axiosError.response?.data?.message || 
                            axiosError.message || 
                            'Erro ao carregar usuários'
        error(errorMessage)
      }
      
      users.value = []
    } finally {
      loadingUsers.value = false
    }
  }

  const loadStats = async (): Promise<void> => {
    try {
      const response = await api.get<ApiResponse<Stats>>('/admin/stats')
      if (response.data && response.data.success) {
        stats.value = response.data.data || stats.value
      } else {
        console.error('Erro na resposta:', response.data?.message)
        error(response.data?.message || 'Erro ao carregar estatísticas')
      }
    } catch (err: unknown) {
      console.error('Erro ao carregar estatísticas:', err)
      
      // Verificar se é erro de autenticação
      if (isAuthError(err)) {
        await handleAuthError(err)
        return
      }

      const axiosError = err as AxiosError<{ message?: string }>
      
      // Tratamento específico para erro 500
      if (axiosError.response?.status === 500) {
        error('Erro no servidor. Verifique se as rotas de admin estão configuradas corretamente.')
        console.error('Detalhes do erro 500:', axiosError.response?.data)
      } else {
        const errorMessage = axiosError.response?.data?.message || 
                            axiosError.message || 
                            'Erro ao carregar estatísticas'
        error(errorMessage)
      }
    }
  }

  const toggleRole = async (targetUser: User): Promise<void> => {
    // Verificar se o usuário atual é admin
    if (user.value?.role !== 'admin') {
      error('Você não tem permissão para alterar funções')
      return
    }

    // Não permitir alterar a própria role
    if (targetUser.id === user.value?.id) {
      error('Você não pode alterar sua própria função')
      return
    }

    const newRole: 'admin' | 'user' = targetUser.role === 'admin' ? 'user' : 'admin'

    updatingRole.value = targetUser.id

    try {
      const response = await api.post<ApiResponse<{ success: boolean }>>('/admin/users/role', {
        user_id: targetUser.id,
        role: newRole,
      })

      if (response.data && response.data.success) {
        await loadUsers()
        await loadStats() // Atualiza estatísticas após mudar role
        success(
          `${targetUser.nome} ${newRole === 'admin' ? 'agora é Administrador' : 'agora é Usuário'}`,
        )
      } else {
        error(response.data?.message || 'Erro ao alterar permissão')
      }
    } catch (err: unknown) {
      console.error('Erro ao alterar role:', err)
      
      // Verificar se é erro de autenticação
      if (isAuthError(err)) {
        await handleAuthError(err)
        return
      }

      const axiosError = err as AxiosError<{ message?: string }>
      const errorMessage = axiosError.response?.data?.message || 
                          axiosError.message || 
                          'Erro ao alterar permissão'
      
      error(errorMessage)
    } finally {
      updatingRole.value = null
    }
  }

  // Função adicional para verificar se o usuário atual é admin
  const isAdmin = computed<boolean>(() => user.value?.role === 'admin')

  // Função para recarregar todos os dados
  const reloadAll = async (): Promise<void> => {
    await Promise.all([
      loadUsers(),
      loadStats()
    ])
  }

  // Função para renovar o token (se necessário)
  const refreshToken = async (): Promise<boolean> => {
    try {
      const refreshToken = localStorage.getItem('refresh_token')
      if (!refreshToken) return false

      const response = await api.post('/auth/refresh', { refresh_token: refreshToken })
      if (response.data.success) {
        const { access_token, refresh_token } = response.data.data
        localStorage.setItem('access_token', access_token)
        localStorage.setItem('refresh_token', refresh_token)
        return true
      }
      return false
    } catch (err) {
      console.error('Erro ao renovar token:', err)
      return false
    }
  }

  onMounted(async () => {
    // Verificar se o usuário tem permissão de admin antes de carregar
    if (user.value?.role === 'admin') {
      // Tentar carregar os dados
      await loadUsers()
      await loadStats()
    } else {
      error('Acesso negado. Você não tem permissões de administrador.')
    }
  })

  return {
    // State
    user,
    activeTab,
    users,
    loadingUsers,
    updatingRole,
    searchQuery,
    stats,
    currentUserId,
    filteredUsers,
    isAdmin,
    
    // Methods
    toggleRole,
    loadUsers,
    loadStats,
    reloadAll,
    refreshToken,
  }
}