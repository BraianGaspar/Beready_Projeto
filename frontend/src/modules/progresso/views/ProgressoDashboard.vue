<template>
  <div class="progresso-page">
    <div class="progresso-header">
      <button class="back-btn" @click="$router.push('/dashboard')">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M10 19l-7-7m0 0l7-7m-7 7h18"
          />
        </svg>
        {{ $t('common.voltar') }}
      </button>
      <div class="hero-content">
        <div class="hero-icon">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-10 w-10"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
            />
          </svg>
        </div>
        <h1 class="hero-title">{{ $t('progresso.title') }}</h1>
        <p class="hero-subtitle">{{ $t('progresso.subtitle') }}</p>
      </div>
    </div>

    <div class="progresso-container">
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>{{ $t('progresso.carregando') }}</p>
      </div>

      <div v-else class="progresso-grid">
        <div class="progresso-card">
          <div class="card-icon bg-blue-100">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-8 w-8"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
              />
            </svg>
          </div>
          <div class="card-content">
            <h3 class="card-value">{{ progresso.vocabulario_aprendido || 0 }}</h3>
            <p class="card-label">{{ $t('progresso.vocabularioAprendido') }}</p>
          </div>
        </div>

        <div class="progresso-card">
          <div class="card-icon bg-green-100">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-8 w-8"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>
          <div class="card-content">
            <h3 class="card-value">{{ progresso.flashcards_concluidos || 0 }}</h3>
            <p class="card-label">{{ $t('progresso.flashcardsConcluidos') }}</p>
          </div>
        </div>

        <div class="progresso-card">
          <div class="card-icon bg-purple-100">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-8 w-8"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
              />
            </svg>
          </div>
          <div class="card-content">
            <h3 class="card-value">{{ progresso.quizes_concluidos || 0 }}</h3>
            <p class="card-label">{{ $t('progresso.quizesConcluidos') }}</p>
          </div>
        </div>

        <div class="progresso-card">
          <div class="card-icon bg-yellow-100">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-8 w-8"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>
          <div class="card-content">
            <h3 class="card-value">{{ formatarTempo(progresso.tempo_total_estudo || 0) }}</h3>
            <p class="card-label">{{ $t('progresso.tempoTotalEstudo') }}</p>
          </div>
        </div>

        <div class="progresso-card">
          <div class="card-icon bg-red-100">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-8 w-8"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M13 10V3L4 14h7v7l9-11h-7z"
              />
            </svg>
          </div>
          <div class="card-content">
            <h3 class="card-value">{{ progresso.sequencia_atual || 0 }}</h3>
            <p class="card-label">{{ $t('progresso.sequenciaAtual') }}</p>
          </div>
        </div>

        <div class="progresso-card">
          <div class="card-icon bg-indigo-100">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-8 w-8"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"
              />
            </svg>
          </div>
          <div class="card-content">
            <h3 class="card-value">{{ progresso.maior_sequencia || 0 }}</h3>
            <p class="card-label">{{ $t('progresso.maiorSequencia') }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useProgressoDashboard } from './ProgressoDashboard'

const {
  progresso,
  loading,
  formatarTempo
} = useProgressoDashboard()
</script>

<style scoped>
@import '@/styles/views/progresso/progresso.css';
</style>