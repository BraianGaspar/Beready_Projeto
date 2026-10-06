import api from '@/core/services/api'
import type { Prompt, PromptInput } from '@/core/types'

export type { Prompt }

export const promptService = {
  // Listar prompts por usuário
  getByUsuario: (usuarioId: number) => {
    return api.get(`/prompts/usuario/${usuarioId}`)
  },

  // Buscar prompt por ID
  getById: (id: number) =>
    api.get(`/prompts/view/${id}`),

  // Criar prompt
  create: (data: PromptInput) => {
    return api.post('/prompts', data)
  },

  // Atualizar prompt
  update: (id: number, data: Partial<PromptInput>) =>
    api.put(`/prompts/edit/${id}`, data),

  // Deletar prompt
  delete: (id: number) =>
    api.delete(`/prompts/delete/${id}`),
}
