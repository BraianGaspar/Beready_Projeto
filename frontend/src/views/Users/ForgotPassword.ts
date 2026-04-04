import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useForm } from '@/composables/useForm'

export function useForgotPassword() {
  const router = useRouter()
  const loading = ref(false)
  const flashMessage = ref('')
  const flashType = ref('')

  const { form, errors, validate } = useForm({ email: '' })

  const rules = {
    email: (value: string) => (!value ? 'E-mail é obrigatório' : null),
  }

  const handleSubmit = async () => {
    if (!validate(rules)) return

    loading.value = true
    flashMessage.value = ''

    try {
      const response = await fetch('http://localhost:8765/auth/forgot-password', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: form.email }),
      })
      const data = await response.json()

      if (data.success) {
        flashType.value = 'success'
        flashMessage.value = data.message || 'Link de recuperação enviado!'
        setTimeout(() => router.push('/login'), 3000)
      } else {
        flashType.value = 'error'
        flashMessage.value = data.message || 'Erro ao enviar link'
      }
    } catch (err) {
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
    flashMessage,
    flashType,
    handleSubmit,
  }
}
