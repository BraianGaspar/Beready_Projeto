import api from '@/core/services/api'
import type { ApiResponse, Flashcard, FlashcardInput, FlashcardsDevidos, NotaRevisao } from '@/core/types'

export type { Flashcard, FlashcardInput, FlashcardsDevidos, NotaRevisao }

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

  // Flashcards com revisão vencida (fila do "Revisar agora"); `limite` corta a lista, `total` é sempre a contagem
  getDevidos: (limite?: number) =>
    api.get<ApiResponse<FlashcardsDevidos>>('/flashcards/revisao', {
      params: limite ? { limite } : undefined,
    }),

  // Grava a avaliação do estudo: reagenda (SM-2), registra a resposta e soma o progresso no backend
  revisar: (id: number, nota: NotaRevisao) =>
    api.post<ApiResponse<Flashcard>>(`/flashcards/${id}/revisao`, { nota }),
}
