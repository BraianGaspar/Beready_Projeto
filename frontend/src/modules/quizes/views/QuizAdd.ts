import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '@/stores/auth'
import { QuizRequestError, useQuizes } from '../composables/useQuizes'
import {
  novaQuestao,
  questoesToInput,
  useQuestoesValidacao,
  type QuestaoForm,
  type QuestoesErrors,
} from '../composables/useQuestoesForm'
import type { NivelDificuldade } from '@/shared/utils/nivelDificuldade'

export function useQuizAdd() {
  const router = useRouter()
  const authStore = useAuthStore()
  const { t } = useI18n()
  // createQuiz verifica permissão e limite do plano e exibe os alertas
  const { createQuiz, loading } = useQuizes()
  const { validar, traduzirErrosApi } = useQuestoesValidacao()

  const form = ref({
    titulo: '',
    descricao: '',
    tipo_criacao: 'manual',
    nivel_dificuldade: 'iniciante' as NivelDificuldade,
    tempo_limite: null as number | null,
    publico: false,
  })

  // Começa com uma questão de múltipla escolha em branco
  const questoes = ref<QuestaoForm[]>([novaQuestao()])
  const questoesErrors = ref<QuestoesErrors>({})

  const errors = ref({
    titulo: '',
  })

  const handleSubmit = async () => {
    errors.value.titulo = form.value.titulo.trim() ? '' : t('quizes.tituloRequired')
    questoesErrors.value = validar(questoes.value)

    if (errors.value.titulo || Object.keys(questoesErrors.value).length > 0) {
      return
    }

    const user = authStore.user
    if (!user) {
      router.push('/login')
      return
    }

    try {
      const quiz = await createQuiz({
        ...form.value,
        usuario_id: user.id,
        questoes: questoesToInput(questoes.value),
      })
      router.push(quiz?.id ? `/quizes/${quiz.id}` : '/quizes')
    } catch (err) {
      // Alerta já exibido por useQuizes; marca os campos que o servidor recusou
      if (err instanceof QuizRequestError) {
        questoesErrors.value = traduzirErrosApi(err.errors)
      }
    }
  }

  return {
    form,
    errors,
    questoes,
    questoesErrors,
    loading,
    handleSubmit,
  }
}
