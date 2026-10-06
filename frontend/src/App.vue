<template>
  <router-view />
  <AlertContainer />
</template>

<script setup lang="ts">
import AlertContainer from '@/shared/components/ui/AlertContainer.vue'
import { useTheme } from '@/shared/composables/useTheme'

// Aplica o tema (claro/escuro/daltônico) conforme rota, sistema e preferências
useTheme()
</script>
