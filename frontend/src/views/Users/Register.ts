import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useForm } from '@/composables/useForm'
import { usePasswordStrength } from '@/composables/usePasswordStrength'

export function useRegister() {
  const router = useRouter()
  const loading = ref(false)
  const showPassword = ref(false)
  const showConfirmPassword = ref(false)
  const flashMessage = ref('')
  const flashType = ref('')

  const { strengthClass, strengthText, strengthWidth, checkPasswordStrength } =
    usePasswordStrength()

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

  const formatTelefone = (e: Event) => {
    const target = e.target as HTMLInputElement
    let value = target.value.replace(/\D/g, '')
    if (value.length <= 11) {
      if (value.length <= 2) value = value.replace(/^(\d{0,2})/, '($1')
      else if (value.length <= 7) value = value.replace(/^(\d{2})(\d{0,5})/, '($1) $2')
      else if (value.length <= 11) value = value.replace(/^(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3')
      target.value = value
      form.telefone = value
    }
  }

  const checkPasswordMatch = () => {}

  const handleSubmit = async () => {
    if (!passwordsMatch.value) {
      flashType.value = 'error'
      flashMessage.value = 'As senhas não coincidem'
      return
    }
    if (form.senha.length < 6) {
      flashType.value = 'error'
      flashMessage.value = 'A senha deve ter pelo menos 6 caracteres'
      return
    }

    loading.value = true
    flashMessage.value = ''

    try {
      const response = await fetch('http://localhost:8765/auth/register', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          nome: form.nome,
          email: form.email,
          senha: form.senha,
          telefone: form.telefone,
          nivel_ingles: form.nivel_ingles,
          idioma_preferido: form.idioma_preferido,
          objetivos_aprendizado: form.objetivos_aprendizado,
        }),
      })
      const data = await response.json()
      if (data.success) {
        flashType.value = 'success'
        flashMessage.value = 'Cadastro realizado com sucesso! Redirecionando...'
        setTimeout(() => router.push('/login'), 2000)
      } else {
        flashType.value = 'error'
        flashMessage.value = data.message || 'Erro ao cadastrar'
      }
    } catch (err) {
      console.error('Erro:', err)
      flashType.value = 'error'
      flashMessage.value = 'Erro de conexão com o servidor'
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
    flashMessage,
    flashType,
    strengthClass,
    strengthText,
    strengthWidth,
    passwordsMatch,
    formatTelefone,
    checkPasswordStrength,
    checkPasswordMatch,
    handleSubmit,
  }
}
