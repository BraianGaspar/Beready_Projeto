import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAlert } from '@/shared/composables/useAlert'
import api, { getApiErrorMessage } from '@/core/services/api'
import { useI18n } from 'vue-i18n'

export function useForgotPassword() {
  const router = useRouter()
  const { success, error } = useAlert()
  const { t } = useI18n()
  const loading = ref(false)

  const form = ref({ email: '' })

  const handleSubmit = async () => {
    if (!form.value.email) {
      error(t('forgotPassword.emailRequired'))
      return
    }

    loading.value = true

    try {
      const { data } = await api.post('/auth/forgot-password', { email: form.value.email })

      if (data.success) {
        success(t('forgotPassword.successMessage'))
        setTimeout(() => router.push('/login'), 2000)
      } else {
        error(data.message || t('forgotPassword.errorMessage'))
      }
    } catch (err) {
      error(getApiErrorMessage(err) || t('errors.networkError'))
    } finally {
      loading.value = false
    }
  }

  return { form, loading, handleSubmit }
}
