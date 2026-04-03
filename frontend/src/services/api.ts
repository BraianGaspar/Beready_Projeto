import axios from 'axios'

const API_BASE_URL = 'http://localhost:8765'

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  },
})

// Interceptor para logar requisições (debug)
api.interceptors.request.use((request) => {
  console.log('📤 Requisição:', request.method?.toUpperCase(), request.url, request.data)
  return request
})

api.interceptors.response.use(
  (response) => {
    console.log('📥 Resposta:', response.status, response.data)
    return response
  },
  (error) => {
    console.log('❌ Erro:', error.response?.status, error.response?.data)
    return Promise.reject(error)
  },
)

export const auth = {
  register: (data: { nome: string; email: string; senha: string }) =>
    api.post('/auth/register', data),

  login: (data: { email: string; password: string }) => api.post('/auth/login', data),

  logout: () => api.post('/auth/logout'),

  profile: () => api.get('/users/profile'),
}

export default api
