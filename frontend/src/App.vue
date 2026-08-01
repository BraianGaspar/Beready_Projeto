<template>
  <div id="app">
    <router-view />
    <AlertContainer />
  </div>
</template>

<script setup lang="ts">
import AlertContainer from '@/shared/components/common/AlertContainer.vue'
import { useTheme } from '@/shared/composables/useTheme'

// Inicializa a lógica de Tema e Preferências (CSS e TS foram movidos para lá)
useTheme()
</script>

<style>
/* Importa o CSS global que criamos no passo 1 */
@import '@/assets/main.css';
</style>