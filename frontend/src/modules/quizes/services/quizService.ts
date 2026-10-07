import api from '@/core/services/api'
import type {
  ApiResponse,
  GerarQuizInput,
  Quiz,
  QuizCorrecao,
  QuizQuestao,
  QuizQuestaoInput,
  QuizRespostaQuestao,
  QuizResultado,
} from '@/core/types'

// total_questoes é calculado pelo backend (contagem das questões)
export type QuizInput = Omit<Quiz, 'id' | 'criado_em' | 'atualizado_em' | 'questoes' | 'total_questoes'> & {
  questoes?: QuizQuestaoInput[]
}

export const quizService = {
  // Listar quizes do usuário logado (o backend filtra pelo usuário do token)
  getAll: () => api.get<ApiResponse<Quiz[]>>('/quizes'),

  // Buscar quiz por ID (com as questões, sem gabarito)
  getById: (id: number) => api.get<ApiResponse<Quiz>>(`/quizes/${id}`),

  // Criar quiz (questões opcionais, validadas juntas no backend)
  create: (data: QuizInput) => api.post<ApiResponse<Quiz>>('/quizes', data),

  // Atualizar quiz (só os dados do quiz; as questões têm rota própria)
  update: (id: number, data: Partial<QuizInput>) =>
    api.put<ApiResponse<Quiz>>(`/quizes/${id}`, data),

  // Deletar quiz
  delete: (id: number) => api.delete<ApiResponse<null>>(`/quizes/${id}`),

  // Questões com gabarito (só o dono): editor
  getQuestoes: (id: number) => api.get<ApiResponse<QuizQuestao[]>>(`/quizes/${id}/questoes`),

  // Substitui todas as questões (ordem = posição na lista)
  saveQuestoes: (id: number, questoes: QuizQuestaoInput[]) =>
    api.put<ApiResponse<QuizQuestao[]>>(`/quizes/${id}/questoes`, { questoes }),

  // Correção de uma questão no servidor (não grava)
  verificar: (quizId: number, questaoId: number, resposta: Omit<QuizRespostaQuestao, 'questao_id'>) =>
    api.post<ApiResponse<QuizCorrecao>>(`/quizes/${quizId}/questoes/${questaoId}/verificar`, resposta),

  // Corrige a tentativa inteira, grava as respostas e o progresso
  finalizar: (quizId: number, respostas: QuizRespostaQuestao[]) =>
    api.post<ApiResponse<QuizResultado>>(`/quizes/${quizId}/finalizar`, { respostas }),

  // Gera um quiz de múltipla escolha a partir dos flashcards do usuário
  gerar: (data: GerarQuizInput) => api.post<ApiResponse<Quiz>>('/quizes/gerar', data),
}
