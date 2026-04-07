import { defineStore } from 'pinia'
import { ref } from 'vue'
import { tagService, type Tag } from '@/services/tagService'

export const useTagStore = defineStore('tag', () => {
  const tags = ref<Tag[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  const fetchTags = async (usuarioId: number) => {
    loading.value = true
    error.value = null
    try {
      const response = await tagService.getByUsuario(usuarioId)
      tags.value = response.data.data
      return response.data.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Erro ao carregar tags'
      throw err
    } finally {
      loading.value = false
    }
  }

  const createTag = async (tag: Tag) => {
    loading.value = true
    try {
      const response = await tagService.create(tag)
      tags.value.unshift(response.data.data)
      return response.data.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Erro ao criar tag'
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateTag = async (id: number, data: Partial<Tag>) => {
    loading.value = true
    try {
      const response = await tagService.update(id, data)
      const index = tags.value.findIndex((t) => t.id === id)
      if (index !== -1) tags.value[index] = response.data.data
      return response.data.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Erro ao atualizar tag'
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteTag = async (id: number) => {
    loading.value = true
    try {
      await tagService.delete(id)
      tags.value = tags.value.filter((t) => t.id !== id)
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Erro ao deletar tag'
      throw err
    } finally {
      loading.value = false
    }
  }

  return { tags, loading, error, fetchTags, createTag, updateTag, deleteTag }
})
