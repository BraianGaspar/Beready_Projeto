import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { getApiErrorMessage } from '@/core/services/api'
import { useAlert } from '@/shared/composables/useAlert'
import { usePermissionStore } from '@/stores/permissionStore'
import { QuizRequestError, useQuizes } from '../composables/useQuizes'
import {
  getApiErrors,
  questoesFromApi,
  questoesToInput,
  useQuestoesValidacao,
  type QuestaoForm,
  type QuestoesErrors,
} from '../composables/useQuestoesForm'
import { quizService } from '../services/quizService'
import { normalizeNivel, type NivelDificuldade } from '@/shared/utils/nivelDificuldade'

export function useQuizEdit() {
  const router = useRouter()
  const route = useRoute()
  const { t } = useI18n()
  const { error } = useAlert()
  const permissionStore = usePermissionStore()
  // useQuizes já exibe os alertas de sucesso/erro
  const { getQuiz, updateQuiz, deleteQuiz } = useQuizes()
  const { validar, traduzirErrosApi } = useQuestoesValidacao()

  const loading = ref(false)
  const deleteLoading = ref(false)
  const showDeleteModal = ref(false)

  const canEdit = computed(() => permissionStore.canEdit('quizes'))

  const form = reactive({
    id: null as number | null,
    titulo: '',
    descricao: '',
    nivel_dificuldade: 'iniciante' as NivelDificuldade,
    tempo_limite: null as number | null,
    publico: false,
    tipo_criacao: 'manual',
  })

  const questoes = ref<QuestaoForm[]>([])
  const questoesErrors = ref<QuestoesErrors>({})

  const errors = reactive({
    titulo: '',
    descricao: '',
  })

  const validateForm = (): boolean => {
    errors.titulo = form.titulo.trim() ? '' : t('quizes.tituloRequired')
    questoesErrors.value = validar(questoes.value)

    return !errors.titulo && Object.keys(questoesErrors.value).length === 0
  }

  const loadQuiz = async () => {
    const id = Number(route.params.id)
    if (!id) {
      router.push('/quizes')
      return
    }

    loading.value = true
    try {
      await permissionStore.loadPermissions()

      const quiz = await getQuiz(id)
      if (!quiz) {
        router.push('/quizes')
        return
      }
      form.id = quiz.id
      form.titulo = quiz.titulo ?? ''
      form.descricao = quiz.descricao ?? ''
      form.nivel_dificuldade = normalizeNivel(quiz.nivel_dificuldade)
      form.tempo_limite = quiz.tempo_limite ?? null
      form.publico = !!quiz.publico
      form.tipo_criacao = quiz.tipo_criacao || 'manual'

      // Gabarito só vem para o dono (outro usuário recebe 404)
      const response = await quizService.getQuestoes(id)
      questoes.value = questoesFromApi(response.data.data ?? [])
    } catch (err) {
      error(getApiErrorMessage(err) || t('quizes.errorLoadOne'))
      router.push('/quizes')
    } finally {
      loading.value = false
    }
  }

  const handleSubmit = async () => {
    if (!form.id || !canEdit.value || !validateForm()) return

    loading.value = true
    try {
      // Valida e grava as questões primeiro: se forem recusadas, o quiz fica como estava
      await quizService.saveQuestoes(form.id, questoesToInput(questoes.value))
    } catch (err) {
      questoesErrors.value = traduzirErrosApi(getApiErrors(err))
      error(t('quizEditor.erroSalvar'))
      loading.value = false
      return
    }

    try {
      await updateQuiz(form.id, {
        titulo: form.titulo,
        descricao: form.descricao,
        nivel_dificuldade: form.nivel_dificuldade,
        tempo_limite: form.tempo_limite ?? undefined,
        publico: form.publico,
        tipo_criacao: form.tipo_criacao,
      })
      router.push(`/quizes/${form.id}`)
    } catch (err) {
      // Alerta de erro já exibido por useQuizes
      if (err instanceof QuizRequestError && err.errors.titulo) {
        errors.titulo = t('quizes.tituloRequired')
      }
    } finally {
      loading.value = false
    }
  }

  const handleDelete = () => {
    showDeleteModal.value = true
  }

  const confirmDelete = async () => {
    if (!form.id) return

    deleteLoading.value = true
    try {
      await deleteQuiz(form.id)
      router.push('/quizes')
    } catch {
      // Alerta de erro já exibido por useQuizes
    } finally {
      deleteLoading.value = false
      showDeleteModal.value = false
    }
  }

  onMounted(() => {
    loadQuiz()
  })

  return {
    form,
    errors,
    questoes,
    questoesErrors,
    loading,
    deleteLoading,
    showDeleteModal,
    canEdit,
    handleSubmit,
    handleDelete,
    confirmDelete,
  }
}
