import { ref, onMounted } from 'vue'
import { useTags as useTagsComposable } from '@/composables/useTags'
import { useAuth } from '@/composables/useAuth'

export function useTags() {
  const { tags, loading, fetchTags, createTag, updateTag, deleteTag } = useTagsComposable()
  const { user } = useAuth()

  const modalOpen = ref(false)
  const saving = ref(false)
  const editingTag = ref<any>(null)

  const form = ref({
    nome: '',
    cor: '#4CAF50',
    descricao: '',
  })

  onMounted(async () => {
    if (user.value?.id) {
      await fetchTags(user.value.id)
    }
  })

  const openModal = () => {
    editingTag.value = null
    form.value = { nome: '', cor: '#4CAF50', descricao: '' }
    modalOpen.value = true
  }

  const editTag = (tag: any) => {
    editingTag.value = tag
    form.value = { ...tag }
    modalOpen.value = true
  }

  const closeModal = () => {
    modalOpen.value = false
  }

  const saveTag = async () => {
    saving.value = true
    try {
      if (editingTag.value) {
        await updateTag(editingTag.value.id, form.value)
      } else {
        await createTag({ ...form.value, criado_por: user.value!.id })
      }
      await fetchTags(user.value!.id)
      closeModal()
    } finally {
      saving.value = false
    }
  }

  const confirmDelete = async (tag: any) => {
    if (confirm(`Tem certeza que deseja excluir a tag "${tag.nome}"?`)) {
      await deleteTag(tag.id)
      await fetchTags(user.value!.id)
    }
  }

  return {
    tags,
    loading,
    modalOpen,
    saving,
    editingTag,
    form,
    openModal,
    editTag,
    closeModal,
    saveTag,
    confirmDelete,
  }
}
