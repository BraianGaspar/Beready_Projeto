import { ref, computed, reactive, watch } from 'vue'
import { useRouter } from 'vue-router'
import api, { getApiErrorMessage } from '@/core/services/api'
import { useI18n } from 'vue-i18n'
import { useAlert } from '@/shared/composables/useAlert'
import { usePasswordStrength } from '@/shared/composables/usePasswordStrength'
import { usePhoneMask } from '@/shared/composables/usePhoneMask'

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

// Exportação principal da função useRegister
export function useRegister() {
  const router = useRouter()
  const { t } = useI18n()
  const { success, error } = useAlert()
  const loading = ref(false)

  const { strengthClass, strengthText, strengthWidth, checkPasswordStrength } =
    usePasswordStrength()
  const { phoneError, handlePhoneInput: maskPhoneInput, handlePhoneKeydown } = usePhoneMask()

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

  // Atualiza o indicador de força conforme a senha é digitada
  watch(
    () => form.senha,
    (senha) => checkPasswordStrength(senha),
  )

  // Mantém o v-model sincronizado com o valor mascarado
  const handlePhoneInput = (event: Event) => {
    form.telefone = maskPhoneInput(event)
  }

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
      const { data } = await api.post('/auth/register', {
        nome: form.nome,
        email: form.email,
        senha: form.senha,
        telefone: form.telefone,
        nivel_ingles: form.nivel_ingles || 'iniciante',
        idioma_preferido: form.idioma_preferido || 'pt-BR',
        objetivos_aprendizado: form.objetivos_aprendizado,
      })

      if (data.success) {
        success(t('register.success'))
        setTimeout(() => router.push('/login'), 2000)
      } else {
        error(data.message || t('register.error'))
      }
    } catch (err: unknown) {
      error(getApiErrorMessage(err) || t('register.error'))
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