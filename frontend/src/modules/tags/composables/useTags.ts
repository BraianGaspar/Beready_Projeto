import { ref } from 'vue'
import { tagService, type Tag } from '@/modules/tags/services/tagService'
import { useAlert } from '@/shared/composables/useAlert'
import { useI18n } from 'vue-i18n'

export function useTags() {
  const tags = ref<Tag[]>([])
  const loading = ref(false)
  const { success, error } = useAlert()
  const { t } = useI18n()

  const fetchTags = async (usuarioId: number) => {
    loading.value = true
    try {
      const response = await tagService.getByUsuario(usuarioId)
      tags.value = response.data.data || []
      return tags.value
    } catch (err: unknown) {
      const apiError = err as { response?: { data?: { message?: string } } }
      error(apiError.response?.data?.message || t('tags.errorLoad'))
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
      success(t('tags.successCreate'))
      return response.data.data
    } catch (err: unknown) {
      const apiError = err as { response?: { data?: { message?: string } } }
      error(apiError.response?.data?.message || t('tags.errorSave'))
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
      success(t('tags.successUpdate'))
      return response.data.data
    } catch (err: unknown) {
      const apiError = err as { response?: { data?: { message?: string } } }
      error(apiError.response?.data?.message || t('tags.errorSave'))
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
      success(t('tags.successDelete'))
    } catch (err: unknown) {
      const apiError = err as { response?: { data?: { message?: string } } }
      error(apiError.response?.data?.message || t('tags.errorDelete'))
      throw err
    } finally {
      loading.value = false
    }
  }

  return { tags, loading, fetchTags, createTag, updateTag, deleteTag }
}