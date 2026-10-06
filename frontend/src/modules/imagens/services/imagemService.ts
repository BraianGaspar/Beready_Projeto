import api from '@/core/services/api'
import type { Imagem, ApiResponse } from '@/core/types'

type ImagemInput = Omit<Imagem, 'id' | 'criado_em'>

// Todos os métodos devolvem o corpo da resposta ({ success, message, data })
export const imagemService = {
  getByPrompt: async (promptId: number): Promise<ApiResponse<Imagem[]>> =>
    (await api.get<ApiResponse<Imagem[]>>(`/imagens/prompt/${promptId}`)).data,

  getById: async (id: number): Promise<ApiResponse<Imagem>> =>
    (await api.get<ApiResponse<Imagem>>(`/imagens/view/${id}`)).data,

  create: async (data: ImagemInput): Promise<ApiResponse<Imagem>> =>
    (await api.post<ApiResponse<Imagem>>('/imagens', data)).data,

  update: async (id: number, data: Partial<ImagemInput>): Promise<ApiResponse<Imagem>> =>
    (await api.put<ApiResponse<Imagem>>(`/imagens/edit/${id}`, data)).data,

  delete: async (id: number): Promise<ApiResponse<null>> =>
    (await api.delete<ApiResponse<null>>(`/imagens/delete/${id}`)).data,
}
