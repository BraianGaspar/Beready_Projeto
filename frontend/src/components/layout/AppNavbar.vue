<template>
  <header class="app-nav">
    <div class="app-nav__bar">
      <router-link to="/dashboard" class="app-nav__brand">
        <img src="/logo.png" alt="BeReady" class="app-nav__logo" />
      </router-link>

      <!-- Menu inline (>= 1280px) -->
      <nav class="app-nav__desktop" :aria-label="$t('ui.mainNavigation')">
        <ul class="app-nav__list">
          <li v-for="item in menuItems" :key="item.path">
            <router-link
              :to="item.path"
              class="app-nav__link"
              :class="{ 'app-nav__link--active': isActive(item.path) }"
              :aria-current="isActive(item.path) ? 'page' : undefined"
            >
              <BaseIcon :name="item.icon" class="app-nav__link-icon" />
              <span>{{ $t(item.name) }}</span>
            </router-link>
          </li>
        </ul>
      </nav>

      <div class="app-nav__user">
        <span class="app-nav__avatar" aria-hidden="true">
          <img v-if="user?.foto_perfil" :src="user.foto_perfil" alt="" class="app-nav__avatar-img" />
          <span v-else>{{ userInitial }}</span>
        </span>
        <span class="app-nav__user-text">
          <span class="app-nav__user-name u-truncate">{{ userName }}</span>
          <span class="app-nav__user-email u-truncate">{{ userEmail }}</span>
        </span>

        <button type="button" class="app-nav__logout" @click="handleLogout">
          <BaseIcon name="logout" />
          <span>{{ $t('common.sair') }}</span>
        </button>

        <!-- Abre o drawer (< 1280px) -->
        <button
          type="button"
          class="app-nav__toggle"
          :aria-expanded="isMenuOpen"
          aria-controls="app-nav-drawer"
          :aria-label="isMenuOpen ? $t('ui.closeMenu') : $t('ui.openMenu')"
          @click="toggleMenu"
        >
          <BaseIcon :name="isMenuOpen ? 'x-mark' : 'menu'" />
        </button>
      </div>
    </div>

    <!-- Drawer (menu mobile/tablet) -->
    <Transition name="app-nav-drawer">
      <div v-if="isMenuOpen" class="app-nav__overlay" @click.self="closeMenu">
        <div
          id="app-nav-drawer"
          ref="drawerRef"
          class="app-nav__drawer"
          role="dialog"
          aria-modal="true"
          :aria-label="$t('ui.mainNavigation')"
          tabindex="-1"
        >
          <div class="app-nav__drawer-head">
            <span class="app-nav__avatar" aria-hidden="true">
              <img v-if="user?.foto_perfil" :src="user.foto_perfil" alt="" class="app-nav__avatar-img" />
              <span v-else>{{ userInitial }}</span>
            </span>
            <span class="app-nav__drawer-user">
              <span class="app-nav__user-name u-truncate">{{ userName }}</span>
              <span class="app-nav__user-email u-truncate">{{ userEmail }}</span>
            </span>
            <button
              type="button"
              class="app-nav__toggle app-nav__drawer-close"
              :aria-label="$t('ui.closeMenu')"
              @click="closeMenu"
            >
              <BaseIcon name="x-mark" />
            </button>
          </div>

          <nav :aria-label="$t('ui.mainNavigation')" class="app-nav__drawer-nav">
            <ul class="app-nav__drawer-list">
              <li v-for="item in menuItems" :key="item.path">
                <router-link
                  :to="item.path"
                  class="app-nav__drawer-link"
                  :class="{ 'app-nav__drawer-link--active': isActive(item.path) }"
                  :aria-current="isActive(item.path) ? 'page' : undefined"
                >
                  <BaseIcon :name="item.icon" class="app-nav__drawer-icon" />
                  <span>{{ $t(item.name) }}</span>
                </router-link>
              </li>
            </ul>
          </nav>

          <div class="app-nav__drawer-foot">
            <BaseButton variant="secondary" icon="logout" block @click="handleLogout">
              {{ $t('common.sair') }}
            </BaseButton>
          </div>
        </div>
      </div>
    </Transition>
  </header>
</template>

<script setup lang="ts">
import { BaseButton, BaseIcon } from '@/shared/components/ui'
import { useNavbarLogic } from './Navbar'

// Usuário e logout vêm do store de auth (ver Navbar.ts)
const {
  user,
  userName,
  userEmail,
  userInitial,
  menuItems,
  isActive,
  isMenuOpen,
  drawerRef,
  toggleMenu,
  closeMenu,
  handleLogout,
} = useNavbarLogic()
</script>

<style scoped>
@import '@/styles/components/navbar.css';
</style>
