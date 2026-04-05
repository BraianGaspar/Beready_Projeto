import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useForm } from '@/composables/useForm'
import { usePasswordStrength } from '@/composables/usePasswordStrength'
import { usePhoneMask } from '@/composables/usePhoneMask'
import { useAlert } from '@/composables/useAlert'

export function useProfileEdit() {
  const router = useRouter()
  const { success, error } = useAlert()
  const loading = ref(false)
  const showPassword = ref(false)
  const showConfirmPassword = ref(false)
  const userId = ref<number | null>(null)

  const { strengthClass, strengthText, strengthWidth, checkPasswordStrength } =
    usePasswordStrength()
  const { handlePhoneInput, phoneError } = usePhoneMask()

  const { form, errors, validate } = useForm({
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

  const checkPasswordMatch = () => {
    // Força a reatividade
    passwordsMatch.value
  }

  const loadUserData = async () => {
    const userData = localStorage.getItem('user')
    if (!userData) {
      router.push('/login')
      return
    }

    try {
      const user = JSON.parse(userData)
      userId.value = user.id

      console.log('User ID carregado:', userId.value) // 🔥 DEBUG

      // Busca os dados atualizados do backend
      const response = await fetch(`http://localhost:8765/users/${user.id}`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
        },
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          form.nome = data.user.nome || ''
          form.email = data.user.email || ''
          form.telefone = data.user.telefone || ''
          form.nivel_ingles = data.user.nivel_ingles || 'iniciante'
          form.idioma_preferido = data.user.idioma_preferido || 'pt-BR'
          form.status = data.user.status || 'ativo'
          form.objetivos_aprendizado = data.user.objetivos_aprendizado || ''
        }
      } else {
        // Fallback para dados do localStorage
        form.nome = user.nome || ''
        form.email = user.email || ''
        form.telefone = user.telefone || ''
        form.nivel_ingles = user.nivel_ingles || 'iniciante'
        form.idioma_preferido = user.idioma_preferido || 'pt-BR'
        form.status = user.status || 'ativo'
        form.objetivos_aprendizado = user.objetivos_aprendizado || ''
      }
    } catch (e) {
      console.error('Erro ao carregar dados do usuário:', e)
      const user = JSON.parse(userData)
      userId.value = user.id // 🔥 Garante que o ID é setado mesmo em erro
      form.nome = user.nome || ''
      form.email = user.email || ''
      form.telefone = user.telefone || ''
      form.nivel_ingles = user.nivel_ingles || 'iniciante'
      form.idioma_preferido = user.idioma_preferido || 'pt-BR'
      form.status = user.status || 'ativo'
      form.objetivos_aprendizado = user.objetivos_aprendizado || ''
    }
  }

  const handleSubmit = async () => {
    console.log('Submit - userId.value:', userId.value) // 🔥 DEBUG

    // 🔥 PEGA O ID DIRETAMENTE DO LOCALSTORAGE SE userId.value estiver vazio
    let currentUserId = userId.value

    if (!currentUserId) {
      const userData = localStorage.getItem('user')
      if (userData) {
        const user = JSON.parse(userData)
        currentUserId = user.id
        userId.value = currentUserId
        console.log('ID recuperado do localStorage:', currentUserId)
      }
    }

    if (!currentUserId) {
      error('ID do usuário não encontrado. Faça login novamente.')
      router.push('/login')
      return
    }

    // Validar telefone se foi alterado
    if (form.telefone) {
      const digits = form.telefone.replace(/\D/g, '')
      if (digits.length > 0 && digits.length < 10) {
        error('Telefone deve ter pelo menos 10 dígitos')
        return
      }
    }

    // Validar senha se foi preenchida
    if (form.nova_senha) {
      if (form.nova_senha.length < 6) {
        error('A nova senha deve ter pelo menos 6 caracteres')
        return
      }
      if (form.nova_senha !== form.confirmar_senha) {
        error('As senhas não coincidem')
        return
      }
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

      if (form.nova_senha) {
        submitData.senha = form.nova_senha
      }

      const url = `http://localhost:8765/users/${currentUserId}`
      console.log('Enviando PUT para:', url)
      console.log('Dados:', submitData)

      const response = await fetch(url, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
        },
        body: JSON.stringify(submitData),
      })

      console.log('Response status:', response.status)
      const data = await response.json()
      console.log('Response data:', data)

      if (response.ok && data.success) {
        // Atualiza o localStorage com os novos dados
        const currentUser = JSON.parse(localStorage.getItem('user') || '{}')
        const updatedUser = {
          ...currentUser,
          nome: form.nome,
          email: form.email,
          telefone: form.telefone,
          nivel_ingles: form.nivel_ingles,
          idioma_preferido: form.idioma_preferido,
          status: form.status,
          objetivos_aprendizado: form.objetivos_aprendizado,
        }
        localStorage.setItem('user', JSON.stringify(updatedUser))

        success('Perfil atualizado com sucesso!')
        setTimeout(() => router.push('/profile'), 1500)
      } else {
        error(data.message || 'Erro ao atualizar perfil')
      }
    } catch (err) {
      console.error('Erro detalhado:', err)
      error('Erro de conexão com o servidor')
    } finally {
      loading.value = false
    }
  }

  onMounted(() => {
    loadUserData()
  })

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
