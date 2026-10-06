<template>
  <div class="app-layout flex min-h-viewport flex-col bg-bg">
    <a href="#conteudo" class="skip-link sr-only z-tooltip rounded-md bg-primary font-semibold text-primary-contrast no-underline hover:text-primary-contrast focus:not-sr-only focus:fixed focus:px-4 focus:py-2 focus:start-2 focus:top-2 focus:text-primary-contrast" @click.prevent="focusMain">{{ $t('ui.skipToContent') }}</a>
    <AppNavbar />
    <!-- Cada tela renderiza aqui; telas não devem usar outro <main> -->
    <main id="conteudo" ref="mainRef" class="app-layout__main min-w-0 flex-1 outline-hidden" tabindex="-1">
      <router-view />
    </main>
  </div>
</template>

<script setup lang="ts">
// Layout das páginas autenticadas: link "pular para o conteúdo", navbar e <main>
import { ref } from 'vue'
import AppNavbar from '@/components/layout/AppNavbar.vue'

const mainRef = ref<HTMLElement | null>(null)

const focusMain = () => {
  mainRef.value?.focus()
}
</script>
