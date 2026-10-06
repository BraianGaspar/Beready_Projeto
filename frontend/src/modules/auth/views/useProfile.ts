// src/modules/auth/views/useProfile.ts
import { ref, onMounted, computed, onUnmounted } from 'vue'
import { useAlert } from '@/shared/composables/useAlert'
import api, { getApiErrorMessage } from '@/core/services/api'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '@/stores/auth'
import { formatPhone } from '@/shared/composables/usePhoneMask'

export function useProfile() {
  const authStore = useAuthStore()
  const { success, error } = useAlert()
  const { t } = useI18n()
  const user = computed(() => authStore.user)
  const showDeleteModal = ref(false)
  const confirmEmail = ref('')
  const deleteLoading = ref(false)

  const formattedPhone = computed(() => {
    if (!user.value?.telefone) return ''
    return formatPhone(user.value.telefone)
  })

  const getNivelIngles = (nivel?: string) => {
    const niveis: Record<string, string> = {
      iniciante: t('profile.nivelIniciante'),
      intermediario: t('profile.nivelIntermediario'),
      avancado: t('profile.nivelAvancado'),
    }
    return (nivel && niveis[nivel]) || nivel || t('profile.naoInformado')
  }

  const getIdiomaPreferido = (idioma?: string) => {
    const idiomas: Record<string, string> = {
      'pt-BR': t('idiomas.pt'),
      en: t('idiomas.en'),
      es: t('idiomas.es'),
      fr: t('idiomas.fr'),
    }
    return (idioma && idiomas[idioma]) || idioma || t('profile.naoInformado')
  }

  const handleDeleteAccount = async () => {
    if (confirmEmail.value !== user.value?.email) {
      error(t('profile.emailMismatch'))
      return
    }
    const currentUser = user.value
    if (!currentUser) return

    deleteLoading.value = true
    try {
      const response = await api.delete(`/users/delete/${currentUser.id}`)
      if (response.data.success) {
        success(t('profile.deleteSuccess'))
        // Encerra a sessão (limpa cookie e stores) e leva ao cadastro
        setTimeout(() => authStore.logout({ redirectTo: '/register' }), 2000)
      } else {
        error(response.data.message || t('profile.deleteError'))
        setTimeout(() => {
          showDeleteModal.value = false
          confirmEmail.value = ''
        }, 1500)
      }
    } catch (err: unknown) {
      console.error('Erro ao excluir:', err)
      error(getApiErrorMessage(err) || t('errors.networkError'))
      setTimeout(() => {
        showDeleteModal.value = false
        confirmEmail.value = ''
      }, 1500)
    } finally {
      deleteLoading.value = false
      showDeleteModal.value = false
      confirmEmail.value = ''
    }
  }

  // Atualiza o usuário do store com os dados mais recentes do servidor
  const loadUserData = async () => {
    if (!authStore.isAuthenticated) return
    try {
      await authStore.fetchMe()
    } catch (e) {
      console.error('Erro ao carregar usuário:', e)
    }
  }

  const handleVisibilityChange = () => {
    if (document.visibilityState === 'visible') {
      loadUserData()
    }
  }

  onMounted(() => {
    loadUserData()
    document.addEventListener('visibilitychange', handleVisibilityChange)
  })

  onUnmounted(() => {
    document.removeEventListener('visibilitychange', handleVisibilityChange)
  })

  return {
    user,
    formattedPhone,
    showDeleteModal,
    confirmEmail,
    deleteLoading,
    getNivelIngles,
    getIdiomaPreferido,
    handleDeleteAccount,
  }
}
