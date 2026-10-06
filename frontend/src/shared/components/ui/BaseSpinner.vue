<template>
  <span
    class="ui-spinner"
    :class="[`ui-spinner--${size}`, { 'ui-spinner--center': center }]"
    role="status"
    aria-live="polite"
  >
    <span class="ui-spinner__ring motion-safe" aria-hidden="true"></span>
    <span :class="showLabel ? 'ui-spinner__label' : 'sr-only'">{{ label || t('common.carregando') }}</span>
  </span>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n'

withDefaults(
  defineProps<{
    size?: 'sm' | 'md' | 'lg'
    /** Texto lido por leitores de tela (padrão: common.carregando) */
    label?: string
    /** Mostra o texto ao lado/abaixo do indicador */
    showLabel?: boolean
    /** Ocupa a largura toda, centralizado, com respiro vertical (carregamento de página/seção) */
    center?: boolean
  }>(),
  { size: 'md', label: '', showLabel: false, center: false },
)

const { t } = useI18n()
</script>

<style scoped>
.ui-spinner {
  display: inline-flex;
  align-items: center;
  gap: var(--space-3);
  color: var(--color-text-muted);
  font-size: var(--font-size-sm);
}

.ui-spinner--center {
  display: flex;
  flex-direction: column;
  justify-content: center;
  width: 100%;
  padding: var(--space-12) var(--space-4);
}

.ui-spinner__ring {
  --ui-spinner-size: 1.5rem;
  width: var(--ui-spinner-size);
  height: var(--ui-spinner-size);
  border: 3px solid var(--color-border);
  border-top-color: var(--color-primary);
  border-radius: var(--radius-full);
  animation: ui-spin 0.8s linear infinite;
  flex-shrink: 0;
}

.ui-spinner--sm .ui-spinner__ring {
  --ui-spinner-size: 1rem;
  border-width: 2px;
}

.ui-spinner--lg .ui-spinner__ring {
  --ui-spinner-size: 2.75rem;
  border-width: 4px;
}

.ui-spinner__label {
  color: var(--color-text-muted);
}

@keyframes ui-spin {
  to {
    transform: rotate(360deg);
  }
}
</style>
