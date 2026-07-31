<template>
  <div class="admin-panel-page">
    <!-- Header com gradiente -->
    <div class="admin-header">
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
      <div class="header-content">
        <div class="header-icon">
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
        <div class="header-text">
          <h1>{{ $t('admin.title') }}</h1>
          <p>
            {{ $t('admin.welcome', { name: user?.nome }) }}
            <span class="admin-chip">{{ $t('admin.badge') }}</span>
          </p>
        </div>
      </div>
    </div>

    <!-- Tabs estilizadas -->
    <div class="admin-tabs">
      <button :class="['tab-btn', { active: activeTab === 'users' }]" @click="setTab('users')">
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
        {{ $t('admin.users') }}
      </button>

      <button :class="['tab-btn', { active: activeTab === 'roles' }]" @click="setTab('roles')">
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
            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
          />
        </svg>
        {{ $t('admin.roles.title') }}
      </button>

      <button :class="['tab-btn', { active: activeTab === 'planos' }]" @click="setTab('planos')">
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
            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
          />
        </svg>
        {{ $t('admin.planos.title') }}
      </button>

      <button :class="['tab-btn', { active: activeTab === 'stats' }]" @click="setTab('stats')">
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
            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
          />
        </svg>
        {{ $t('admin.statistics') }}
      </button>
    </div>

    <!-- Tab Usuários -->
    <div v-if="activeTab === 'users'" class="admin-users">
      <div class="users-header">
        <h2>{{ $t('admin.manageUsers') }}</h2>
        <div class="search-box">
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
              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
            />
          </svg>
          <input
            type="text"
            v-model="searchQuery"
            :placeholder="$t('admin.searchPlaceholder')"
          />
        </div>
      </div>

      <div v-if="loadingUsers" class="loading-state">
        <div class="spinner"></div>
        <p>{{ $t('admin.loadingUsers') }}</p>
      </div>

      <div v-else class="users-table-wrapper">
        <table class="users-table">
          <thead>
            <tr>
              <th>{{ $t('admin.id') }}</th>
              <th>{{ $t('admin.user') }}</th>
              <th>{{ $t('login.email') }}</th>
              <th>{{ $t('admin.level') }}</th>
              <th>{{ $t('admin.status') }}</th>
              <th>{{ $t('admin.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="userItem in filteredUsers" :key="userItem.id">
              <td>
                <span class="user-id">#{{ userItem.id }}</span>
              </td>
              <td>
                <div class="user-name-cell">
                  <div class="user-avatar">{{ userItem.nome?.charAt(0) || 'U' }}</div>
                  <span class="user-name">{{ userItem.nome }}</span>
                </div>
              </td>
              <td>{{ userItem.email }}</td>
              <td>
                <span :class="['role-badge', userItem.role]">
                  {{ userItem.role === 'admin' ? $t('admin.admin') : $t('admin.user') }}
                </span>
              </td>
              <td>
                <span :class="['status-badge', userItem.status]">
                  {{ userItem.status === 'ativo' ? '🟢 ' + $t('admin.active') : '🔴 ' + $t('admin.inactive') }}
                </span>
              </td>
              <td>
                <button
                  v-if="userItem.id !== currentUserId"
                  @click="toggleRole(userItem)"
                  class="action-btn"
                  :class="userItem.role === 'admin' ? 'rebaixar' : 'promover'"
                  :disabled="updatingRole === userItem.id"
                >
                  {{
                    updatingRole === userItem.id
                      ? '...'
                      : userItem.role === 'admin'
                        ? $t('admin.demote')
                        : $t('admin.promote')
                  }}
                </button>
                <span v-else class="current-user-badge">{{ $t('admin.you') }}</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB ROLES -->
    <div v-if="activeTab === 'roles'" class="admin-roles">
      <RoleManager />
    </div>

    <!-- TAB PLANOS -->
    <div v-if="activeTab === 'planos'" class="admin-planos">
      <PlanoManager />
    </div>

    <!-- TAB ESTATÍSTICAS -->
    <div v-if="activeTab === 'stats'" class="admin-stats">
      <div class="stats-grid">
        <!-- 1. USUÁRIOS -->
        <div class="stat-card">
          <div class="stat-icon purple">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
          </div>
          <div class="stat-value">{{ stats.total_users || 0 }}</div>
          <div class="stat-label">Total de Usuários</div>
          <div class="stat-sub">
            <span class="admin-badge-small"> Admin: {{ stats.admin_count || 0 }}</span>
            <span class="user-badge-small">Usuários: {{ stats.user_count || 0 }}</span>
          </div>
        </div>

        <!-- 2. FLASHCARDS -->
        <div class="stat-card">
          <div class="stat-icon blue">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
          <div class="stat-value">{{ stats.total_flashcards || 0 }}</div>
          <div class="stat-label">Flashcards</div>
        </div>

        <!-- 3. QUIZES -->
        <div class="stat-card">
          <div class="stat-icon green">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
          </div>
          <div class="stat-value">{{ stats.total_quizes || 0 }}</div>
          <div class="stat-label">Quizes</div>
        </div>

        <!-- 4. PROMPTS IA -->
        <div class="stat-card">
          <div class="stat-icon orange">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
            </svg>
          </div>
          <div class="stat-value">{{ stats.total_prompts || 0 }}</div>
          <div class="stat-label">Prompts IA</div>
        </div>

        <!-- 5. TAGS -->
        <div class="stat-card">
          <div class="stat-icon pink">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-5-5A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
          </div>
          <div class="stat-value">{{ stats.total_tags || 0 }}</div>
          <div class="stat-label">Tags</div>
        </div>

        <!-- 6. TRADUÇÕES -->
        <div class="stat-card">
          <div class="stat-icon teal">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
            </svg>
          </div>
          <div class="stat-value">{{ stats.total_traducoes || 0 }}</div>
          <div class="stat-label">Traduções</div>
        </div>

        <!-- 7. IMAGENS GERADAS -->
        <div class="stat-card">
          <div class="stat-icon indigo">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
          <div class="stat-value">{{ stats.total_imagens || 0 }}</div>
          <div class="stat-label">Imagens Geradas</div>
        </div>

        <!-- 8. FRASES SEMELHANTES -->
        <div class="stat-card">
          <div class="stat-icon rose">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
          </div>
          <div class="stat-value">{{ stats.total_frases || 0 }}</div>
          <div class="stat-label">Frases Semelhantes</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAdminPanel } from './AdminPanel'
import RoleManager from '@/components/admin/RoleManager.vue'
import PlanoManager from '@/components/admin/PlanoManager.vue'

const route = useRoute()
const router = useRouter()

const {
  user,
  loadingUsers,
  updatingRole,
  searchQuery,
  stats,
  currentUserId,
  filteredUsers,
  toggleRole,
} = useAdminPanel()

const activeTab = ref('users')

const setTab = (tab: string) => {
  activeTab.value = tab
  router.replace({ query: { tab } })
}

onMounted(() => {
  const tab = route.query.tab as string
  if (['users', 'roles', 'planos', 'stats'].includes(tab)) {
    activeTab.value = tab
  }
})

watch(() => route.query.tab, (newTab) => {
  if (['users', 'roles', 'planos', 'stats'].includes(newTab as string)) {
    activeTab.value = newTab as string
  }
})
</script>

<style scoped>
@import '@/styles/views/admin/admin-panel.css';
</style>