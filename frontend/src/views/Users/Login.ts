import { useAuth } from '@/composables/useAuth'
import { useForm } from '@/composables/useForm'

export function useLogin() {
  const { login, loading } = useAuth()
  const { form, errors, validate } = useForm({ email: '', password: '' })

  const rules = {
    email: (value: string) => (!value ? 'E-mail é obrigatório' : null),
    password: (value: string) => (!value ? 'Senha é obrigatória' : null),
  }

  const handleSubmit = async () => {
    if (!validate(rules)) return
    await login(form.email, form.password)
  }

  return {
    form,
    errors,
    loading,
    handleSubmit,
  }
}
