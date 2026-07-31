import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/core/services/api'

export interface User {
  id: number
  uuid?: string
  nome: string
  email: string
  role?: 'user' | 'admin'
  telefone?: string
  nivel_ingles?: string
  idioma_preferido?: string
  objetivos_aprendizado?: string
  foto_perfil?: string
  assinatura?: {
    plano: {
      nome: string
      recursos?: string[]
      limites?: Record<string, number>
    }
  }
}

export interface RegisterData {
  nome: string
  email: string
  password: string
  password_confirmation?: string
  telefone?: string
  nivel_ingles?: string
  idioma_preferido?: string
  objetivos_aprendizado?: string
}

export interface LoginResponse {
  success: boolean
  user: User
  token?: string
  message?: string
}

export interface RegisterResponse {
  success: boolean
  user?: User
  message?: string
}

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const token = ref<string | null>(localStorage.getItem('token'))

  const login = async (email: string, password: string): Promise<boolean> => {
    const response = await api.post<LoginResponse>('/auth/login', { email, password })
    if (response.data.success) {
      user.value = response.data.user
      localStorage.setItem('user', JSON.stringify(response.data.user))
      if (response.data.token) {
        token.value = response.data.token
        localStorage.setItem('token', response.data.token)
      }
      return true
    }
    throw new Error(response.data.message || 'Erro ao fazer login')
  }

  const register = async (data: RegisterData): Promise<RegisterResponse> => {
    const response = await api.post<RegisterResponse>('/auth/register', data)
    if (response.data.success) {
      return response.data
    }
    throw new Error(response.data.message || 'Erro ao cadastrar')
  }

  const logout = (): void => {
    user.value = null
    token.value = null
    localStorage.removeItem('user')
    localStorage.removeItem('token')
    localStorage.removeItem('refresh_token')
  }

  const fetchUser = async (id: number): Promise<void> => {
    const response = await api.get<{ success: boolean; user: User }>(`/users/${id}`)
    if (response.data.success) {
      user.value = response.data.user
    }
  }

  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const isAdmin = computed(() => user.value?.role === 'admin')

  return { 
    user, 
    token, 
    login, 
    register, 
    logout, 
    fetchUser,
    isAuthenticated,
    isAdmin
  }
})