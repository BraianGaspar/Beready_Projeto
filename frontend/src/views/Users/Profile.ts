import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

export function useProfile() {
  const router = useRouter()
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
      alert('E-mail não confere')
      return
    }

    deleteLoading.value = true

    try {
      const response = await fetch(`http://localhost:8765/users/${user.value.id}`, {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
      })
      const data = await response.json()
      if (data.success) {
        localStorage.removeItem('user')
        localStorage.removeItem('auth_token')
        alert('Conta excluída com sucesso')
        router.push('/register')
      } else {
        alert(data.message || 'Erro ao excluir conta')
      }
    } catch (err) {
      console.error('Erro ao excluir:', err)
      alert('Erro de conexão com o servidor')
    } finally {
      deleteLoading.value = false
      showDeleteModal.value = false
    }
  }

  onMounted(() => {
    const userData = localStorage.getItem('user')
    if (userData) {
      try {
        user.value = JSON.parse(userData)
      } catch (e) {
        console.error('Erro ao carregar usuário:', e)
      }
    }
    if (!user.value) router.push('/login')
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
