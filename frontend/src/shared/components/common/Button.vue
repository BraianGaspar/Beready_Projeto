<template>
  <button
    :type="type"
    :disabled="disabled"
    class="btn"
    :class="[variant, { 'btn-loading': loading }, { 'btn-block': block }]"
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
defineProps<{
  type?: 'button' | 'submit' | 'reset'
  variant?: 'primary' | 'secondary' | 'danger' | 'success' | 'outline'
  disabled?: boolean
  loading?: boolean
  block?: boolean
}>()

defineEmits<{
  (e: 'click'): void
}>()
</script>

<style scoped>
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.625rem 1.25rem;
  border: none;
  border-radius: 0.5rem;
  font-weight: 600;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-block {
  width: 100%;
}

.btn.primary {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
}

.btn.primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn.secondary {
  background: #e2e8f0;
  color: #4a5568;
}

.btn.secondary:hover:not(:disabled) {
  background: #cbd5e0;
}

.btn.outline {
  background: transparent;
  border: 1px solid #667eea;
  color: #667eea;
}

.btn.outline:hover:not(:disabled) {
  background: #667eea;
  color: white;
}

.btn.danger {
  background: #e53e3e;
  color: white;
}

.btn.danger:hover:not(:disabled) {
  background: #c53030;
}

.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-loading {
  cursor: wait;
}

.btn-spinner {
  width: 16px;
  height: 16px;
  animation: spin 0.8s linear infinite;
}

.spinner-circle {
  fill: none;
  stroke: rgba(255, 255, 255, 0.3);
  stroke-width: 2;
}

.spinner-path {
  fill: none;
  stroke: white;
  stroke-width: 2;
  stroke-linecap: round;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
</style>
