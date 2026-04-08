import api from '@/core/services/api'

export interface Prompt {
  id?: number
  usuario_id: number
  texto_original: string
  idioma_original?: string
  contexto?: string
  sessao_id?: string
  criado_em?: string
}

export const promptService = {
  // Listar prompts por usuário
  getByUsuario: (usuarioId: number) => {
    console.log('getByUsuario chamado com ID:', usuarioId)
    return api.get(`/prompts/usuario/${usuarioId}`)
  },

  // Buscar prompt por ID
  getById: (id: number) =>
    api.get(`/prompts/view/${id}`),

  // Criar prompt
  create: (data: Prompt) => {
    console.log('create prompt com dados:', data)
    return api.post('/prompts', data)
  },

  // Atualizar prompt
  update: (id: number, data: Partial<Prompt>) =>
    api.put(`/prompts/edit/${id}`, data),

  // Deletar prompt
  delete: (id: number) =>
    api.delete(`/prompts/delete/${id}`),
}