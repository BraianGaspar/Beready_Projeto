import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAlert } from '@/composables/useAlert'

export function useProfile() {
  const router = useRouter()
  const { success, error, clearAllAlerts } = useAlert()
  const user = ref<any>(null)
  const showDeleteModal = ref(false)
  const confirmEmail = ref('')
  const deleteLoading = ref(false)

  const getNivelIngles = (nivel: string) => {
    const niveis: Record<string, string> = {
      iniciante: 'Iniciante',
      intermediario: 'Intermediário',
      avancado: 'Avançado',
    }
    return niveis[nivel] || nivel || 'Não informado'
  }

  const getIdiomaPreferido = (idioma: string) => {
    const idiomas: Record<string, string> = {
      'pt-BR': 'Português (Brasil)',
      en: 'Inglês',
      es: 'Espanhol',
    }
    return idiomas[idioma] || idioma || 'Não informado'
  }

  const handleDeleteAccount = async () => {
    if (confirmEmail.value !== user.value?.email) {
      error('E-mail não confere')
      return
    }

    deleteLoading.value = true

    try {
      const response = await fetch(`http://localhost:8765/users/${user.value.id}`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
        },
      })

      const data = await response.json()

      if (response.ok && data.success) {
        // Limpa os dados do usuário
        localStorage.removeItem('user')
        localStorage.removeItem('auth_token')

        success('Conta excluída com sucesso!')

        setTimeout(() => {
          clearAllAlerts()
          router.push('/register')
        }, 2000)
      } else {
        error(data.message || 'Erro ao excluir conta')
        // Fecha o modal após erro
        setTimeout(() => {
          showDeleteModal.value = false
          confirmEmail.value = ''
        }, 1500)
      }
    } catch (err) {
      console.error('Erro ao excluir:', err)
      error('Erro de conexão com o servidor')
      setTimeout(() => {
        showDeleteModal.value = false
        confirmEmail.value = ''
      }, 1500)
    } finally {
      deleteLoading.value = false
      // Se não houve erro, o modal será fechado após o redirect
      if (!response?.ok) {
        showDeleteModal.value = false
        confirmEmail.value = ''
      }
    }
  }

  const loadUserData = async () => {
    const userData = localStorage.getItem('user')
    if (!userData) {
      router.push('/login')
      return
    }

    try {
      const localUser = JSON.parse(userData)

      // Busca dados atualizados do backend
      const response = await fetch(`http://localhost:8765/users/${localUser.id}`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
        },
      })

      if (response.ok) {
        const data = await response.json()
        if (data.success) {
          user.value = data.user
          // Atualiza o localStorage
          localStorage.setItem('user', JSON.stringify(data.user))
        } else {
          user.value = localUser
        }
      } else {
        user.value = localUser
      }
    } catch (e) {
      console.error('Erro ao carregar usuário:', e)
      user.value = JSON.parse(userData)
    }

    if (!user.value) router.push('/login')
  }

  onMounted(() => {
    loadUserData()
  })

  return {
    user,
    showDeleteModal,
    confirmEmail,
    deleteLoading,
    getNivelIngles,
    getIdiomaPreferido,
    handleDeleteAccount,
  }
}
