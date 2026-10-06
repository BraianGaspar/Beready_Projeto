<template>
  <!-- Região de notificações (toasts) alimentada por useAlert(); montada uma vez em App.vue -->
  <div class="ui-toasts" aria-live="polite" aria-relevant="additions">
    <TransitionGroup name="ui-toast" tag="div" class="ui-toasts__list">
      <BaseAlert
        v-for="alert in alerts"
        :key="alert.id"
        :variant="toVariant(alert.type)"
        :message="alert.message"
        :duration="alert.duration"
        toast
        dismissible
        @close="removeAlert(alert.id)"
      />
    </TransitionGroup>
  </div>
</template>

<script setup lang="ts">
import BaseAlert from './BaseAlert.vue'
import type { StatusVariant } from './icons'
import { useAlert, type Alert } from '@/shared/composables/useAlert'

const { alerts, removeAlert } = useAlert()

// useAlert usa 'error'; o design system chama o status de 'danger'
const toVariant = (type: Alert['type']): StatusVariant => (type === 'error' ? 'danger' : type)
</script>

<style scoped>
.ui-toasts {
  position: fixed;
  inset-block-start: var(--space-4);
  inset-inline-end: var(--space-4);
  z-index: var(--z-toast);
  width: min(26rem, calc(100vw - var(--space-8)));
  pointer-events: none;
}

.ui-toasts__list {
  display: flex;
  flex-direction: column;
  gap: var(--space-2);
}

.ui-toasts__list > * {
  pointer-events: auto;
}

@media (max-width: 479.98px) {
  .ui-toasts {
    inset-block-start: var(--space-2);
    inset-inline: var(--space-2);
    width: auto;
  }
}

.ui-toast-enter-active,
.ui-toast-leave-active {
  transition:
    opacity var(--duration-slow) var(--easing-standard),
    transform var(--duration-slow) var(--easing-standard);
}

.ui-toast-enter-from,
.ui-toast-leave-to {
  opacity: 0;
  transform: translateY(-0.5rem) scale(0.97);
}
</style>
