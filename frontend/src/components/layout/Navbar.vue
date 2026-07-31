<template>
  <nav class="dashboard-nav">
    <div class="nav-container">
      <!-- Logo -->
      <div class="nav-brand">
        <img src="/logo.png" alt="Beready Logo" class="logo-icon-img" />
      </div>

      <!-- Desktop Menu com Scroll -->
      <div class="nav-menu-wrapper" ref="menuWrapper">
        <!-- Seta Esquerda -->
        <button 
          v-show="showLeftArrow" 
          class="nav-scroll-indicator left" 
          @click="scrollMenu('left')"
          aria-label="Rolar menu para esquerda"
        >
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18" height="18">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>

        <div class="nav-menu" ref="navMenu">
          <router-link
            v-for="item in menuItems"
            :key="item.path"
            :to="item.path"
            class="nav-link"
            active-class="active"
          >
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
                :d="item.iconPath"
              />
            </svg>
            {{ $t(item.name) }}
          </router-link>
        </div>

        <!-- Seta Direita -->
        <button 
          v-show="showRightArrow" 
          class="nav-scroll-indicator right" 
          @click="scrollMenu('right')"
          aria-label="Rolar menu para direita"
        >
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18" height="18">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>

      <!-- User + Hamburger -->
      <div class="nav-user">
        <div class="user-info">
          <div class="user-avatar">
            <img
              v-if="userData?.foto_perfil"
              :src="userData.foto_perfil"
              alt="Foto de perfil"
              class="avatar-image"
            />
            <span v-else>{{ userInitial }}</span>
          </div>
          <div class="user-details">
            <span class="user-name">{{ userName }}</span>
            <span class="user-email">{{ userEmail }}</span>
          </div>
        </div>

        <button class="logout-btn" @click="handleLogout">
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
              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
            />
          </svg>
          <span>{{ $t('common.sair') }}</span>
        </button>

        <button
          class="hamburger-btn"
          @click="toggleMenu"
          :class="{ active: isMenuOpen }"
          aria-label="Alternar menu"
        >
          <span class="hamburger-line"></span>
          <span class="hamburger-line"></span>
          <span class="hamburger-line"></span>
        </button>
      </div>
    </div>

    <!-- Mobile Menu - SEM o botão de logout duplicado -->
    <div v-show="isMenuOpen" class="mobile-menu-overlay" @click.self="closeMenu">
      <div class="mobile-menu-items">
        <router-link
          v-for="item in menuItems"
          :key="item.path"
          :to="item.path"
          class="mobile-nav-link"
          @click="closeMenu"
        >
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
              :d="item.iconPath"
            />
          </svg>
          {{ $t(item.name) }}
        </router-link>

      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { watch } from 'vue'
import { useNavbarLogic } from './Navbar'
import type { User } from '@/core/types/User'

const props = defineProps<{
  user: User | null
  loading?: boolean
}>()

const emit = defineEmits<{
  (e: 'logout'): void
}>()

const {
  user: userData,
  userName,
  userEmail,
  userInitial,
  menuItems,
  isMenuOpen,
  navMenu,
  menuWrapper,
  showLeftArrow,
  showRightArrow,
  toggleMenu,
  closeMenu,
  scrollMenu,
  handleLogout: logoutHandler,
  setUser
} = useNavbarLogic()

// Sincronizar props com a lógica
watch(() => props.user, (newUser) => {
  setUser(newUser)
}, { immediate: true })

// Emitir evento de logout
const handleLogout = () => {
  logoutHandler()
  emit('logout')
}
</script>

<style scoped>
@import '@/styles/components/navbar.css';
</style>