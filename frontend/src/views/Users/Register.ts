import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useForm } from '@/composables/useForm'
import { usePasswordStrength } from '@/composables/usePasswordStrength'
import { usePhoneMask } from '@/composables/usePhoneMask'
import { useAlert } from '@/composables/useAlert'

export function useRegister() {
  const router = useRouter()
  const { success, error } = useAlert()
  const loading = ref(false)
  const showPassword = ref(false)
  const showConfirmPassword = ref(false)

  const { strengthClass, strengthText, strengthWidth, checkPasswordStrength } =
    usePasswordStrength()
  const { handlePhoneInput, phoneError } = usePhoneMask()

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

  const passwordsMatch = computed(() => form.senha === form.confirmar_senha)

  const rules = {
    nome: (value: string) => (!value ? 'Nome é obrigatório' : null),
    email: (value: string) => {
      if (!value) return 'E-mail é obrigatório'
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) return 'E-mail inválido'
      return null
    },
    senha: (value: string) => {
      if (!value) return 'Senha é obrigatória'
      if (value.length < 6) return 'A senha deve ter pelo menos 6 caracteres'
      return null
    },
    confirmar_senha: (value: string) => {
      if (!value) return 'Confirmação de senha é obrigatória'
      if (form.senha !== value) return 'As senhas não coincidem'
      return null
    },
  }

  const checkPasswordMatch = () => {
    // Força a revalidação do campo confirmar_senha
    if (form.confirmar_senha) {
      validate(rules)
    }
  }

  const handleSubmit = async () => {
    // Validar telefone se foi preenchido
    if (form.telefone) {
      const digits = form.telefone.replace(/\D/g, '')
      if (digits.length > 0 && digits.length < 10) {
        error('Telefone deve ter pelo menos 10 dígitos')
        return
      }
    }

    if (!validate(rules)) return

    loading.value = true

    try {
      const response = await fetch('http://localhost:8765/auth/register', {
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
        success('Cadastro realizado com sucesso! Redirecionando...')
        setTimeout(() => router.push('/login'), 2000)
      } else {
        error(data.message || 'Erro ao cadastrar')
      }
    } catch (err) {
      console.error('Erro:', err)
      error('Erro de conexão com o servidor')
    } finally {
      loading.value = false
    }
  }

  return {
    form,
    errors,
    loading,
    showPassword,
    showConfirmPassword,
    strengthClass,
    strengthText,
    strengthWidth,
    phoneError,
    passwordsMatch,
    handlePhoneInput,
    checkPasswordStrength,
    checkPasswordMatch,
    handleSubmit,
  }
}
