<template>
  <!-- < 1280px: logo + avatar + botão de menu (gaveta). >= 1280px: menu inline. >= 1536px: ícones e nome/e-mail -->
  <header class="app-nav sticky top-0 z-sticky w-full border-0 border-b border-solid border-border bg-surface shadow-sm">
    <div class="app-nav__bar mx-auto flex h-navbar max-w-page-xl items-center gap-3 px-gutter">
      <router-link to="/dashboard" class="app-nav__brand inline-flex shrink-0 items-center rounded-md">
        <img src="/logo.png" alt="BeReady" class="app-nav__logo block h-10 w-auto max-w-34 object-contain md:h-12 md:max-w-40" />
      </router-link>

      <!-- Menu inline (>= 1280px) -->
      <nav class="app-nav__desktop hidden min-w-0 flex-1 xl:block" :aria-label="$t('ui.mainNavigation')">
        <ul class="app-nav__list scrollbar-thin flex list-none items-center gap-1 overflow-x-auto py-1">
          <li v-for="item in menuItems" :key="item.path">
            <router-link
              :to="item.path"
              :class="[linkBase, isActive(item.path) ? linkActive : linkIdle]"
              :aria-current="isActive(item.path) ? 'page' : undefined"
            >
              <BaseIcon :name="item.icon" class="app-nav__link-icon hidden size-4.5 2xl:inline-block" />
              <span>{{ $t(item.name) }}</span>
            </router-link>
          </li>
        </ul>
      </nav>

      <div class="app-nav__user ms-auto flex min-w-0 items-center gap-2">
        <span :class="avatarClass" aria-hidden="true">
          <img v-if="user?.foto_perfil" :src="user.foto_perfil" alt="" class="app-nav__avatar-img size-full object-cover" />
          <span v-else>{{ userInitial }}</span>
        </span>
        <span class="app-nav__user-text hidden min-w-0 max-w-44 flex-col 2xl:flex">
          <span :class="nameClass">{{ userName }}</span>
          <span :class="emailClass">{{ userEmail }}</span>
        </span>

        <button
          type="button"
          class="app-nav__logout hidden min-h-control-sm items-center gap-2 whitespace-nowrap rounded-md border border-solid border-border bg-surface px-3 py-2 text-sm font-semibold text-danger transition-colors hover:bg-danger-soft xl:inline-flex"
          @click="handleLogout"
        >
          <BaseIcon name="logout" class="size-4.5" />
          <span>{{ $t('common.sair') }}</span>
        </button>

        <!-- Abre a gaveta (< 1280px) -->
        <button
          type="button"
          :class="toggleClass"
          :aria-expanded="isMenuOpen"
          aria-controls="app-nav-drawer"
          :aria-label="isMenuOpen ? $t('ui.closeMenu') : $t('ui.openMenu')"
          @click="toggleMenu"
        >
          <BaseIcon :name="isMenuOpen ? 'x-mark' : 'menu'" />
        </button>
      </div>
    </div>

    <!-- Gaveta (menu mobile/tablet): desliza do lado "fim" (direita em LTR, esquerda em RTL) -->
    <Transition v-bind="overlayTransition">
      <div v-if="isMenuOpen" class="app-nav__overlay fixed inset-0 z-drawer bg-overlay" @click.self="closeMenu">
        <div
          id="app-nav-drawer"
          ref="drawerRef"
          class="app-nav__drawer outline-hidden absolute inset-y-0 end-0 flex w-drawer flex-col bg-surface text-text shadow-xl t-active:transition-transform t-active:duration-slow t-from:translate-x-full rtl:t-from:-translate-x-full"
          role="dialog"
          aria-modal="true"
          :aria-label="$t('ui.mainNavigation')"
          tabindex="-1"
        >
          <div class="app-nav__drawer-head flex items-center gap-3 border-0 border-b border-solid border-border px-4 py-3">
            <span :class="avatarClass" aria-hidden="true">
              <img v-if="user?.foto_perfil" :src="user.foto_perfil" alt="" class="app-nav__avatar-img size-full object-cover" />
              <span v-else>{{ userInitial }}</span>
            </span>
            <span class="app-nav__drawer-user flex min-w-0 flex-1 flex-col">
              <span :class="nameClass">{{ userName }}</span>
              <span :class="emailClass">{{ userEmail }}</span>
            </span>
            <button
              type="button"
              :class="[toggleClass, 'app-nav__drawer-close ms-auto']"
              :aria-label="$t('ui.closeMenu')"
              @click="closeMenu"
            >
              <BaseIcon name="x-mark" />
            </button>
          </div>

          <nav :aria-label="$t('ui.mainNavigation')" class="app-nav__drawer-nav flex-1 overflow-y-auto p-3">
            <ul class="app-nav__drawer-list flex list-none flex-col gap-1">
              <li v-for="item in menuItems" :key="item.path">
                <router-link
                  :to="item.path"
                  :class="[drawerLinkBase, isActive(item.path) ? drawerLinkActive : drawerLinkIdle]"
                  :aria-current="isActive(item.path) ? 'page' : undefined"
                >
                  <BaseIcon :name="item.icon" class="app-nav__drawer-icon size-5" />
                  <span>{{ $t(item.name) }}</span>
                </router-link>
              </li>
            </ul>
          </nav>

          <div class="app-nav__drawer-foot border-0 border-t border-solid border-border p-4">
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
import { overlayTransition } from '@/shared/components/ui/transitions'
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

// Classes repetidas (barra e gaveta). Ativo: cor + fundo + barra (não depende só de cor);
// `hover:text-…` repetido para vencer `a:hover` do reset base.
const avatarClass =
  'app-nav__avatar inline-flex size-9 shrink-0 items-center justify-center overflow-hidden rounded-full bg-primary text-sm font-semibold text-primary-contrast'
const nameClass = 'app-nav__user-name min-w-0 truncate text-sm font-semibold text-text'
const emailClass = 'app-nav__user-email min-w-0 truncate text-xs text-text-muted'
const toggleClass =
  'app-nav__toggle inline-flex size-control shrink-0 items-center justify-center rounded-md border-0 bg-transparent text-2xl text-text hover:bg-surface-hover xl:hidden'

const linkBase =
  'app-nav__link inline-flex min-h-control-sm items-center gap-2 whitespace-nowrap rounded-md px-3 py-2 text-sm no-underline transition-colors'
const linkActive =
  'app-nav__link--active bg-primary-soft font-semibold text-primary-soft-text shadow-nav-active hover:text-primary-soft-text'
const linkIdle = 'font-medium text-text-muted hover:bg-surface-hover hover:text-text'

const drawerLinkBase = 'app-nav__drawer-link flex min-h-control items-center gap-3 rounded-md px-3 py-2 no-underline'
const drawerLinkActive =
  'app-nav__drawer-link--active bg-primary-soft font-semibold text-primary-soft-text shadow-drawer-active hover:text-primary-soft-text rtl:shadow-drawer-active-rtl'
const drawerLinkIdle = 'font-medium text-text hover:bg-surface-hover hover:text-text'
</script>
