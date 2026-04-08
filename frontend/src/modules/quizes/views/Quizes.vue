<template>
  <div class="quizes-page">
    <div class="quizes-hero">
      <div class="hero-content">
        <button class="hero-back-btn" @click="$router.push('/dashboard')">
          ← Voltar ao Dashboard
        </button>
        <div class="hero-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
            <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83" />
            <circle cx="12" cy="12" r="3" />
          </svg>
        </div>
        <h1 class="hero-title">Meus Quizes</h1>
        <p class="hero-subtitle">Teste seus conhecimentos com quizes interativos</p>
        <button class="hero-btn" @click="openCreateModal">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 5v14M5 12h14" />
          </svg>
          Criar Novo Quiz
        </button>
      </div>
    </div>

    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>Carregando quizes...</p>
    </div>

    <div v-else-if="quizes.length === 0" class="empty-state">
      <div class="empty-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5">
          <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83" />
          <circle cx="12" cy="12" r="3" />
        </svg>
      </div>
      <h2 class="empty-title">Nenhum quiz ainda</h2>
      <p class="empty-description">Comece criando seu primeiro quiz para testar seus conhecimentos!</p>
      <button class="empty-btn" @click="openCreateModal">
        Criar Primeiro Quiz
      </button>
    </div>

    <div v-else class="quizes-grid">
      <div v-for="quiz in quizes" :key="quiz.id" class="quiz-card">
        <div class="quiz-card-actions">
          <button class="btn-edit" @click="openEditModal(quiz)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17 3l4 4L7 21H3v-4L17 3z" />
            </svg>
          </button>
          <button class="btn-delete" @click="confirmDelete(quiz)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
            </svg>
          </button>
        </div>
        <span class="quiz-card-badge" :class="getLevelClass(quiz.nivel_dificuldade)">
          {{ getDifficultyText(quiz.nivel_dificuldade) }}
        </span>
        <div class="quiz-card-content" @click="viewQuiz(quiz.id)">
          <h3 class="quiz-card-title">{{ quiz.titulo }}</h3>
          <p class="quiz-card-description">{{ quiz.descricao || 'Sem descrição' }}</p>
          <div class="quiz-card-stats">
            <span class="stat">��� {{ quiz.total_questoes || 0 }} questões</span>
            <span class="stat">⏱️ {{ quiz.tempo_limite || 'Sem limite' }} min</span>
          </div>
        </div>
        <div class="quiz-card-footer">
          <button class="btn-play" @click="playQuiz(quiz.id)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polygon points="5 3 19 12 5 21 5 3" />
            </svg>
            Jogar
          </button>
        </div>
      </div>
    </div>

    <!-- Modal de Criar/Editar -->
    <div v-if="showModal" class="modal-overlay" @click="closeModal">
      <div class="modal-container" @click.stop>
        <div class="modal-header">
          <div>
            <h2 class="modal-title">{{ isEditing ? 'Editar Quiz' : 'Novo Quiz' }}</h2>
            <p class="modal-subtitle">{{ isEditing ? 'Atualize as informações do quiz' : 'Preencha os dados abaixo' }}</p>
          </div>
          <button class="modal-close" @click="closeModal">×</button>
        </div>
        <form @submit.prevent="submitForm">
          <div class="modal-body">
            <div class="form-group">
              <label class="form-label">Título *</label>
              <input v-model="form.titulo" type="text" class="form-input" required placeholder="Digite o título do quiz">
            </div>
            <div class="form-group">
              <label class="form-label">Descrição</label>
              <textarea v-model="form.descricao" class="form-textarea" rows="3" placeholder="Descreva o conteúdo do quiz..."></textarea>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Nível de Dificuldade</label>
                <select v-model="form.nivel_dificuldade" class="form-select">
                  <option value="iniciante">Iniciante</option>
                  <option value="intermediario">Intermediário</option>
                  <option value="avancado">Avançado</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Tempo Limite (minutos)</label>
                <input v-model.number="form.tempo_limite" type="number" class="form-input" placeholder="Opcional">
              </div>
            </div>
            <div class="form-group">
              <label class="form-checkbox">
                <input v-model="form.publico" type="checkbox">
                <span>Tornar quiz público (outros usuários podem ver)</span>
              </label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn-cancel" @click="closeModal">Cancelar</button>
            <button type="submit" class="btn-create" :disabled="submitting">
              {{ submitting ? 'Salvando...' : (isEditing ? 'Atualizar' : 'Criar') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div v-if="showDeleteModal" class="modal-overlay" @click="showDeleteModal = false">
      <div class="modal-container confirm-modal" @click.stop>
        <div class="modal-header confirm-header">
          <div class="confirm-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10" />
              <path d="M12 8v4M12 16h.01" />
            </svg>
          </div>
          <h2 class="modal-title">Confirmar Exclusão</h2>
        </div>
        <div class="modal-body confirm-body">
          <p>Tem certeza que deseja excluir este quiz?</p>
          <p class="quiz-name">{{ deletingQuiz?.titulo }}</p>
          <p class="modal-warning">Esta ação não pode ser desfeita!</p>
        </div>
        <div class="confirm-footer">
          <button class="btn-cancel" @click="showDeleteModal = false">Cancelar</button>
          <button class="btn-delete-confirm" @click="handleDelete" :disabled="deleting">
            {{ deleting ? 'Excluindo...' : 'Sim, excluir' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useQuizes } from '../composables/useQuizes'

const router = useRouter()
const { quizes, loading, loadQuizes, createQuiz, updateQuiz, deleteQuiz } = useQuizes()

const showModal = ref(false)
const showDeleteModal = ref(false)
const isEditing = ref(false)
const editingId = ref<number | null>(null)
const deletingQuiz = ref<any>(null)
const submitting = ref(false)
const deleting = ref(false)

const form = reactive({
  titulo: '',
  descricao: '',
  nivel_dificuldade: 'intermediario',
  tempo_limite: null as number | null,
  total_questoes: 0,
  publico: false,
  tipo_criacao: 'manual'
})

const getDifficultyText = (level: string) => {
  const texts: Record<string, string> = {
    iniciante: 'Iniciante',
    intermediario: 'Intermediário',
    avancado: 'Avançado'
  }
  return texts[level] || level
}

const getLevelClass = (level: string) => {
  const classes: Record<string, string> = {
    iniciante: 'level-beginner',
    intermediario: 'level-intermediate',
    avancado: 'level-advanced'
  }
  return classes[level] || 'level-intermediate'
}

const resetForm = () => {
  form.titulo = ''
  form.descricao = ''
  form.nivel_dificuldade = 'intermediario'
  form.tempo_limite = null
  form.publico = false
  editingId.value = null
  isEditing.value = false
}

const openCreateModal = () => {
  resetForm()
  isEditing.value = false
  showModal.value = true
}

const openEditModal = (quiz: any) => {
  form.titulo = quiz.titulo
  form.descricao = quiz.descricao || ''
  form.nivel_dificuldade = quiz.nivel_dificuldade
  form.tempo_limite = quiz.tempo_limite
  form.publico = quiz.publico || false
  editingId.value = quiz.id
  isEditing.value = true
  showModal.value = true
}

const viewQuiz = (id: number) => {
  router.push(`/quizes/${id}`)
}

const playQuiz = (id: number) => {
  router.push(`/quizes/${id}/play`)
}

const confirmDelete = (quiz: any) => {
  deletingQuiz.value = quiz
  showDeleteModal.value = true
}

const handleDelete = async () => {
  if (!deletingQuiz.value) return
  deleting.value = true
  try {
    await deleteQuiz(deletingQuiz.value.id)
    showDeleteModal.value = false
    const userData = localStorage.getItem('user')
    if (userData) {
      const user = JSON.parse(userData)
      await loadQuizes(user.id)
    }
  } finally {
    deleting.value = false
    deletingQuiz.value = null
  }
}

const submitForm = async () => {
  const userData = localStorage.getItem('user')
  if (!userData) return

  const user = JSON.parse(userData)
  submitting.value = true

  try {
    const data = {
      usuario_id: user.id,
      titulo: form.titulo,
      descricao: form.descricao,
      nivel_dificuldade: form.nivel_dificuldade,
      tempo_limite: form.tempo_limite,
      total_questoes: 0,
      publico: form.publico,
      tipo_criacao: 'manual'
    }

    if (isEditing.value && editingId.value) {
      await updateQuiz(editingId.value, data)
    } else {
      await createQuiz(data)
    }

    closeModal()
    await loadQuizes(user.id)
  } finally {
    submitting.value = false
  }
}

const closeModal = () => {
  showModal.value = false
  resetForm()
}

onMounted(async () => {
  const userData = localStorage.getItem('user')
  if (userData) {
    const user = JSON.parse(userData)
    await loadQuizes(user.id)
  }
})
</script>

<style scoped>
@import '@/styles/views/quizes/quizes.css';
</style>
