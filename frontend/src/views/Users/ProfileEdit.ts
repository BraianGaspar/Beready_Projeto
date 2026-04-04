import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useForm } from '@/composables/useForm'
import { usePasswordStrength } from '@/composables/usePasswordStrength'

export function useProfileEdit() {
  const router = useRouter()
  const loading = ref(false)
  const showPassword = ref(false)
  const showConfirmPassword = ref(false)

  const { strengthClass, strengthText, strengthWidth, checkPasswordStrength } =
    usePasswordStrength()

  const { form, errors, validate } = useForm({
    id: '',
    nome: '',
    email: '',
    telefone: '',
    nivel_ingles: '',
    idioma_preferido: '',
    status: 'ativo',
    objetivos_aprendizado: '',
    nova_senha: '',
    confirmar_senha: '',
  })

  const passwordsMatch = computed(() => {
    if (!form.nova_senha && !form.confirmar_senha) return true
    return form.nova_senha === form.confirmar_senha
  })

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

  const loadUserData = () => {
    const userData = localStorage.getItem('user')
    if (userData) {
      try {
        const user = JSON.parse(userData)
        form.id = user.id
        form.nome = user.nome || ''
        form.email = user.email || ''
        form.telefone = user.telefone || ''
        form.nivel_ingles = user.nivel_ingles || ''
        form.idioma_preferido = user.idioma_preferido || ''
        form.status = user.status || 'ativo'
        form.objetivos_aprendizado = user.objetivos_aprendizado || ''
      } catch (e) {
        console.error(e)
      }
    }
  }

  const handleSubmit = async () => {
    if (form.nova_senha && form.nova_senha !== form.confirmar_senha) {
      alert('As senhas não coincidem')
      return
    }
    if (form.nova_senha && form.nova_senha.length < 6) {
      alert('A senha deve ter pelo menos 6 caracteres')
      return
    }

    loading.value = true
    try {
      const submitData: any = {
        nome: form.nome,
        email: form.email,
        telefone: form.telefone,
        nivel_ingles: form.nivel_ingles,
        idioma_preferido: form.idioma_preferido,
        status: form.status,
        objetivos_aprendizado: form.objetivos_aprendizado,
      }
      if (form.nova_senha) submitData.senha = form.nova_senha

      const response = await fetch(`http://localhost:8765/users/${form.id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(submitData),
      })
      const data = await response.json()
      if (data.success) {
        localStorage.setItem('user', JSON.stringify(data.user))
        alert('Perfil atualizado com sucesso!')
        router.push('/profile')
      } else {
        alert(data.message || 'Erro ao atualizar perfil')
      }
    } catch (err) {
      alert('Erro de conexão com o servidor')
    } finally {
      loading.value = false
    }
  }

  onMounted(loadUserData)

  return {
    form,
    errors,
    loading,
    showPassword,
    showConfirmPassword,
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
