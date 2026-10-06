import api from '@/core/services/api'

export interface CadastroRespostaPayload {
  // Ignorado pelo backend (usa o usuário do token); mantido por compatibilidade
  usuario_id: number
  tipo: 'flashcard' | 'quiz'
  referencia_id: number
  correto: boolean
}

export const respostaService = {
  // POST /respostas
  async registrarResposta(payload: CadastroRespostaPayload): Promise<void> {
    await api.post('/respostas', payload)
  },
}
