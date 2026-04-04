import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { usePasswordStrength } from '@/composables/usePasswordStrength'

export function useResetPassword() {
  const router = useRouter()
  const route = useRoute()
  const loading = ref(false)
  const showPassword = ref(false)
  const showConfirmPassword = ref(false)
  const flashMessage = ref('')
  const flashType = ref('')

  const { strengthClass, strengthText, strengthWidth, checkPasswordStrength } =
    usePasswordStrength()

  const form = ref({
    senha: '',
    confirmar_senha: '',
    token: '',
  })

  const passwordsMatch = computed(() => form.value.senha === form.value.confirmar_senha)

  const checkPasswordMatch = () => {}

  const handleSubmit = async () => {
    if (!passwordsMatch.value) {
      flashType.value = 'error'
      flashMessage.value = 'As senhas não coincidem'
      return
    }
    if (form.value.senha.length < 6) {
      flashType.value = 'error'
      flashMessage.value = 'A senha deve ter pelo menos 6 caracteres'
      return
    }

    loading.value = true
    flashMessage.value = ''

    try {
      const response = await fetch(
        `http://localhost:8765/auth/reset-password/${form.value.token}`,
        {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ senha: form.value.senha }),
        },
      )
      const data = await response.json()
      if (data.success) {
        flashType.value = 'success'
        flashMessage.value = 'Senha redefinida com sucesso! Redirecionando...'
        setTimeout(() => router.push('/login'), 3000)
      } else {
        flashType.value = 'error'
        flashMessage.value = data.message || 'Erro ao redefinir senha'
      }
    } catch (err) {
      flashType.value = 'error'
      flashMessage.value = 'Erro de conexão com o servidor'
    } finally {
      loading.value = false
    }
  }

  onMounted(() => {
    form.value.token = (route.params.token as string) || (route.query.token as string)
    if (!form.value.token) {
      flashType.value = 'error'
      flashMessage.value = 'Token inválido ou expirado'
    }
  })

  return {
    form,
    loading,
    showPassword,
    showConfirmPassword,
    flashMessage,
    flashType,
    strengthClass,
    strengthText,
    strengthWidth,
    passwordsMatch,
    checkPasswordStrength,
    checkPasswordMatch,
    handleSubmit,
  }
}
