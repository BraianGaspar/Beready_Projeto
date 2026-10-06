import api from '@/core/services/api'
import type { ApiResponse, Flashcard, FlashcardInput } from '@/core/types'

export type { Flashcard, FlashcardInput }

export const flashcardService = {
  // Listar flashcards do usuário logado (o backend filtra pelo usuário do token)
  getAll: () => api.get<ApiResponse<Flashcard[]>>('/flashcards'),

  // Buscar flashcard por ID
  getById: (id: number) => api.get<ApiResponse<Flashcard>>(`/flashcards/${id}`),

  // Criar flashcard
  create: (data: FlashcardInput) => api.post<ApiResponse<Flashcard>>('/flashcards', data),

  // Atualizar flashcard
  update: (id: number, data: Partial<FlashcardInput>) =>
    api.put<ApiResponse<Flashcard>>(`/flashcards/edit/${id}`, data),

  // Deletar flashcard
  delete: (id: number) => api.delete<ApiResponse<null>>(`/flashcards/delete/${id}`),
}
