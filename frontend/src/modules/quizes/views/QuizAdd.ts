import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '@/stores/auth'
import { useQuizes } from '../composables/useQuizes'
import type { NivelDificuldade } from '@/shared/utils/nivelDificuldade'

export function useQuizAdd() {
  const router = useRouter()
  const authStore = useAuthStore()
  const { t } = useI18n()
  // createQuiz verifica permissão e limite do plano e exibe os alertas
  const { createQuiz, loading } = useQuizes()

  const form = ref({
    titulo: '',
    descricao: '',
    tipo_criacao: 'manual',
    nivel_dificuldade: 'iniciante' as NivelDificuldade,
    total_questoes: 0,
    tempo_limite: null as number | null,
    publico: false,
  })

  const errors = ref({
    titulo: '',
  })

  const handleSubmit = async () => {
    if (!form.value.titulo) {
      errors.value.titulo = t('quizes.tituloRequired')
      return
    }
    errors.value.titulo = ''

    const user = authStore.user
    if (!user) {
      router.push('/login')
      return
    }

    try {
      await createQuiz({ ...form.value, usuario_id: user.id })
      router.push('/quizes')
    } catch {
      // Alerta de erro já exibido por useQuizes
    }
  }

  return {
    form,
    errors,
    loading,
    handleSubmit,
  }
}
