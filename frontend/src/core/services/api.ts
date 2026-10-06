import axios, { type AxiosError, type InternalAxiosRequestConfig } from 'axios'
import { API_BASE_URL } from '@/shared/config/env'
import { useAuthStore } from '@/stores/auth'

/**
 * Instância única do axios para falar com o backend.
 *
 * - O access token fica apenas em memória (store de auth) e é injetado aqui.
 * - O refresh token é um cookie httpOnly (Path=/auth) controlado pelo backend,
 *   por isso `withCredentials: true`.
 * - 401 => tenta UM refresh compartilhado e repete a request; se falhar, logout.
 * - 403 => apenas propaga o erro (sem permissão não é sessão inválida).
 */
const api = axios.create({
  baseURL: API_BASE_URL,
  withCredentials: true,
  headers: {
    Accept: 'application/json',
  },
})

// Endpoints de autenticação nunca disparam refresh automático (evita loops)
const AUTH_ENDPOINTS = [
  '/auth/login',
  '/auth/register',
  '/auth/refresh',
  '/auth/logout',
  '/auth/social/exchange',
  '/auth/forgot-password',
  '/auth/reset-password',
]

const isAuthEndpoint = (url?: string): boolean =>
  !!url && AUTH_ENDPOINTS.some((endpoint) => url.startsWith(endpoint))

declare module 'axios' {
  interface AxiosRequestConfig {
    // Não tentar refresh automático em caso de 401
    skipAuthRefresh?: boolean
  }
}

interface RetriableRequestConfig extends InternalAxiosRequestConfig {
  _retry?: boolean
}

api.interceptors.request.use((config) => {
  const authStore = useAuthStore()
  if (authStore.accessToken) {
    config.headers.Authorization = `Bearer ${authStore.accessToken}`
  }
  return config
})

api.interceptors.response.use(
  (response) => response,
  async (error: AxiosError) => {
    const originalRequest = error.config as RetriableRequestConfig | undefined

    if (
      !originalRequest ||
      error.response?.status !== 401 ||
      originalRequest._retry ||
      originalRequest.skipAuthRefresh ||
      isAuthEndpoint(originalRequest.url)
    ) {
      return Promise.reject(error)
    }

    const authStore = useAuthStore()

    // Request feita sem sessão (ex.: página pública): nada para renovar
    if (!originalRequest.headers.Authorization) {
      return Promise.reject(error)
    }

    originalRequest._retry = true

    // Várias requests com 401 simultâneas aguardam a mesma promise de refresh
    const refreshed = await authStore.refresh()

    if (!refreshed) {
      await authStore.logout({ callServer: false })
      return Promise.reject(error)
    }

    originalRequest.headers.Authorization = `Bearer ${authStore.accessToken}`
    return api(originalRequest)
  },
)

/**
 * Extrai a mensagem de erro enviada pelo backend (`{ success: false, message }`).
 */
export const getApiErrorMessage = (err: unknown): string | undefined => {
  if (axios.isAxiosError<{ message?: string }>(err)) {
    return err.response?.data?.message
  }
  return undefined
}

export default api
