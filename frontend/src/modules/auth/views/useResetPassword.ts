import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAlert } from '@/shared/composables/useAlert'
import api, { getApiErrorMessage } from '@/core/services/api'
import { useI18n } from 'vue-i18n'

export function useResetPassword() {
  const router = useRouter()
  const route = useRoute()
  const { success, error } = useAlert()
  const { t } = useI18n()
  const loading = ref(false)

  const form = ref({
    senha: '',
    confirmar_senha: '',
    token: '',
  })

  const handleSubmit = async () => {
    if (!form.value.senha) {
      error(t('resetPassword.passwordRequired'))
      return
    }
    if (form.value.senha.length < 6) {
      error(t('passwordValidation.minLength'))
      return
    }
    if (form.value.senha !== form.value.confirmar_senha) {
      error(t('passwordValidation.doNotMatch'))
      return
    }

    loading.value = true

    try {
      const { data } = await api.post(`/auth/reset-password/${form.value.token}`, {
        senha: form.value.senha,
      })

      if (data.success) {
        success(t('resetPassword.successMessage'))
        setTimeout(() => router.push('/login'), 2000)
      } else {
        error(data.message || t('resetPassword.errorMessage'))
      }
    } catch (err) {
      error(getApiErrorMessage(err) || t('errors.networkError'))
    } finally {
      loading.value = false
    }
  }

  onMounted(() => {
    form.value.token = (route.params.token as string) || (route.query.token as string)
    if (!form.value.token) {
      error(t('resetPassword.invalidToken'))
    }
  })

  return {
    form,
    loading,
    handleSubmit,
  }
}
