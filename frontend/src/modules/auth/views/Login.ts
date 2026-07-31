import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useForm } from '@/shared/composables/useForm'
import { useAlert } from '@/shared/composables/useAlert'
import { auth } from '@/core/services/api'

const API_BASE_URL = import.meta.env.VITE_API_URL
const RECAPTCHA_SITE_KEY = import.meta.env.VITE_RECAPTCHA_SITE_KEY
const RECAPTCHA_JS_URL = import.meta.env.VITE_RECAPTCHA_JS_URL

export function useLogin() {
  const router = useRouter()
  const { success, error, clearAllAlerts } = useAlert()
  const loading = ref(false)

  const { form, errors, validate } = useForm({ email: '', password: '' })

  const rules = {
    email: (value: string) => (!value ? 'E-mail é obrigatório' : null),
    password: (value: string) => {
      if (!value) return 'Senha é obrigatória'
      if (value.length < 6) return 'A senha deve ter pelo menos 6 caracteres'
      return null
    },
  }

  // Carrega o script do reCAPTCHA
  const loadRecaptcha = () => {
    if (!RECAPTCHA_SITE_KEY) {
      console.warn('VITE_RECAPTCHA_SITE_KEY não configurado no .env')
      return
    }

    if (document.querySelector('script[src*="recaptcha"]')) {
      return
    }

    const script = document.createElement('script')
    script.src = `${RECAPTCHA_JS_URL}?render=${RECAPTCHA_SITE_KEY}`
    script.async = true
    script.defer = true
    document.head.appendChild(script)
  }

  // Obtém o token do reCAPTCHA
  const getRecaptchaToken = (): Promise<string> => {
    return new Promise((resolve, reject) => {
      if (!RECAPTCHA_SITE_KEY) {
        reject(new Error('reCAPTCHA não configurado'))
        return
      }

      if (typeof window === 'undefined' || !window.grecaptcha) {
        reject(new Error('reCAPTCHA não carregado. Recarregue a página.'))
        return
      }

      window.grecaptcha.execute(RECAPTCHA_SITE_KEY, { action: 'login' }).then(resolve).catch(reject)
    })
  }

  const handleSubmit = async () => {
    if (!validate(rules)) return

    loading.value = true

    try {
      let recaptchaToken = ''
      try {
        recaptchaToken = await getRecaptchaToken()
      } catch (err) {
        error((err as Error).message || 'Erro ao carregar verificação de segurança')
        loading.value = false
        return
      }

      const response = await auth.login({
        email: form.email,
        password: form.password,
        recaptcha_token: recaptchaToken,
      })

      if (response.success) {
        success('Login realizado com sucesso!')
        setTimeout(() => {
          clearAllAlerts()
          router.push('/dashboard')
        }, 500)
      } else {
        error(response.message || 'E-mail ou senha inválidos')
      }
    } catch (err) {
      console.error('Erro:', err)
      error('Erro de conexão com o servidor. Verifique se o backend está rodando.')
    } finally {
      loading.value = false
    }
  }

  const loginWithProvider = (provider: string) => {
    loading.value = true

    const width = 500
    const height = 600
    const left = window.screen.width / 2 - width / 2
    const top = window.screen.height / 2 - height / 2

    const popup = window.open(
      `${API_BASE_URL}/auth/login/${provider}`,
      `Login ${provider}`,
      `width=${width},height=${height},left=${left},top=${top}`,
    )

    if (!popup) {
      loading.value = false
      error('Popup bloqueado! Permita popups para este site.')
      return
    }

    const interval = setInterval(() => {
      if (popup.closed) {
        clearInterval(interval)
        loading.value = false

        const userData = localStorage.getItem('user')
        const token = localStorage.getItem('access_token')

        if (userData && token) {
          window.location.href = '/dashboard'
        }
      }
    }, 500)
  }

  onMounted(() => {
    loadRecaptcha()
  })

  return {
    form,
    errors,
    loading,
    handleSubmit,
    loginWithProvider,
  }
}
