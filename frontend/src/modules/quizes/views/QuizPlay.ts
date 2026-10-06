import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { respostaService } from '@/modules/progresso/services/respostaService'
import { progressoService } from '@/modules/progresso/services/progressoService'
import { quizService } from '../services/quizService'
import { getApiErrorMessage } from '@/core/services/api'
import { useAlert } from '@/shared/composables/useAlert'
import { useAuthStore } from '@/stores/auth'
import type { Quiz } from '@/core/types'

/**
 * Jogo de quiz com os dados reais do backend.
 *
 * A tabela `quizes` não possui questões/alternativas: cada quiz tem apenas
 * `titulo` e `descricao`. Por isso cada quiz do usuário vira uma pergunta
 * (titulo) com a resposta (descricao); o usuário revela a resposta e marca se
 * acertou. A sessão começa pelo quiz escolhido e segue pelos demais quizzes do
 * usuário. Cada resposta é registrada em POST /respostas com o id real do quiz.
 */
export function useQuizPlay() {
  const route = useRoute()
  const router = useRouter()
  const { t } = useI18n()
  const { error } = useAlert()
  const authStore = useAuthStore()

  const quizes = ref<Quiz[]>([])
  const currentIndex = ref(0)
  const respostaVisivel = ref(false)
  const acertos = ref(0)
  const loading = ref(true)
  const sessionStartTime = ref(Date.now())

  const currentQuiz = computed<Quiz | null>(() => quizes.value[currentIndex.value] ?? null)
  const total = computed(() => quizes.value.length)
  const isFinished = computed(() => total.value > 0 && currentIndex.value >= total.value)

  const loadQuizes = async () => {
    const quizId = Number(route.params.id)
    loading.value = true
    try {
      const response = await quizService.getAll()
      const lista = response.data.data || []
      const inicio = lista.findIndex((q) => q.id === quizId)

      if (inicio === -1) {
        error(t('quizes.errorLoad'))
        router.push('/quizes')
        return
      }

      // Começa pelo quiz escolhido e segue pelos demais
      quizes.value = [...lista.slice(inicio), ...lista.slice(0, inicio)]
    } catch (err) {
      error(getApiErrorMessage(err) || t('quizes.errorLoad'))
      router.push('/quizes')
    } finally {
      loading.value = false
    }
  }

  const mostrarResposta = () => {
    respostaVisivel.value = true
  }

  const registrarTempo = async () => {
    const usuarioId = authStore.user?.id
    const segundos = Math.floor((Date.now() - sessionStartTime.value) / 1000)
    if (!usuarioId || segundos <= 0) return

    try {
      await progressoService.incrementarTempo(segundos, usuarioId)
    } catch {
      // Best-effort: não interrompe o jogo
    }
  }

  const responder = async (correto: boolean) => {
    const quiz = currentQuiz.value
    const usuarioId = authStore.user?.id
    if (!quiz || !usuarioId) return

    if (correto) acertos.value++

    respostaService
      .registrarResposta({
        usuario_id: usuarioId,
        tipo: 'quiz',
        referencia_id: quiz.id,
        correto,
      })
      .catch(() => {
        // Best-effort: não interrompe o jogo
      })

    respostaVisivel.value = false
    currentIndex.value++

    if (isFinished.value) {
      await registrarTempo()
    }
  }

  const jogarNovamente = () => {
    currentIndex.value = 0
    acertos.value = 0
    respostaVisivel.value = false
    sessionStartTime.value = Date.now()
  }

  const voltar = () => {
    router.push('/quizes')
  }

  onMounted(() => {
    sessionStartTime.value = Date.now()
    loadQuizes()
  })

  return {
    currentQuiz,
    currentIndex,
    total,
    acertos,
    respostaVisivel,
    loading,
    isFinished,
    mostrarResposta,
    responder,
    jogarNovamente,
    voltar,
  }
}
