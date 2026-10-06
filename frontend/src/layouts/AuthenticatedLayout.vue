<template>
  <div class="app-layout">
    <a href="#conteudo" class="skip-link" @click.prevent="focusMain">{{ $t('ui.skipToContent') }}</a>
    <AppNavbar />
    <!-- Cada tela renderiza aqui; telas não devem usar outro <main> -->
    <main id="conteudo" ref="mainRef" class="app-layout__main" tabindex="-1">
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

<style scoped>
.app-layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  min-height: 100dvh;
  background: var(--color-bg);
}

.app-layout__main {
  flex: 1;
  min-width: 0;
  outline: none;
}
</style>
