<template>
  <div class="card" :class="`card-${variant}`">
    <div v-if="$slots.header" class="card-header">
      <slot name="header" />
    </div>
    <div class="card-body">
      <slot />
    </div>
    <div v-if="$slots.footer" class="card-footer">
      <slot name="footer" />
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps({
  variant: {
    type: String,
    default: 'default',
    validator: (value: string) =>
      ['default', 'primary', 'success', 'danger', 'warning'].includes(value),
  },
})
</script>

<style scoped>
@import '@/styles/components/card.css';
</style>
