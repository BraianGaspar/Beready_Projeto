<template>
  <div class="quizes-page">
    <!-- Hero Section -->
    <div class="quizes-hero">
      <div class="hero-content">
        <div class="hero-icon">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
          </svg>
        </div>
        <h1 class="hero-title">Quizzes</h1>
        <p class="hero-subtitle">Teste seus conhecimentos com nossos quizzes interativos</p>
        <button class="hero-btn" @click="addQuiz">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Criar Novo Quiz
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>Carregando quizzes...</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="quizes.length === 0" class="empty-state">
      <div class="empty-icon">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
      </div>
      <h3 class="empty-title">Nenhum quiz encontrado</h3>
      <p class="empty-description">Comece criando seu primeiro quiz!</p>
      <button class="empty-btn" @click="addQuiz">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Criar Primeiro Quiz
      </button>
    </div>

    <!-- Quizzes Grid -->
    <div v-else class="quizes-grid">
      <div v-for="quiz in quizes" :key="quiz.id" class="quiz-card">
        <div class="quiz-card-actions">
          <button class="btn-edit" @click.stop="editQuiz(quiz)">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
            </svg>
          </button>
          <button class="btn-delete" @click.stop="openDeleteModal(quiz.id, quiz.titulo)">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
          </button>
        </div>
        <div class="quiz-card-badge" :class="getLevelClass(quiz.nivel_dificuldade)">
          {{ getLevelText(quiz.nivel_dificuldade) }}
        </div>
        <div class="quiz-card-content" @click="viewQuiz(quiz.id)">
          <h3 class="quiz-card-title">{{ quiz.titulo }}</h3>
          <p class="quiz-card-description">{{ quiz.descricao || 'Sem descrição' }}</p>
          <div class="quiz-card-stats">
            <div class="stat">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>{{ quiz.total_questoes || 0 }} questões</span>
            </div>
            <div v-if="quiz.tempo_limite" class="stat">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>{{ quiz.tempo_limite }} min</span>
            </div>
            <div class="stat">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
              <span>{{ formatDate(quiz.criado_em) }}</span>
            </div>
          </div>
        </div>
        <div class="quiz-card-footer">
          <button class="btn-play" @click="startQuiz(quiz.id)">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Iniciar Quiz
          </button>
        </div>
      </div>
    </div>

    <!-- Confirm Delete Modal -->
    <ConfirmModal
      v-model="showConfirmModal"
      title="Excluir Quiz"
      message="Tem certeza que deseja excluir este quiz? Esta ação não pode ser desfeita."
      confirm-text="Sim, excluir"
      type="danger"
      :item-name="quizToDeleteTitle"
      :loading="deleting"
      @confirm="confirmDelete"
    />
  </div>
</template>

<script setup lang="ts">
import { useQuizes } from './Quizes'
import ConfirmModal from '@/components/common/ConfirmModal.vue'

const {
  quizes,
  loading,
  creating,
  editing,
  deleting,
  showConfirmModal,
  quizToDeleteTitle,
  viewQuiz,
  startQuiz,
  addQuiz,
  editQuiz,
  openDeleteModal,
  confirmDelete,
  getLevelClass,
  getLevelText,
  formatDate
} = useQuizes()
</script>

<style scoped>
@import '@/styles/views/quizes/quizes.css';
</style>