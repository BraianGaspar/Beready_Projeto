<template>
  <div class="dashboard-container">
    <Navbar :user="user" :loading="loading" @logout="handleLogout" />

    <main class="dashboard-main">
      <div class="welcome-section">
        <h1 class="welcome-title">Bem-vindo de volta, {{ userName }}! 👋</h1>
        <p class="welcome-subtitle">
          Continue sua jornada de aprendizado. Hoje é um ótimo dia para aprender algo novo!
        </p>
      </div>

      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon bg-blue-100">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 text-blue-600"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
              />
            </svg>
          </div>
          <div class="stat-info">
            <h3 class="stat-value">12</h3>
            <p class="stat-label">Flashcards Estudados</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon bg-green-100">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 text-green-600"
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
          <div class="stat-info">
            <h3 class="stat-value">85%</h3>
            <p class="stat-label">Taxa de Acerto</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon bg-purple-100">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 text-purple-600"
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
          <div class="stat-info">
            <h3 class="stat-value">3</h3>
            <p class="stat-label">Dias de Sequência</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon bg-yellow-100">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 text-yellow-600"
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
          <div class="stat-info">
            <h3 class="stat-value">2h 30m</h3>
            <p class="stat-label">Tempo de Estudo</p>
          </div>
        </div>
      </div>

      <div class="progress-section">
        <div class="progress-card">
          <h3 class="progress-title">Progresso Geral</h3>
          <div class="progress-bar-container">
            <div class="progress-bar" style="width: 65%"></div>
          </div>
          <p class="progress-text">65% completo - Continue assim!</p>
        </div>
      </div>

      <div class="dashboard-features-grid">
        <div class="dashboard-feature-card" @click="$router.push('/flashcards')">
          <div class="feature-icon-large bg-blue-500">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-8 w-8 text-white"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
              />
            </svg>
          </div>
          <div class="feature-content">
            <h3 class="feature-title">Flashcards</h3>
            <p class="feature-description">
              Estude com flashcards interativos e acelere seu aprendizado
            </p>
            <span class="feature-link">Começar agora →</span>
          </div>
        </div>

        <div class="dashboard-feature-card" @click="$router.push('/quizes')">
          <div class="feature-icon-large bg-green-500">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-8 w-8 text-white"
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
          <div class="feature-content">
            <h3 class="feature-title">Quizes</h3>
            <p class="feature-description">Teste seus conhecimentos com quizzes personalizados</p>
            <span class="feature-link">Testar conhecimento →</span>
          </div>
        </div>

        <div class="dashboard-feature-card" @click="$router.push('/prompts')">
          <div class="feature-icon-large bg-purple-500">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-8 w-8 text-white"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"
              />
            </svg>
          </div>
          <div class="feature-content">
            <h3 class="feature-title">Prompts com IA</h3>
            <p class="feature-description">Gere prompts personalizados para praticar conversação</p>
            <span class="feature-link">Gerar prompts →</span>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { useDashboard } from './Dashboard'
import Navbar from '@/components/layout/Navbar.vue'

const { user, loading, userName, handleLogout } = useDashboard()
</script>

<style scoped>
@import '@/styles/views/dashboard.css';
</style>
