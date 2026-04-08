<template>
  <div id="app">
    <router-view />
    <AlertContainer />
  </div>
</template>

<script setup lang="ts">
import AlertContainer from '@/shared/components/common/AlertContainer.vue'
</script>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html,
body,
#app {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

body {
  font-family:
    'Inter',
    -apple-system,
    BlinkMacSystemFont,
    'Segoe UI',
    Roboto,
    'Helvetica Neue',
    Arial,
    sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

/* Garantir que ícones SVG tenham tamanho consistente */
svg {
  width: 1em;
  height: 1em;
  flex-shrink: 0;
}

button svg {
  pointer-events: none;
}
</style>
