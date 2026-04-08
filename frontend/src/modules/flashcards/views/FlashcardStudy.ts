import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAlert } from '@/shared/composables/useAlert'

export function useFlashcardStudy() {
  const router = useRouter()
  const route = useRoute()
  const { error } = useAlert()
  const flashcards = ref<any[]>([])
  const currentIndex = ref(0)
  const loading = ref(true)
  const isFlipped = ref(false)
  const showCompletionModal = ref(false)
  const stats = ref({ hard: 0, good: 0, easy: 0 })

  const currentFlashcard = computed(() => flashcards.value[currentIndex.value])
  const flashcard = computed(() => currentFlashcard.value)

  const loadFlashcards = async () => {
    const id = route.params.id
    if (!id) {
      error('ID do flashcard não informado')
      router.push('/flashcards')
      return
    }

    loading.value = true
    try {
      const response = await fetch(`http://localhost:8765/flashcards/${id}`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
        },
      })

      const data = await response.json()

      if (data.success) {
        flashcards.value = [data.data]
        currentIndex.value = 0
      } else {
        error(data.message || 'Erro ao carregar flashcard')
        router.push('/flashcards')
      }
    } catch (err) {
      console.error('Erro:', err)
      error('Erro de conexão com o servidor')
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

  const rateCard = (rating: string) => {
    if (rating === 'hard') stats.value.hard++
    if (rating === 'good') stats.value.good++
    if (rating === 'easy') stats.value.easy++

    if (currentIndex.value < flashcards.value.length - 1) {
      nextCard()
    } else {
      finishStudy()
    }
  }

  const finishStudy = () => {
    showCompletionModal.value = true
  }

  const studyAgain = () => {
    showCompletionModal.value = false
    currentIndex.value = 0
    isFlipped.value = false
    stats.value = { hard: 0, good: 0, easy: 0 }
  }

  const getLevelClass = (level: string) => {
    const classes: Record<string, string> = {
      iniciante: 'level-beginner',
      intermediario: 'level-intermediate',
      avancado: 'level-advanced',
    }
    return classes[level] || 'level-beginner'
  }

  const getLevelText = (level: string) => {
    const texts: Record<string, string> = {
      iniciante: 'Iniciante',
      intermediario: 'Intermediário',
      avancado: 'Avançado',
    }
    return texts[level] || level
  }

  onMounted(() => {
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
    goBack,
    flipCard,
    nextCard,
    previousCard,
    rateCard,
    finishStudy,
    studyAgain,
    getLevelClass,
    getLevelText,
  }
}
