import api from '@/core/services/api'
import type { Tag, TagInput } from '@/core/types'

export type { Tag, TagInput }

export const tagService = {
  getAll: () => api.get('/tags'),
  getByUsuario: (usuarioId: number) => api.get(`/tags/usuario/${usuarioId}`),
  getById: (id: number) => api.get(`/tags/view/${id}`),
  create: (data: TagInput) => api.post('/tags', data),
  update: (id: number, data: Partial<TagInput>) => api.put(`/tags/edit/${id}`, data),
  delete: (id: number) => api.delete(`/tags/delete/${id}`),
}
