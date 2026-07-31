<template>
  <div class="input-group">
    <label v-if="label" class="input-label">{{ label }}</label>
    <div class="select-container" :class="{ 'has-error': error }">
      <select
        :value="modelValue"
        @change="$emit('update:modelValue', ($event.target as HTMLSelectElement).value)"
        class="select-field"
      >
        <option v-if="placeholder" value="" disabled selected>{{ placeholder }}</option>
        <option v-for="option in options" :key="option.value" :value="option.value">
          {{ option.label }}
        </option>
      </select>
      <svg
        class="select-arrow"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </div>
    <span v-if="error" class="input-error">{{ error }}</span>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  modelValue: string | number
  label?: string
  placeholder?: string
  options: Array<{ value: string | number; label: string }>
  error?: string
}>()

defineEmits<{
  (e: 'update:modelValue', value: string | number): void
}>()
</script>

<style scoped>
@import '@/styles/components/select.css';
</style>