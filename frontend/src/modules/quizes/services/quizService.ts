import api from '@/core/services/api'
import type { Quiz, ApiResponse } from '@/core/types'

export type QuizInput = Omit<Quiz, 'id' | 'criado_em' | 'atualizado_em'>

export const quizService = {
  // Listar quizes do usuário logado (o backend filtra pelo usuário do token)
  getAll: () => api.get<ApiResponse<Quiz[]>>('/quizes'),

  // Buscar quiz por ID
  getById: (id: number) => api.get<ApiResponse<Quiz>>(`/quizes/${id}`),

  // Criar quiz
  create: (data: QuizInput) => api.post<ApiResponse<Quiz>>('/quizes', data),

  // Atualizar quiz
  update: (id: number, data: Partial<QuizInput>) =>
    api.put<ApiResponse<Quiz>>(`/quizes/${id}`, data),

  // Deletar quiz
  delete: (id: number) => api.delete<ApiResponse<null>>(`/quizes/${id}`),
}
