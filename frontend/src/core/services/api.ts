import axios from 'axios'

// URL CORRETA - porta 8765 do backend
const API_BASE_URL = 'http://localhost:8765'

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  },
})

// Interceptor para adicionar token
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  console.log('Ì≥§ Requisi√ß√£o:', config.method?.toUpperCase(), config.url)
  return config
})

// Interceptor para logar respostas (debug)
api.interceptors.response.use(
  (response) => {
    console.log('Ì≥• Resposta:', response.status, response.data?.success)
    return response
  },
  (error) => {
    console.log('‚ùå Erro:', error.response?.status, error.response?.data)
    return Promise.reject(error)
  },
)

// Exportar fun√ß√µes de autentica√ß√£o
export const auth = {
  register: (data: { nome: string; email: string; senha: string }) =>
    api.post('/auth/register', data),

  login: (data: { email: string; senha: string }) => api.post('/auth/login', data),

  logout: () => api.post('/auth/logout'),

  profile: () => api.get('/users/profile'),
}

// Exporta√ß√£o padr√£o
export default api
