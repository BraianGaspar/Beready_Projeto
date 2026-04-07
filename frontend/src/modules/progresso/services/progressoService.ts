import api from '@/core/services/api'

export interface Progresso {
  id?: number
  usuario_id: number
  vocabulario_aprendido?: number
  flashcards_concluidos?: number
  quizes_concluidos?: number
  tempo_total_estudo?: number
  sequencia_atual?: number
  maior_sequencia?: number
  ultima_atividade?: string
  progresso_nivel?: any
}

export const progressoService = {
  getByUsuario: (usuarioId: number) => api.get(`/progresso/usuario/${usuarioId}`),
  save: (data: Progresso) => api.post('/progresso', data),
}
