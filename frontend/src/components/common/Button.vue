<template>
  <button
    :type="type"
    :disabled="disabled"
    class="btn"
    :class="[getVariantClass(variant), { 'btn-loading': loading }, { 'btn-block': block }]"
    @click="$emit('click')"
  >
    <svg v-if="loading" class="btn-spinner" viewBox="0 0 24 24">
      <circle class="spinner-circle" cx="12" cy="12" r="10" />
      <path class="spinner-path" d="M12 2a10 10 0 0 1 10 10" />
    </svg>
    <slot />
  </button>
</template>

<script setup lang="ts">
import { useButton } from './Button'

const props = defineProps({
  type: {
    type: String,
    default: 'button',
    validator: (value: string) => ['button', 'submit', 'reset'].includes(value),
  },
  variant: {
    type: String,
    default: 'primary',
    validator: (value: string) =>
      ['primary', 'secondary', 'danger', 'success', 'outline'].includes(value),
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  loading: {
    type: Boolean,
    default: false,
  },
  block: {
    type: Boolean,
    default: false,
  },
})

defineEmits(['click'])

const { getVariantClass } = useButton()
</script>

<style scoped>
@import '@/styles/components/button.css';
</style>
