import { ref, computed, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { API_BASE_URL } from '@/shared/config/env'
import { useI18n } from 'vue-i18n'

// Tipos
type ValidationRule = (value: string) => string | null
type ValidationRules = Record<string, ValidationRule>

// Interface para o formulário
interface RegisterForm {
  nome: string
  email: string
  telefone: string
  senha: string
  confirmar_senha: string
  nivel_ingles: string
  idioma_preferido: string
  objetivos_aprendizado: string
}

// Interface para erros
interface RegisterErrors {
  nome: string
  email: string
  senha: string
  confirmar_senha: string
}

function usePasswordStrength() {
  const { t } = useI18n()
  const strengthClass = ref('')
  const strengthText = ref('')
  const strengthWidth = ref('0%')

  const checkPasswordStrength = (password: string) => {
    let score = 0

    if (!password) {
      strengthClass.value = ''
      strengthText.value = ''
      strengthWidth.value = '0%'
      return
    }

    if (password.length >= 6) score += 1
    if (password.length >= 8) score += 1
    if (/[A-Z]/.test(password)) score += 1
    if (/[a-z]/.test(password)) score += 1
    if (/[0-9]/.test(password)) score += 1
    if (/[^A-Za-z0-9]/.test(password)) score += 1

    if (score <= 2) {
      strengthText.value = t('passwordStrength.weak')
      strengthClass.value = 'weak'
      strengthWidth.value = '25%'
    } else if (score <= 4) {
      strengthText.value = t('passwordStrength.medium')
      strengthClass.value = 'medium'
      strengthWidth.value = '50%'
    } else if (score <= 6) {
      strengthText.value = t('passwordStrength.strong')
      strengthClass.value = 'strong'
      strengthWidth.value = '75%'
    } else {
      strengthText.value = t('passwordStrength.veryStrong')
      strengthClass.value = 'very-strong'
      strengthWidth.value = '100%'
    }
  }

  return { strengthClass, strengthText, strengthWidth, checkPasswordStrength }
}

function usePhoneMask() {
  const { t } = useI18n() // ADICIONADO
  const phoneError = ref('')

  const handlePhoneInput = (event: Event) => {
    const input = event.target as HTMLInputElement
    let value = input.value.replace(/\D/g, '')

    if (value.length > 11) {
      value = value.slice(0, 11)
    }

    let formatted = value
    if (value.length === 11) {
      formatted = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3')
    } else if (value.length === 10) {
      formatted = value.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3')
    } else if (value.length > 6) {
      formatted = value.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3')
    } else if (value.length > 2) {
      formatted = value.replace(/(\d{2})(\d{0,5})/, '($1) $2')
    } else {
      formatted = value
    }

    input.value = formatted

    // CORRIGIDO: usando tradução
    if (value.length > 0 && value.length < 11) {
      phoneError.value = t('register.phoneInvalid')
    } else {
      phoneError.value = ''
    }

    return formatted
  }

  const handlePhoneKeydown = (event: KeyboardEvent) => {
    const allowedKeys = [
      'Backspace',
      'Delete',
      'ArrowLeft',
      'ArrowRight',
      'Tab',
      'Escape',
      'Enter',
      'Home',
      'End',
    ]
    if (allowedKeys.includes(event.key)) return
    if (!/^[0-9]$/.test(event.key)) {
      event.preventDefault()
    }
  }

  return { phoneError, handlePhoneInput, handlePhoneKeydown }
}

function useAlert() {
  const showAlert = ref(false)
  const alertMessage = ref('')
  const alertType = ref<'success' | 'error' | 'warning' | 'info'>('success')

  const success = (message: string) => {
    alertMessage.value = message
    alertType.value = 'success'
    showAlert.value = true
    setTimeout(() => {
      showAlert.value = false
    }, 3000)
  }

  const error = (message: string) => {
    alertMessage.value = message
    alertType.value = 'error'
    showAlert.value = true
    setTimeout(() => {
      showAlert.value = false
    }, 3000)
  }

  const warning = (message: string) => {
    alertMessage.value = message
    alertType.value = 'warning'
    showAlert.value = true
    setTimeout(() => {
      showAlert.value = false
    }, 3000)
  }

  const info = (message: string) => {
    alertMessage.value = message
    alertType.value = 'info'
    showAlert.value = true
    setTimeout(() => {
      showAlert.value = false
    }, 3000)
  }

  return { showAlert, alertMessage, alertType, success, error, warning, info }
}

// Exportação principal da função useRegister
export function useRegister() {
  const router = useRouter()
  const { t } = useI18n()
  const { success, error } = useAlert()
  const loading = ref(false)

  const { strengthClass, strengthText, strengthWidth, checkPasswordStrength } =
    usePasswordStrength()
  const {
    phoneError,
    handlePhoneInput,
    handlePhoneKeydown,
  } = usePhoneMask()

  // Formulário reativo
  const form = reactive<RegisterForm>({
    nome: '',
    email: '',
    telefone: '',
    senha: '',
    confirmar_senha: '',
    nivel_ingles: 'iniciante',
    idioma_preferido: 'pt-BR',
    objetivos_aprendizado: '',
  })

  const errors = reactive<RegisterErrors>({
    nome: '',
    email: '',
    senha: '',
    confirmar_senha: '',
  })

  const passwordsMatch = computed(() => form.senha === form.confirmar_senha)

  // Opções para selects com tradução
  const nivelOptions = [
    { value: 'iniciante', label: t('common.iniciante') },
    { value: 'intermediario', label: t('common.intermediario') },
    { value: 'avancado', label: t('common.avancado') },
  ]

  const idiomaOptions = [
    { value: 'pt-BR', label: t('idiomas.pt') },
    { value: 'en', label: t('idiomas.en') },
    { value: 'es', label: t('idiomas.es') },
    { value: 'fr', label: t('idiomas.fr') },
    { value: 'de', label: t('idiomas.de') },
    { value: 'it', label: t('idiomas.it') },
  ]

  const validateForm = (): boolean => {
    let valid = true

    if (!form.nome.trim()) {
      errors.nome = t('register.nomeRequired')
      valid = false
    } else {
      errors.nome = ''
    }

    if (!form.email.trim()) {
      errors.email = t('register.emailRequired')
      valid = false
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
      errors.email = t('register.emailInvalid')
      valid = false
    } else {
      errors.email = ''
    }

    if (!form.senha) {
      errors.senha = t('register.passwordRequired')
      valid = false
    } else if (form.senha.length < 6) {
      errors.senha = t('passwordValidation.minLength')
      valid = false
    } else {
      errors.senha = ''
    }

    if (form.senha !== form.confirmar_senha) {
      errors.confirmar_senha = t('passwordValidation.doNotMatch')
      valid = false
    } else {
      errors.confirmar_senha = ''
    }

    // Validar telefone - já usa tradução via phoneError
    const digits = form.telefone.replace(/\D/g, '')
    if (digits.length > 0 && digits.length < 11) {
      // phoneError já foi definido pelo handlePhoneInput
      valid = false
    } else {
      phoneError.value = ''
    }

    return valid
  }

  const handleSubmit = async () => {
    if (!validateForm()) return

    loading.value = true

    try {
      const response = await fetch(`${API_BASE_URL}/auth/register`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
        },
        body: JSON.stringify({
          nome: form.nome,
          email: form.email,
          senha: form.senha,
          telefone: form.telefone,
          nivel_ingles: form.nivel_ingles || 'iniciante',
          idioma_preferido: form.idioma_preferido || 'pt-BR',
          objetivos_aprendizado: form.objetivos_aprendizado,
        }),
      })

      const data = await response.json()

      if (response.ok && data.success) {
        success(t('register.success'))
        setTimeout(() => router.push('/login'), 2000)
      } else {
        error(data.message || t('register.error'))
      }
    } catch (err: unknown) {
      console.error('Erro:', err)
      const errorMessage = err && typeof err === 'object' && 'message' in err
        ? String((err as { message: string }).message)
        : t('errors.networkError')
      error(errorMessage)
    } finally {
      loading.value = false
    }
  }

  return {
    form,
    errors,
    loading,
    strengthClass,
    strengthText,
    strengthWidth,
    phoneError,
    passwordsMatch,
    nivelOptions,
    idiomaOptions,
    handlePhoneInput,
    handlePhoneKeydown,
    checkPasswordStrength,
    handleSubmit,
  }
}