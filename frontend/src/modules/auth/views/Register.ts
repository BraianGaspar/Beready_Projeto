import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { API_BASE_URL } from '@/shared/config/env'
import { useI18n } from 'vue-i18n'

// Tipos
type ValidationRule = (value: string) => string | null
type ValidationRules = Record<string, ValidationRule>
type FormData = Record<string, string | number | boolean | null | undefined>

// Composables internos para evitar erros de import
function useForm(initialData: FormData) {
  const form = ref(initialData)
  const errors = ref<Record<string, string>>({})

  const validate = (rules: ValidationRules) => {
    let isValid = true
    const newErrors: Record<string, string> = {}

    for (const field in rules) {
      const rule = rules[field]
      if (rule) {
        const error = rule(String(form.value[field] ?? ''))
        if (error) {
          newErrors[field] = error
          isValid = false
        }
      }
    }

    errors.value = newErrors
    return isValid
  }

  const resetForm = () => {
    form.value = { ...initialData }
    errors.value = {}
  }

  return { form, errors, validate, resetForm }
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
  const phoneError = ref('')

  const formatPhone = (value: string) => {
    const digits = value.replace(/\D/g, '')
    if (digits.length === 11) {
      return digits.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3')
    }
    if (digits.length === 10) {
      return digits.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3')
    }
    return value
  }

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

    if (value.length > 0 && value.length < 11) {
      phoneError.value = 'Telefone deve ter 10 ou 11 dígitos'
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

  return { phoneError, handlePhoneInput, handlePhoneKeydown, formatPhone }
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
  const showPassword = ref(false)
  const showConfirmPassword = ref(false)

  const { strengthClass, strengthText, strengthWidth, checkPasswordStrength } =
    usePasswordStrength()
  const {
    phoneError,
    handlePhoneInput: handlePhoneMaskInput,
    handlePhoneKeydown: handlePhoneMaskKeydown,
  } = usePhoneMask()

  const { form, errors, validate } = useForm({
    nome: '',
    email: '',
    telefone: '',
    senha: '',
    confirmar_senha: '',
    nivel_ingles: '',
    idioma_preferido: '',
    objetivos_aprendizado: '',
  })

  const passwordsMatch = computed(() => form.value.senha === form.value.confirmar_senha)

  const rules = {
    nome: (value: string) => (!value ? t('register.nomeRequired') : null),
    email: (value: string) => {
      if (!value) return t('register.emailRequired')
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) return t('register.emailInvalid')
      return null
    },
    senha: (value: string) => {
      if (!value) return t('register.passwordRequired')
      if (value.length < 6) return t('passwordValidation.minLength')
      return null
    },
    confirmar_senha: (value: string) => {
      if (!value) return t('register.confirmPasswordRequired')
      if (form.value.senha !== value) return t('passwordValidation.doNotMatch')
      return null
    },
  }

  const checkPasswordMatch = () => {
    if (form.value.confirmar_senha) {
      validate(rules)
    }
  }

  const handleSubmit = async () => {
    // Função auxiliar para garantir que o telefone seja string
    const getTelefoneAsString = (): string => {
      const telefone = form.value.telefone
      if (typeof telefone === 'string') return telefone
      if (typeof telefone === 'number') return String(telefone)
      return ''
    }

    const telefoneStr = getTelefoneAsString()
    if (telefoneStr) {
      const digits = telefoneStr.replace(/\D/g, '')
      if (digits.length > 0 && digits.length < 11) {
        error(t('register.phoneInvalid'))
        return
      }
    }

    if (!validate(rules)) return

    loading.value = true

    try {
      const response = await fetch(`${API_BASE_URL}/auth/register`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
        },
        body: JSON.stringify({
          nome: form.value.nome,
          email: form.value.email,
          senha: form.value.senha,
          telefone: telefoneStr,
          nivel_ingles: form.value.nivel_ingles || 'iniciante',
          idioma_preferido: form.value.idioma_preferido || 'pt-BR',
          objetivos_aprendizado: form.value.objetivos_aprendizado,
        }),
      })

      const data = await response.json()

      if (response.ok && data.success) {
        success(t('register.success'))
        setTimeout(() => router.push('/login'), 2000)
      } else {
        error(data.message || t('register.error'))
      }
    } catch (err) {
      console.error('Erro:', err)
      error(t('errors.networkError'))
    } finally {
      loading.value = false
    }
  }

  return {
    form: form.value,
    errors: errors.value,
    loading,
    showPassword,
    showConfirmPassword,
    strengthClass,
    strengthText,
    strengthWidth,
    phoneError,
    passwordsMatch,
    handlePhoneInput: handlePhoneMaskInput,
    handlePhoneKeydown: handlePhoneMaskKeydown,
    checkPasswordStrength,
    checkPasswordMatch,
    handleSubmit,
  }
}