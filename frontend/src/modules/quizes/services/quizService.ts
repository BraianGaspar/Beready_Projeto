import api from '@/core/services/api'
import type { Quiz, ApiResponse } from '@/core/types'

export const quizService = {
  // Listar quizes do usuário
  getByUsuario: (usuarioId: number): Promise<ApiResponse<Quiz[]>> => {
    console.log('Buscando quizes do usuário:', usuarioId)
    return api.get(`/quizes?usuario_id=${usuarioId}`)
  },

  // Buscar quiz por ID
  getById: (id: number): Promise<ApiResponse<Quiz>> => {
    console.log('Buscando quiz por ID:', id)
    return api.get(`/quizes/view/${id}`)
  },

  // Criar quiz
  create: (data: Omit<Quiz, 'id' | 'criado_em' | 'atualizado_em'>): Promise<ApiResponse<Quiz>> => {
    console.log('Criando quiz:', data)
    return api.post('/quizes', data)
  },

  // Atualizar quiz
  update: (id: number, data: Partial<Omit<Quiz, 'id' | 'criado_em' | 'atualizado_em'>>): Promise<ApiResponse<Quiz>> => {
    console.log('Atualizando quiz:', id, data)
    return api.put(`/quizes/edit/${id}`, data)
  },

  // Deletar quiz
  delete: (id: number): Promise<ApiResponse<null>> => {
    console.log('Deletando quiz:', id)
    return api.delete(`/quizes/delete/${id}`)
  },
}
