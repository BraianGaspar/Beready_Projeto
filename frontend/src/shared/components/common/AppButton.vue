<template>
  <button :class="['btn', variant]" :disabled="loading" @click="$emit('click')">
    <span v-if="loading" class="btn-spinner"></span>
    <slot />
  </button>
</template>

<script setup lang="ts">
defineProps<{
  variant?: 'primary' | 'secondary' | 'outline' | 'danger'
  loading?: boolean
}>()

defineEmits<{
  (e: 'click'): void
}>()
</script>

<style scoped>
@import '@/styles/components/button.css';
</style>