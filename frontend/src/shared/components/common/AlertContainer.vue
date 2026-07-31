<template>
  <div class="alert-container">
    <AppAlert
      v-for="alert in alerts"
      :key="alert.id"
      :message="alert.message"
      :type="alert.type"
      :duration="alert.duration || 3000"
      @close="removeAlert(alert.id)"
    />
  </div>
</template>

<script setup lang="ts">
import { useAlert } from '@/shared/composables/useAlert'
import AppAlert from './AppAlert.vue'

const { alerts, removeAlert } = useAlert()
</script>

<style scoped>
@import '@/styles/components/alert-container.css';
</style>