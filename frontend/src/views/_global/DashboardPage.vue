<template>
  <div class="dashboard-container">
    <Navbar :user="user" :loading="loading" @logout="handleLogout" />

    <main class="dashboard-main">
      <div class="welcome-section">
        <h1 class="welcome-title">{{ $t('dashboard.welcome', { name: userName }) }}</h1>
        <p class="welcome-subtitle">{{ motivationalMessage }}</p>
      </div>

      <!-- PAINEL ADMIN -->
      <div v-if="isAdmin" class="admin-banner">
        <div class="admin-banner-icon">
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
              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
            />
          </svg>
        </div>
        <div class="admin-banner-content">
          <span class="admin-badge">{{ $t('admin.badge') }}</span>
          <div class="admin-links">
            <button @click="goToAdmin('users')" class="admin-link-btn">
               {{ $t('admin.users') }}
            </button>
            <button @click="goToAdmin('roles')" class="admin-link-btn">
               {{ $t('admin.roles.title') }}
            </button>
            <button @click="goToAdmin('planos')" class="admin-link-btn">
               {{ $t('admin.planos.title') }}
            </button>
            <button @click="goToAdmin('stats')" class="admin-link-btn">
               {{ $t('admin.statistics') }}
            </button>
          </div>
        </div>
      </div>

      <!-- Stats Grid -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon bg-blue-100">
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
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
              />
            </svg>
          </div>
          <div class="stat-info">
            <h3 class="stat-value">{{ stats.flashcardsCount || 0 }}</h3>
            <p class="stat-label">{{ $t('dashboard.flashcardsCount') }}</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon bg-green-100">
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
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>
          <div class="stat-info">
            <h3 class="stat-value">{{ stats.acertoRate || 0 }}%</h3>
            <p class="stat-label">{{ $t('dashboard.acertoRate') }}</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon bg-purple-100">
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
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>
          <div class="stat-info">
            <h3 class="stat-value">{{ stats.sequenciaAtual || 0 }}</h3>
            <p class="stat-label">{{ $t('dashboard.sequencia') }}</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon bg-yellow-100">
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
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>
          <div class="stat-info">
            <h3 class="stat-value">{{ stats.tempoEstudo || '0 min' }}</h3>
            <p class="stat-label">{{ $t('dashboard.tempoEstudo') }}</p>
          </div>
        </div>
      </div>

      <!-- Progress Section -->
      <div class="progress-section">
        <div class="progress-card">
          <h3 class="progress-title">{{ $t('dashboard.progressoGeral') }}</h3>
          <div class="progress-bar-container">
            <div class="progress-bar" :style="{ width: (stats.progressoGeral || 0) + '%' }"></div>
          </div>
          <p class="progress-text">
            {{ stats.progressoGeral || 0 }}% completo - {{ $t('dashboard.continueAssim') }}
          </p>
        </div>
      </div>

      <!-- Features Grid -->
      <div class="dashboard-features-grid">
        <div class="dashboard-feature-card" @click="$router.push('/flashcards')">
          <div class="feature-icon-large bg-blue-500">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
              />
            </svg>
          </div>
          <div class="feature-content">
            <h3 class="feature-title">{{ $t('common.flashcards') }}</h3>
            <p class="feature-description">{{ $t('dashboard.features.flashcards.desc') }}</p>
            <span class="feature-link">{{ $t('dashboard.features.flashcards.link') }}</span>
          </div>
        </div>

        <div class="dashboard-feature-card" @click="$router.push('/quizes')">
          <div class="feature-icon-large bg-green-500">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
              />
            </svg>
          </div>
          <div class="feature-content">
            <h3 class="feature-title">{{ $t('common.quizes') }}</h3>
            <p class="feature-description">{{ $t('dashboard.features.quizes.desc') }}</p>
            <span class="feature-link">{{ $t('dashboard.features.quizes.link') }}</span>
          </div>
        </div>

        <div class="dashboard-feature-card" @click="$router.push('/prompts')">
          <div class="feature-icon-large bg-purple-500">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"
              />
            </svg>
          </div>
          <div class="feature-content">
            <h3 class="feature-title">{{ $t('prompts.title') }}</h3>
            <p class="feature-description">{{ $t('dashboard.features.prompts.desc') }}</p>
            <span class="feature-link">{{ $t('dashboard.features.prompts.link') }}</span>
          </div>
        </div>

        <div class="dashboard-feature-card" @click="$router.push('/tags')">
          <div class="feature-icon-large bg-orange-500">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-5-5A1.994 1.994 0 013 12V7a4 4 0 014-4z"
              />
            </svg>
          </div>
          <div class="feature-content">
            <h3 class="feature-title">{{ $t('common.tags') }}</h3>
            <p class="feature-description">{{ $t('dashboard.features.tags.desc') }}</p>
            <span class="feature-link">{{ $t('dashboard.features.tags.link') }}</span>
          </div>
        </div>

        <div class="dashboard-feature-card" @click="$router.push('/progresso')">
          <div class="feature-icon-large bg-indigo-500">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
              />
            </svg>
          </div>
          <div class="feature-content">
            <h3 class="feature-title">{{ $t('common.progresso') }}</h3>
            <p class="feature-description">{{ $t('dashboard.features.progresso.desc') }}</p>
            <span class="feature-link">{{ $t('dashboard.features.progresso.link') }}</span>
          </div>
        </div>

        <div class="dashboard-feature-card" @click="$router.push('/preferencias')">
          <div class="feature-icon-large bg-gray-500">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
              />
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
              />
            </svg>
          </div>
          <div class="feature-content">
            <h3 class="feature-title">{{ $t('common.preferencias') }}</h3>
            <p class="feature-description">{{ $t('dashboard.features.preferencias.desc') }}</p>
            <span class="feature-link">{{ $t('dashboard.features.preferencias.link') }}</span>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { useDashboard } from './DashboardPage'
import Navbar from '@/components/layout/AppNavbar.vue'
import { useAuth } from '@/shared/composables/useAuth'
import { useRouter } from 'vue-router'

const router = useRouter()
const { user, loading, userName, stats, motivationalMessage, handleLogout } = useDashboard()
const { isAdmin } = useAuth()

// FUNÇÃO PARA NAVEGAR PARA ADMIN COM TAB ESPECÍFICA
const goToAdmin = (tab: string) => {
  router.push(`/admin?tab=${tab}`)
}
</script>

<style scoped>
@import '@/styles/views/dashboard.css';
</style>
