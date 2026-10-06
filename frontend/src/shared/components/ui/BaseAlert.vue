<template>
  <div
    class="ui-alert"
    :class="[`ui-alert--${variant}`, { 'ui-alert--toast': toast }]"
    :role="variant === 'danger' ? 'alert' : 'status'"
  >
    <BaseIcon :name="statusIcons[variant]" class="ui-alert__icon" />
    <div class="ui-alert__content">
      <!-- Rótulo textual do status: a informação não depende só da cor -->
      <p class="ui-alert__title">
        {{ title || t(`ui.status.${variant}`) }}
      </p>
      <div class="ui-alert__message">
        <slot>{{ message }}</slot>
      </div>
    </div>
    <button
      v-if="dismissible"
      type="button"
      class="ui-alert__close"
      :aria-label="t('ui.closeNotification')"
      @click="emit('close')"
    >
      <BaseIcon name="x-mark" />
    </button>
    <span
      v-if="toast && duration > 0"
      class="ui-alert__progress"
      :style="{ animationDuration: `${duration}ms` }"
      aria-hidden="true"
    ></span>
  </div>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import BaseIcon from './BaseIcon.vue'
import { statusIcons, type StatusVariant } from './icons'

withDefaults(
  defineProps<{
    variant?: StatusVariant
    /** Título; padrão = rótulo do status traduzido (ui.status.*) */
    title?: string
    message?: string
    dismissible?: boolean
    /** Estilo de notificação flutuante (sombra + barra de tempo) */
    toast?: boolean
    /** Duração em ms (só para a barra de progresso do toast) */
    duration?: number
  }>(),
  { variant: 'info', title: '', message: '', dismissible: false, toast: false, duration: 0 },
)

const emit = defineEmits<{ close: [] }>()
defineSlots<{ default?: () => unknown }>()

const { t } = useI18n()
</script>

<style scoped>
.ui-alert {
  --ui-alert-color: var(--color-info);
  --ui-alert-bg: var(--color-info-soft);
  position: relative;
  display: flex;
  align-items: flex-start;
  gap: var(--space-3);
  width: 100%;
  padding: var(--space-3) var(--space-4);
  border: var(--border-width) solid var(--color-border);
  border-inline-start: 4px solid var(--ui-alert-color);
  border-radius: var(--radius-lg);
  background: var(--ui-alert-bg);
  color: var(--color-text);
  overflow: hidden;
}

.ui-alert--success {
  --ui-alert-color: var(--color-success);
  --ui-alert-bg: var(--color-success-soft);
}

.ui-alert--warning {
  --ui-alert-color: var(--color-warning);
  --ui-alert-bg: var(--color-warning-soft);
}

.ui-alert--danger {
  --ui-alert-color: var(--color-danger);
  --ui-alert-bg: var(--color-danger-soft);
}

.ui-alert--toast {
  background: var(--color-surface);
  box-shadow: var(--shadow-lg);
}

.ui-alert__icon {
  width: 1.25rem;
  height: 1.25rem;
  margin-block-start: 0.1rem;
  color: var(--ui-alert-color);
}

.ui-alert__content {
  flex: 1;
  min-width: 0;
}

.ui-alert__title {
  font-size: var(--font-size-sm);
  font-weight: var(--font-weight-bold);
  color: var(--ui-alert-color);
}

.ui-alert__message {
  font-size: var(--font-size-sm);
  color: var(--color-text);
  overflow-wrap: anywhere;
}

.ui-alert__close {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  width: 2rem;
  height: 2rem;
  margin: calc(var(--space-1) * -1);
  border: none;
  border-radius: var(--radius-md);
  background: transparent;
  color: var(--color-text-muted);
  font-size: 1rem;
}

.ui-alert__close:hover {
  background: var(--color-surface-hover);
  color: var(--color-text);
}

.ui-alert__progress {
  position: absolute;
  inset-block-end: 0;
  inset-inline-start: 0;
  height: 3px;
  width: 100%;
  background: var(--ui-alert-color);
  opacity: 0.6;
  transform-origin: left;
  animation: ui-alert-progress linear forwards;
}

[dir='rtl'] .ui-alert__progress {
  transform-origin: right;
}

@keyframes ui-alert-progress {
  from {
    transform: scaleX(1);
  }
  to {
    transform: scaleX(0);
  }
}
</style>
