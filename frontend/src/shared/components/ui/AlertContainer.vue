<template>
  <!-- Região de notificações (toasts) alimentada por useAlert(); montada uma vez em App.vue.
       < 480px: faixa no topo com 8px de margem; >= 480px: canto superior do lado "fim". -->
  <div
    class="ui-toasts pointer-events-none fixed end-2 start-2 top-2 z-toast w-auto sm:end-4 sm:start-auto sm:top-4 sm:w-toast"
    aria-live="polite"
    aria-relevant="additions"
  >
    <TransitionGroup
      v-bind="toastTransition"
      tag="div"
      class="ui-toasts__list flex flex-col gap-2 *:pointer-events-auto"
    >
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
import { toastTransition } from './transitions'
import { useAlert, type Alert } from '@/shared/composables/useAlert'

const { alerts, removeAlert } = useAlert()

// useAlert usa 'error'; o design system chama o status de 'danger'
const toVariant = (type: Alert['type']): StatusVariant => (type === 'error' ? 'danger' : type)
</script>
