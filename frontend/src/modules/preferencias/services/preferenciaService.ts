import api from '@/core/services/api'

export interface Preferencia {
  id?: number
  usuario_id: number
  tema: 'claro' | 'escuro'
  modo_daltonico: boolean
  notificacoes_ativas: boolean
  som_ativo: boolean
  traducao_automatica: boolean
  preferencia_dificuldade: 'iniciante' | 'intermediario' | 'avancado' | 'adaptativo'
  meta_diaria_minutos: number
}

export const preferenciaService = {
  getByUsuario: (usuarioId: number) => api.get(`/preferencias/usuario/${usuarioId}`),
  save: (data: Preferencia) => api.post('/preferencias', data),
}