import { ref, computed, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAlert } from '@/shared/composables/useAlert'
import api, { getApiErrorMessage } from '@/core/services/api'
import { respostaService } from '@/modules/progresso/services/respostaService'
import { progressoService } from '@/modules/progresso/services/progressoService'
import { useAuthStore } from '@/stores/auth'
import { useI18n } from 'vue-i18n'
import { getNivelLabelKey } from '@/shared/utils/nivelDificuldade'

interface StudyCard {
  id: number
  pergunta: string
  resposta: string
  nivel_dificuldade: string
}

export function useFlashcardStudy() {
  const sessionStartTime = ref(Date.now())

  const router = useRouter()
  const route = useRoute()
  const authStore = useAuthStore()
  const { error } = useAlert()
  const { t } = useI18n()

  const flashcards = ref<StudyCard[]>([])
  const currentIndex = ref(0)
  const loading = ref(true)
  const isFlipped = ref(false)
  const showCompletionModal = ref(false)
  const stats = ref({
    hard: 0,
    good: 0,
    easy: 0,
  })

  const allFlashcardIds = ref<number[]>([])
  const hasNextFlashcard = ref(false)

  const currentFlashcard = computed(
    () => flashcards.value[currentIndex.value]
  )

  const flashcard = computed(() => currentFlashcard.value)

  const loadAllFlashcardIds = async () => {
    try {
      const { data } = await api.get('/flashcards')

      if (data.success && Array.isArray(data.data)) {
        allFlashcardIds.value = data.data.map((item: { id: number | string }) => Number(item.id))
      }
    } catch (err) {
      console.error('Erro ao carregar lista de flashcards:', err)
    }
  }

  const updateHasNextFlashcard = () => {
    const currentId = Number(route.params.id)
    const idx = allFlashcardIds.value.indexOf(currentId)

    hasNextFlashcard.value =
      idx !== -1 && idx < allFlashcardIds.value.length - 1
  }

  const loadFlashcards = async () => {
    const id = route.params.id

    if (!id) {
      error(t('flashcardStudy.missingId'))
      router.push('/flashcards')
      return
    }

    loading.value = true

    try {
      const { data } = await api.get(`/flashcards/${id}`)

      if (data.success) {
        const item = data.data

        flashcards.value = [
          {
            id: item.id,
            pergunta: item.pergunta || item.frente || '',
            resposta: item.resposta || item.verso || '',
            nivel_dificuldade: item.nivel_dificuldade || 'iniciante',
          },
        ]

        currentIndex.value = 0
        updateHasNextFlashcard()
      } else {
        error(data.message || t('flashcardStudy.errorLoad'))
        router.push('/flashcards')
      }
    } catch (err) {
      console.error(err)
      error(getApiErrorMessage(err) || t('errors.networkError'))
      router.push('/flashcards')
    } finally {
      loading.value = false
    }
  }

  const goBack = () => {
    router.push('/flashcards')
  }

  const flipCard = () => {
    isFlipped.value = !isFlipped.value
  }

  const nextCard = () => {
    if (currentIndex.value < flashcards.value.length - 1) {
      currentIndex.value++
      isFlipped.value = false
    }
  }

  const previousCard = () => {
    if (currentIndex.value > 0) {
      currentIndex.value--
      isFlipped.value = false
    }
  }

  const goToNextFlashcard = () => {
    const currentId = Number(route.params.id)
    const idx = allFlashcardIds.value.indexOf(currentId)

    if (idx === -1 || idx >= allFlashcardIds.value.length - 1) {
      return
    }

    const nextId = allFlashcardIds.value[idx + 1]

    isFlipped.value = false

    router.push({
      name: route.name as string,
      params: { id: nextId },
    })
  }

  const incrementarProgresso = async (usuarioId: number) => {
    try {
      await progressoService.incrementarFlashcards(usuarioId, 1)
    } catch (err) {
      console.error('Erro ao incrementar progresso:', err)
    }
  }

  const incrementarTempo = async (usuarioId: number) => {
    const segundos = Math.floor(
      (Date.now() - sessionStartTime.value) / 1000
    )

    if (segundos <= 0) return

    try {
      await progressoService.incrementarTempo(segundos, usuarioId)
    } catch (err) {
      console.error('Erro ao incrementar tempo de estudo:', err)
    }
  }

  const rateCard = async (rating: 'hard' | 'good' | 'easy') => {
    if (rating === 'hard') stats.value.hard++
    if (rating === 'good') stats.value.good++
    if (rating === 'easy') stats.value.easy++

    const userId = authStore.user?.id

    if (userId && currentFlashcard.value) {
      const isCorrect = rating !== 'hard'

      respostaService
        .registrarResposta({
          usuario_id: userId,
          tipo: 'flashcard',
          referencia_id: currentFlashcard.value.id,
          correto: isCorrect,
        })
        .catch(() => {
          // Registro de resposta é best-effort: não interrompe o estudo
        })

      incrementarProgresso(userId)
    }

    if (hasNextFlashcard.value) {
      goToNextFlashcard()
    } else {
      finishStudy()
    }
  }

  const finishStudy = () => {
    const userId = authStore.user?.id

    if (userId) {
      incrementarTempo(userId)
    }

    showCompletionModal.value = true
  }

  const studyAgain = () => {
    showCompletionModal.value = false

    stats.value = {
      hard: 0,
      good: 0,
      easy: 0,
    }

    isFlipped.value = false

    sessionStartTime.value = Date.now()

    if (allFlashcardIds.value.length > 0) {
      const firstId = allFlashcardIds.value[0]

      router.push({
        name: route.name as string,
        params: { id: firstId },
      })
    }
  }

  const getLevelText = (level: string) => t(getNivelLabelKey(level))

  watch(
    () => route.params.id,
    () => {
      loadFlashcards()
    }
  )

  onMounted(() => {
    sessionStartTime.value = Date.now()
    loadAllFlashcardIds()
    loadFlashcards()
  })

  return {
    flashcard,
    flashcards,
    currentIndex,
    currentFlashcard,
    loading,
    isFlipped,
    showCompletionModal,
    stats,
    hasNextFlashcard,
    goBack,
    flipCard,
    nextCard,
    previousCard,
    rateCard,
    finishStudy,
    studyAgain,
    getLevelText,
  }
}