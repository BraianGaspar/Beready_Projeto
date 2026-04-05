<template>
  <div class="alert-container-wrapper">
    <Alert
      v-for="alert in alerts"
      :key="alert.id"
      :message="alert.message"
      :type="alert.type"
      :duration="alert.duration"
      @close="removeAlert(alert.id)"
    />
  </div>
</template>

<script setup lang="ts">
import { useAlert } from '@/composables/useAlert'
import Alert from './Alert.vue'

const { alerts, removeAlert } = useAlert()
</script>

<style scoped>
@import '@/styles/components/alert-container.css';
</style>
