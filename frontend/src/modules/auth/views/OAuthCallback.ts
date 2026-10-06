import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '@/stores/auth'

/**
 * Callback do login social: o backend redireciona para
 * /oauth-callback?code=<código de uso único>. O código é trocado por uma
 * sessão via POST /auth/social/exchange (nenhum token trafega na URL).
 */
export function useOAuthCallback() {
  const router = useRouter()
  const route = useRoute()
  const authStore = useAuthStore()
  const { t } = useI18n()
  const error = ref<string | null>(null)
  const loading = ref(true)

  const handleCallback = async () => {
    const code = typeof route.query.code === 'string' ? route.query.code : ''

    // Remove o código da URL antes de qualquer outra coisa (histórico/referer)
    window.history.replaceState(window.history.state, '', route.path)

    if (!code) {
      loading.value = false
      error.value = t('login.socialAuthFailed')
      return
    }

    const result = await authStore.loginWithSocialCode(code)
    loading.value = false

    if (result.success) {
      await router.replace('/dashboard')
      return
    }

    error.value = result.message ?? t('oauth.processError')
  }

  const goToLogin = () => {
    router.push('/login')
  }

  onMounted(() => {
    handleCallback()
  })

  return {
    error,
    loading,
    goToLogin,
  }
}
